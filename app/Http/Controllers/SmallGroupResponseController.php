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

        return view('small-groups.reports.index', compact('weeklyReports', 'stats'));
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

        // Get member's small group
        $smallGroup = $user->member->smallGroups()->first();
        if (!$smallGroup) {
            return redirect()->route('small-groups.my-group')
                ->with('warning', 'You must be part of a small group to submit reports.');
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();
        $weekRange = SmallGroupResponse::formatWeekRange($currentWeek);

        // Check if already submitted this week
        $existingSubmission = SmallGroupResponse::forMember($user->member->id)
            ->forWeek($currentWeek)
            ->exists();

        if ($existingSubmission) {
            return redirect()->route('small-groups.reports.edit', ['weekStart' => $currentWeek->format('Y-m-d')])
                ->with('info', 'You have already submitted a report for this week. You can edit it below.');
        }

        // Get active questions
        $questions = SmallGroupQuestion::active()->ordered()->get();

        return view('small-groups.reports.create', compact('questions', 'currentWeek', 'weekRange', 'smallGroup'));
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

        $smallGroup = $user->member->smallGroups()->first();
        if (!$smallGroup) {
            return redirect()->route('small-groups.my-group')
                ->with('warning', 'You must be part of a small group to submit reports.');
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();

        // Validate responses
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['responses'] as $questionId => $responseValue) {
                SmallGroupResponse::updateOrCreate(
                    [
                        'member_id' => $user->member->id,
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
            return redirect()->route('small-groups.my-group')
                ->with('success', 'Weekly report submitted successfully! Asante sana!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit report. Please try again.');
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

        $smallGroup = $user->member->smallGroups()->first();
        if (!$smallGroup) {
            return redirect()->route('small-groups.my-group')
                ->with('warning', 'You must be part of a small group.');
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

        return view('small-groups.reports.edit', compact('questions', 'weekStartDate', 'weekRange', 'smallGroup', 'existingResponses'));
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

        $smallGroup = $user->member->smallGroups()->first();
        $weekStartDate = Carbon::parse($weekStart);

        // Validate responses
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['responses'] as $questionId => $responseValue) {
                SmallGroupResponse::updateOrCreate(
                    [
                        'member_id' => $user->member->id,
                        'small_group_id' => $smallGroup->id,
                        'question_id' => $questionId,
                        'week_starting' => $weekStartDate,
                    ],
                    [
                        'response_value' => $responseValue,
                        'submitted_at' => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('small-groups.reports.index')
                ->with('success', 'Weekly report updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update report. Please try again.');
        }
    }

    /**
     * Leader dashboard to view group submissions
     */
    public function leaderDashboard($groupId)
    {
        $user = Auth::user();
        $smallGroup = SmallGroup::with(['leader', 'members'])->findOrFail($groupId);

        // Check if user is the leader or co-leader
        if ($smallGroup->leader_id !== $user->member->id) {
            $isCoLeader = $smallGroup->members()
                ->where('member_id', $user->member->id)
                ->wherePivot('role', 'co-leader')
                ->exists();

            if (!$isCoLeader) {
                return redirect()->route('small-groups.my-group')
                    ->with('error', 'You do not have permission to view this dashboard.');
            }
        }

        $currentWeek = SmallGroupResponse::getCurrentWeekStart();
        $weekRange = SmallGroupResponse::formatWeekRange($currentWeek);

        // Get submission status for each member
        $members = $smallGroup->members->map(function ($member) use ($currentWeek, $smallGroup) {
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

        // Calculate group statistics for current week
        $groupStats = $this->calculateGroupStats($smallGroup->id, $currentWeek);

        // [NEW] Fetch active offerings
        $offerings = $smallGroup->offerings()->where('is_active', true)->get();

        return view('small-groups.reports.leader-dashboard', compact('smallGroup', 'members', 'currentWeek', 'weekRange', 'groupStats', 'offerings'));
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
                $totalMembers = $group->members->count();
                $submitted = SmallGroupResponse::where('small_group_id', $group->id)
                    ->forWeek($currentWeek)
                    ->distinct('member_id')
                    ->count('member_id');

                return [
                    'group' => $group,
                    'total_members' => $totalMembers,
                    'submitted_count' => $submitted,
                    'participation_rate' => $totalMembers > 0 ? round(($submitted / $totalMembers) * 100) : 0,
                ];
            });

        // Church-wide statistics
        $churchStats = $this->calculateChurchStats($currentWeek);

        return view('small-groups.reports.admin-dashboard', compact('groups', 'currentWeek', 'weekRange', 'churchStats'));
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
        $responses = SmallGroupResponse::where('small_group_id', $groupId)
            ->forWeek($weekStart)
            ->with('question')
            ->get();

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

            if (str_contains($question->question_en, 'evangelism visits')) {
                $stats['total_evangelism_visits'] += (int)$response->response_value;
            } elseif (str_contains($question->question_en, 'help the community')) {
                $stats['total_community_help'] += (int)$response->response_value;
            } elseif (str_contains($question->question_en, 'Bible according to plan') && $response->response_value === '1') {
                $stats['members_read_bible']++;
            } elseif (str_contains($question->question_en, 'lesson according to plan') && $response->response_value === '1') {
                $stats['members_read_lesson']++;
            }
        }

        return $stats;
    }

    /**
     * Calculate church-wide statistics
     */
    private function calculateChurchStats($weekStart)
    {
        $responses = SmallGroupResponse::forWeek($weekStart)->with('question')->get();

        $stats = [
            'total_submissions' => $responses->pluck('member_id')->unique()->count(),
            'total_members' => Member::whereHas('smallGroups')->count(),
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

            if (str_contains($question->question_en, 'evangelism visits')) {
                $stats['total_evangelism_visits'] += (int)$response->response_value;
            } elseif (str_contains($question->question_en, 'help the community')) {
                $stats['total_community_help'] += (int)$response->response_value;
            }
        }

        return $stats;
    }
}
