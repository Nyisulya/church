<?php

namespace App\Http\Controllers;

use App\Models\SmallGroupQuestion;
use App\Models\SmallGroupResponse;
use App\Models\SmallGroup;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmallGroupResponseController extends Controller
{
    /**
     * Display member's report history
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        // Get all weeks the member has submitted reports
        $weeklyReports = SmallGroupResponse::forMember($user->member->id)
            ->select('week_starting')
            ->groupBy('week_starting')
            ->orderBy('week_starting', 'desc')
            ->get();

        // Calculate some statistics
        $stats = $this->calculateMemberStats($user->member->id);

        // Check if user is a small group leader
        $isLeader = SmallGroup::where('leader_id', $user->member->id)->exists();

        // Check if user has admin privileges
        $isAdmin = $user->hasAnyRole(['super_admin', 'admin', 'pastor']);

        return view('weekly-reports.index', compact('weeklyReports', 'stats', 'isLeader', 'isAdmin'));
    }

    /**
     * Show the form for creating weekly report
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();
        $weekRange = SmallGroupResponse::formatWeekRange($currentWeek);

        // Check if already submitted this week
        $existingSubmission = SmallGroupResponse::forMember($user->member->id)
            ->forWeek($currentWeek)
            ->exists();

        if ($existingSubmission) {
            return redirect()->route('weekly-reports.edit', ['weekStart' => $currentWeek->format('Y-m-d')])
                ->with('info', 'Umeshawasilisha ripoti ya wiki hii. Unaweza kuihariri hapa chini.');
        }

        // Get active questions
        $questions = SmallGroupQuestion::active()->ordered()->get();

        // Get all active small groups
        $smallGroups = SmallGroup::where('status', 'active')->orderBy('name')->get();
        $defaultGroup = $user->member->smallGroups()->first();

        return view('weekly-reports.create', compact('questions', 'currentWeek', 'weekRange', 'smallGroups', 'defaultGroup'));
    }

    /**
     * Store weekly report responses
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();

        // Validate responses
        $validated = $request->validate([
            'small_group_id' => 'nullable|exists:small_groups,id',
            'responses' => 'required|array',
            'responses.*' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['responses'] as $questionId => $responseValue) {
                SmallGroupResponse::updateOrCreate(
                    [
                        'member_id' => $user->member->id,
                        'question_id' => $questionId,
                        'week_starting' => $currentWeek,
                    ],
                    [
                        'small_group_id' => $validated['small_group_id'] ?? null,
                        'response_value' => $responseValue,
                        'submitted_at' => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('weekly-reports.index')
                ->with('success', 'Ripoti ya Wiki imewasilishwa kikamilifu!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Imeshindwa kuwasilisha ripoti. Tafadhali jaribu tena.');
        }
    }

    /**
     * Show form to edit existing weekly report
     */
    public function edit($weekStart)
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        $weekStartDate = Carbon::parse($weekStart);
        $weekRange = SmallGroupResponse::formatWeekRange($weekStartDate);

        // Get all active questions
        $questions = SmallGroupQuestion::active()->ordered()->get();

        // Get existing responses for this week
        $existingResponses = SmallGroupResponse::forMember($user->member->id)
            ->forWeek($weekStartDate)
            ->get()
            ->keyBy('question_id');

        // Get all active small groups
        $smallGroups = SmallGroup::where('status', 'active')->orderBy('name')->get();
        $firstResponse = $existingResponses->first();
        $selectedGroupId = $firstResponse ? $firstResponse->small_group_id : null;

        return view('weekly-reports.edit', compact('questions', 'weekStartDate', 'weekRange', 'smallGroups', 'selectedGroupId', 'existingResponses'));
    }

    /**
     * Update weekly report
     */
    public function update(Request $request, $weekStart)
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        $weekStartDate = Carbon::parse($weekStart);

        // Validate responses
        $validated = $request->validate([
            'small_group_id' => 'nullable|exists:small_groups,id',
            'responses' => 'required|array',
            'responses.*' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['responses'] as $questionId => $responseValue) {
                SmallGroupResponse::updateOrCreate(
                    [
                        'member_id' => $user->member->id,
                        'question_id' => $questionId,
                        'week_starting' => $weekStartDate,
                    ],
                    [
                        'small_group_id' => $validated['small_group_id'] ?? null,
                        'response_value' => $responseValue,
                        'submitted_at' => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('weekly-reports.index')
                ->with('success', 'Ripoti ya Wiki imesasishwa kikamilifu!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Imeshindwa kusasisha ripoti. Tafadhali jaribu tena.');
        }
    }

    /**
     * Group weekly reporting for Leaders/Admin
     */
    public function createGroupReport(Request $request)
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        // Determine which groups they can report for
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor'])) {
            $smallGroups = SmallGroup::where('status', 'active')->orderBy('name')->get();
        } else {
            $smallGroups = SmallGroup::where('leader_id', $user->member->id)->get();
        }

        if ($smallGroups->isEmpty()) {
            return redirect()->route('weekly-reports.index')->with('error', 'Huna ruhusa ya kuwasilisha ripoti ya kanda.');
        }

        $selectedGroupId = $request->get('small_group_id', $smallGroups->first()->id);
        $selectedGroup = SmallGroup::findOrFail($selectedGroupId);

        // Security check for non-admins
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor']) && $selectedGroup->leader_id !== $user->member->id) {
            return redirect()->route('weekly-reports.index')->with('error', 'Huna ruhusa ya kanda hii.');
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();
        $weekRange = SmallGroupResponse::formatWeekRange($currentWeek);

        // Check if group report already submitted
        $existingResponses = SmallGroupResponse::whereNull('member_id')
            ->where('small_group_id', $selectedGroupId)
            ->where('week_starting', $currentWeek)
            ->get()
            ->keyBy('question_id');

        $questions = SmallGroupQuestion::active()->ordered()->get();

        return view('weekly-reports.group-create', compact('questions', 'currentWeek', 'weekRange', 'smallGroups', 'selectedGroup', 'existingResponses'));
    }

    /**
     * Store group weekly report
     */
    public function storeGroupReport(Request $request)
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        $validated = $request->validate([
            'small_group_id' => 'required|exists:small_groups,id',
            'responses' => 'required|array',
            'responses.*' => 'nullable',
        ]);

        $smallGroup = SmallGroup::findOrFail($validated['small_group_id']);

        // Check authorization
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor']) && $smallGroup->leader_id !== $user->member->id) {
            return abort(403);
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();

        DB::beginTransaction();
        try {
            foreach ($validated['responses'] as $questionId => $responseValue) {
                SmallGroupResponse::updateOrCreate(
                    [
                        'member_id' => null, // null means group report
                        'small_group_id' => $smallGroup->id,
                        'question_id' => $questionId,
                        'week_starting' => $currentWeek,
                    ],
                    [
                        'response_value' => $responseValue,
                        'submitted_at' => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('weekly-reports.index')
                ->with('success', 'Ripoti ya Kanda imehifadhiwa kikamilifu!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Imeshindwa kuhifadhi ripoti. Tafadhali jaribu tena.');
        }
    }

    /**
     * Leader dashboard to view group submissions
     */
    public function leaderDashboard()
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        // Get group where user is leader
        $smallGroup = SmallGroup::where('leader_id', $user->member->id)->first();
        if (!$smallGroup) {
            return redirect()->route('weekly-reports.index')->with('error', 'Huna ruhusa ya kuona ukurasa huu.');
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();
        $weekRange = SmallGroupResponse::formatWeekRange($currentWeek);

        // Load group members
        $smallGroup->load('members');

        // Get submission status for each member (only individual submissions)
        $members = $smallGroup->members->map(function ($member) use ($currentWeek) {
            $hasSubmitted = SmallGroupResponse::forMember($member->id)
                ->forWeek($currentWeek)
                ->exists();

            $submittedAt = null;
            if ($hasSubmitted) {
                $submittedAt = SmallGroupResponse::forMember($member->id)
                    ->forWeek($currentWeek)
                    ->first()
                    ->submitted_at;
            }

            return [
                'member' => $member,
                'has_submitted' => $hasSubmitted,
                'submitted_at' => $submittedAt,
            ];
        });

        // Get group-level report submission status
        $groupReportSubmitted = SmallGroupResponse::whereNull('member_id')
            ->where('small_group_id', $smallGroup->id)
            ->where('week_starting', $currentWeek)
            ->exists();

        // Calculate stats
        $groupStats = $this->calculateGroupStats($smallGroup->id, $currentWeek);

        $offerings = $smallGroup->offerings()->where('is_active', true)->get();

        return view('weekly-reports.leader-dashboard', compact('smallGroup', 'members', 'currentWeek', 'weekRange', 'groupStats', 'offerings', 'groupReportSubmitted'));
    }

    /**
     * Admin dashboard for all groups
     */
    public function adminDashboard()
    {
        $currentWeek = SmallGroupResponse::getCurrentWeekStart();
        $weekRange = SmallGroupResponse::formatWeekRange($currentWeek);

        // Get all groups with submission rates
        $groups = SmallGroup::where('status', 'active')
            ->with('members')
            ->get()
            ->map(function ($group) use ($currentWeek) {
                // Check if group-level report exists
                $hasGroupReport = SmallGroupResponse::whereNull('member_id')
                    ->where('small_group_id', $group->id)
                    ->forWeek($currentWeek)
                    ->exists();

                $totalMembers = $group->members->count();
                $submitted = SmallGroupResponse::where('small_group_id', $group->id)
                    ->whereNotNull('member_id')
                    ->forWeek($currentWeek)
                    ->distinct('member_id')
                    ->count('member_id');

                return [
                    'group' => $group,
                    'total_members' => $totalMembers,
                    'submitted_count' => $submitted,
                    'has_group_report' => $hasGroupReport,
                    'participation_rate' => $totalMembers > 0 ? round(($submitted / $totalMembers) * 100) : 0,
                ];
            });

        // Church-wide statistics
        $churchStats = $this->calculateChurchStats($currentWeek);

        return view('weekly-reports.admin-dashboard', compact('groups', 'currentWeek', 'weekRange', 'churchStats'));
    }

    /**
     * Calculate member statistics
     */
    private function calculateMemberStats($memberId)
    {
        $responses = SmallGroupResponse::forMember($memberId)->get();
        $totalWeeks = $responses->pluck('week_starting')->unique()->count();

        $stats = [
            'total_weeks_submitted' => $totalWeeks,
            'total_evangelism_visits' => 0,
            'total_community_help' => 0,
            'bible_reading_weeks' => 0,
            'lesson_reading_weeks' => 0,
        ];

        foreach ($responses as $response) {
            if ($response->question && $response->question->category === 'evangelism') {
                $stats['total_evangelism_visits'] += (int)$response->response_value;
            }
        }

        return $stats;
    }

    /**
     * Calculate group statistics for a week
     */
    private function calculateGroupStats($groupId, $weekStart)
    {
        $groupReportResponses = SmallGroupResponse::whereNull('member_id')
            ->where('small_group_id', $groupId)
            ->where('week_starting', $weekStart)
            ->with('question')
            ->get();

        if ($groupReportResponses->isNotEmpty()) {
            $responses = $groupReportResponses;
        } else {
            $responses = SmallGroupResponse::whereNotNull('member_id')
                ->where('small_group_id', $groupId)
                ->where('week_starting', $weekStart)
                ->with('question')
                ->get();
        }

        $stats = [
            'total_evangelism_visits' => 0,
            'total_community_help' => 0,
            'members_read_bible' => 0,
            'members_read_lesson' => 0,
        ];

        $questionsById = SmallGroupQuestion::all()->keyBy('id');

        foreach ($responses as $response) {
            $question = $questionsById->get($response->question_id);
            if (!$question) continue;

            $val = (int)$response->response_value;

            if (str_contains($question->question_en, 'evangelism visits')) {
                $stats['total_evangelism_visits'] += $val;
            } elseif (str_contains($question->question_en, 'help the community')) {
                $stats['total_community_help'] += $val;
            } elseif (str_contains($question->question_en, 'Bible according to plan')) {
                $stats['members_read_bible'] += $val;
            } elseif (str_contains($question->question_en, 'lesson according to plan')) {
                $stats['members_read_lesson'] += $val;
            }
        }

        return $stats;
    }

    /**
     * Calculate church-wide statistics
     */
    private function calculateChurchStats($weekStart)
    {
        $groupResponses = SmallGroupResponse::whereNull('member_id')
            ->where('week_starting', $weekStart)
            ->get();
            
        $individualResponses = SmallGroupResponse::whereNotNull('member_id')
            ->whereNull('small_group_id')
            ->where('week_starting', $weekStart)
            ->get();
            
        $responses = $groupResponses->merge($individualResponses);

        $stats = [
            'total_submissions' => $groupResponses->pluck('small_group_id')->filter()->unique()->count() + $individualResponses->pluck('member_id')->unique()->count(),
            'total_members' => Member::count(),
            'participation_rate' => 0,
            'total_evangelism_visits' => 0,
            'total_community_help' => 0,
        ];

        if ($stats['total_members'] > 0) {
            $stats['participation_rate'] = round(($stats['total_submissions'] / $stats['total_members']) * 100);
        }

        $questionsById = SmallGroupQuestion::all()->keyBy('id');

        foreach ($responses as $response) {
            $question = $questionsById->get($response->question_id);
            if (!$question) continue;

            $val = (int)$response->response_value;

            if (str_contains($question->question_en, 'evangelism visits')) {
                $stats['total_evangelism_visits'] += $val;
            } elseif (str_contains($question->question_en, 'help the community')) {
                $stats['total_community_help'] += $val;
            }
        }

        return $stats;
    }
}
