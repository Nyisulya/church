<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\SmallGroup;
use App\Models\SmallGroupMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmallGroupController extends Controller
{
    public function index()
    {
        $groups = SmallGroup::with(['leader', 'members'])->where('status', 'active')->get();
        return view('small-groups.index', compact('groups'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('full_name')->get();
        return view('small-groups.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:members,id',
            'meeting_day' => 'nullable|string',
            'meeting_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:5|max:50',
        ]);

        SmallGroup::create($validated);

        return redirect()->route('small-groups.index')->with('success', 'Small Group created successfully.');
    }

    public function show(SmallGroup $smallGroup)
    {
        $smallGroup->load(['leader', 'members', 'meetings.creator']);
        $availableMembers = Member::where('status', 'active')
            ->whereDoesntHave('smallGroups', function ($query) use ($smallGroup) {
                $query->where('small_group_id', $smallGroup->id);
            })
            ->orderBy('full_name')
            ->get();

        return view('small-groups.show', compact('smallGroup', 'availableMembers'));
    }

    public function edit(SmallGroup $smallGroup)
    {
        $members = Member::where('status', 'active')->orderBy('full_name')->get();
        return view('small-groups.edit', compact('smallGroup', 'members'));
    }

    public function update(Request $request, SmallGroup $smallGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:members,id',
            'meeting_day' => 'nullable|string',
            'meeting_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:5|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $smallGroup->update($validated);

        return redirect()->route('small-groups.index')->with('success', 'Small Group updated successfully.');
    }

    public function destroy(SmallGroup $smallGroup)
    {
        $smallGroup->delete();
        return redirect()->route('small-groups.index')->with('success', 'Small Group deleted successfully.');
    }

    // Add member to group
    public function addMember(Request $request, SmallGroup $smallGroup)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'role' => 'nullable|in:member,co-leader',
        ]);

        if ($smallGroup->isFull()) {
            return back()->with('error', 'This group is full.');
        }

        $smallGroup->members()->attach($validated['member_id'], [
            'role' => $validated['role'] ?? 'member',
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Member added to group.');
    }

    // Remove member from group
    public function removeMember(SmallGroup $smallGroup, Member $member)
    {
        $smallGroup->members()->detach($member->id);
        return back()->with('success', 'Member removed from group.');
    }

    // Store meeting
    public function storeMeeting(Request $request, SmallGroup $smallGroup)
    {
        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'topic' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attendees_count' => 'required|integer|min:0',
        ]);

        $smallGroup->meetings()->create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Meeting logged successfully.');
    }

    // My group (member view)
    public function myGroup()
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        $group = $user->member->smallGroups()->with(['leader', 'members', 'meetings' => function($query) {
            $query->latest()->take(5);
        }])->first();

        // Fetch active offerings and calculate debts
        $myDebts = collect();
        if ($group) {
            $offerings = $group->offerings()->where('is_active', true)->get();
            foreach ($offerings as $offering) {
                $balance = $offering->getMemberBalance($user->member->id);
                if ($balance > 0) {
                    $offering->my_balance = $balance;
                    $myDebts->push($offering);
                }
            }
        }

        return view('small-groups.my-group', compact('group', 'myDebts'));
    }

    // Kanda Attendance View
    public function groupAttendance(Request $request)
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('dashboard')->with('warning', 'Tafadhali kamilisha taarifa zako kwanza.');
        }

        // Get the group where user is a leader
        $group = SmallGroup::where('leader_id', $user->member->id)->first();
        
        if (!$group) {
            return redirect()->route('small-groups.my-group')->with('error', 'Huna ruhusa ya kuchukua mahudhurio kwa sababu wewe si kiongozi wa kanda.');
        }

        // Get recent events (services) to select from
        $events = \App\Models\Event::whereDate('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();

        $selectedEventId = $request->get('event_id', $events->first()->id ?? null);
        $selectedEvent = null;
        $attendances = collect();

        if ($selectedEventId) {
            $selectedEvent = \App\Models\Event::find($selectedEventId);
            
            // Get attendances for this event for members of this group
            $groupMemberIds = $group->members->pluck('id');
            $attendances = \App\Models\Attendance::where('event_id', $selectedEventId)
                ->whereIn('member_id', $groupMemberIds)
                ->get()
                ->keyBy('member_id');
        }

        $group->load('members');

        return view('small-groups.attendance', compact('group', 'events', 'selectedEvent', 'attendances'));
    }

    // Mark Kanda Attendance
    public function markGroupAttendance(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'member_id' => 'required|exists:members,id',
            'status' => 'required|in:present,absent,late',
        ]);

        $user = Auth::user();
        if (!$user->member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Verify the user is the leader of the group this member belongs to
        $group = SmallGroup::where('leader_id', $user->member->id)->first();
        if (!$group || !$group->members()->where('member_id', $validated['member_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or member not in your group.'], 403);
        }

        \App\Models\Attendance::updateOrCreate(
            [
                'event_id' => $validated['event_id'],
                'member_id' => $validated['member_id'],
            ],
            [
                'status' => $validated['status'],
                'scanned_by' => $user->id,
                'scanned_at' => now(),
            ]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mahudhurio yamewekwa kikamilifu.',
                'member_id' => $validated['member_id'],
                'status' => $validated['status']
            ]);
        }

        return back()->with('success', 'Mahudhurio yamewekwa kikamilifu.');
    }

    // Mark Kanda Attendance (Bulk)
    public function bulkMarkGroupAttendance(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'present' => 'array',
            'present.*' => 'exists:members,id',
            'absent' => 'array',
            'absent.*' => 'exists:members,id',
        ]);

        $user = Auth::user();
        if (!$user->member) {
            file_put_contents(storage_path('logs/attendance_debug.log'), "[" . date('Y-m-d H:i:s') . "] User has no member profile.\n", FILE_APPEND);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Verify the user is the leader of the group
        $group = SmallGroup::where('leader_id', $user->member->id)->first();
        if (!$group) {
            file_put_contents(storage_path('logs/attendance_debug.log'), "[" . date('Y-m-d H:i:s') . "] User (Member ID: {$user->member->id}) is not a leader of any group.\n", FILE_APPEND);
            return response()->json(['success' => false, 'message' => 'Unauthorized. Not a leader.'], 403);
        }

        // Get group member IDs
        $groupMemberIds = $group->members->pluck('id')->map(fn($id) => (int)$id)->toArray();

        $present = array_map('intval', $validated['present'] ?? []);
        $absent = array_map('intval', $validated['absent'] ?? []);

        file_put_contents(storage_path('logs/attendance_debug.log'), "[" . date('Y-m-d H:i:s') . "] Group: {$group->name} (ID: {$group->id})\n", FILE_APPEND);
        file_put_contents(storage_path('logs/attendance_debug.log'), "Group Member IDs: " . json_encode($groupMemberIds) . "\n", FILE_APPEND);
        file_put_contents(storage_path('logs/attendance_debug.log'), "Present IDs received: " . json_encode($present) . "\n", FILE_APPEND);
        file_put_contents(storage_path('logs/attendance_debug.log'), "Absent IDs received: " . json_encode($absent) . "\n", FILE_APPEND);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $markedPresentCount = 0;
            $markedAbsentCount = 0;

            foreach ($present as $memberId) {
                if (in_array($memberId, $groupMemberIds, true)) {
                    \App\Models\Attendance::updateOrCreate(
                        [
                            'event_id' => $validated['event_id'],
                            'member_id' => $memberId,
                        ],
                        [
                            'status' => 'present',
                            'scanned_by' => $user->id,
                            'scanned_at' => now(),
                        ]
                    );
                    $markedPresentCount++;
                } else {
                    file_put_contents(storage_path('logs/attendance_debug.log'), "Member ID {$memberId} not in group member IDs!\n", FILE_APPEND);
                }
            }

            foreach ($absent as $memberId) {
                if (in_array($memberId, $groupMemberIds, true)) {
                    \App\Models\Attendance::updateOrCreate(
                        [
                            'event_id' => $validated['event_id'],
                            'member_id' => $memberId,
                        ],
                        [
                            'status' => 'absent',
                            'scanned_by' => $user->id,
                            'scanned_at' => now(),
                        ]
                    );
                    $markedAbsentCount++;
                } else {
                    file_put_contents(storage_path('logs/attendance_debug.log'), "Member ID {$memberId} not in group member IDs!\n", FILE_APPEND);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            file_put_contents(storage_path('logs/attendance_debug.log'), "Successfully saved. Marked Present: {$markedPresentCount}, Marked Absent: {$markedAbsentCount}\n\n", FILE_APPEND);

            // Calculate new counts for this specific group
            $attendances = \App\Models\Attendance::where('event_id', $validated['event_id'])
                ->whereIn('member_id', $groupMemberIds)
                ->get();
                
            $counts = [
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
            ];
            $counts['not_marked'] = count($groupMemberIds) - $attendances->count();

            return response()->json([
                'success' => true,
                'message' => 'Mahudhurio yamehifadhiwa kikamilifu!',
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            file_put_contents(storage_path('logs/attendance_debug.log'), "Error: " . $e->getMessage() . "\n\n", FILE_APPEND);
            return response()->json([
                'success' => false,
                'message' => 'Kuna tatizo: ' . $e->getMessage()
            ], 500);
        }
    }
}
