<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user is admin/staff or regular member
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader'])) {
            // Admin view - show all events with all attendance
            $events = Event::whereDate('date', '>=', now()->subDays(30))
                ->orderBy('date', 'desc')
                ->with(['attendances'])
                ->get();
            
            return view('attendance.index', compact('events'));
        } else {
            // Member view - show only their attendance
            $member = $user->member;
            
            if (!$member) {
                // User doesn't have a member profile - show empty attendance view
                $attendances = collect(); // Empty collection
                return view('attendance.my-attendance', [
                    'attendances' => $attendances,
                    'member' => (object) ['full_name' => $user->name]
                ]);
            }
            
            $attendances = Attendance::where('member_id', $member->id)
                ->with('event')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('attendance.my-attendance', compact('attendances', 'member'));
        }
    }

    public function show(Event $event)
    {
        // Only admins can access the manual marking interface
        if (!auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader'])) {
            return redirect()->route('attendance.index')->with('error', 'You do not have permission to access this page.');
        }
        
        $attendances = Attendance::where('event_id', $event->id)
            ->with('member')
            ->get()
            ->keyBy('member_id');

        $members = Member::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('attendance.show', compact('event', 'attendances', 'members'));
    }

    public function markAttendance(Request $request, Event $event)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'status' => 'required|in:present,absent,late',
        ]);

        $attendance = Attendance::updateOrCreate(
            [
                'event_id' => $event->id,
                'member_id' => $validated['member_id'],
            ],
            [
                'status' => $validated['status'],
                'scanned_by' => auth()->id(),
                'scanned_at' => now(),
            ]
        );

        // Return JSON response for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            // Get updated counts
            $counts = [
                'present' => Attendance::where('event_id', $event->id)->where('status', 'present')->count(),
                'late' => Attendance::where('event_id', $event->id)->where('status', 'late')->count(),
                'absent' => Attendance::where('event_id', $event->id)->where('status', 'absent')->count(),
                'total' => Member::where('status', 'active')->count(),
            ];
            $counts['not_marked'] = $counts['total'] - ($counts['present'] + $counts['late'] + $counts['absent']);

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully.',
                'member_id' => $validated['member_id'],
                'status' => $validated['status'],
                'counts' => $counts,
            ]);
        }

        return back()->with('status', 'Attendance updated successfully.');
    }

    public function bulkMark(Request $request, Event $event)
    {
        $validated = $request->validate([
            'present' => 'array',
            'present.*' => 'exists:members,id',
            'absent' => 'array',
            'absent.*' => 'exists:members,id',
        ]);

        DB::beginTransaction();
        try {
            // Mark present
            if (isset($validated['present'])) {
                foreach ($validated['present'] as $memberId) {
                    Attendance::updateOrCreate(
                        [
                            'event_id' => $event->id,
                            'member_id' => $memberId,
                        ],
                        [
                            'status' => 'present',
                            'scanned_by' => auth()->id(),
                            'scanned_at' => now(),
                        ]
                    );
                }
            }

            // Mark absent
            if (isset($validated['absent'])) {
                foreach ($validated['absent'] as $memberId) {
                    Attendance::updateOrCreate(
                        [
                            'event_id' => $event->id,
                            'member_id' => $memberId,
                        ],
                        [
                            'status' => 'absent',
                            'scanned_by' => auth()->id(),
                            'scanned_at' => now(),
                        ]
                    );
                }
            }

            DB::commit();
            
            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                // Get updated counts
                $counts = [
                    'present' => Attendance::where('event_id', $event->id)->where('status', 'present')->count(),
                    'late' => Attendance::where('event_id', $event->id)->where('status', 'late')->count(),
                    'absent' => Attendance::where('event_id', $event->id)->where('status', 'absent')->count(),
                    'total' => Member::where('status', 'active')->count(),
                ];
                $counts['not_marked'] = $counts['total'] - ($counts['present'] + $counts['late'] + $counts['absent']);

                return response()->json([
                    'success' => true,
                    'message' => 'Attendance marked successfully.',
                    'counts' => $counts,
                ]);
            }
            
            return back()->with('status', 'Attendance marked successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error marking attendance: ' . $e->getMessage(),
                ], 500);
            }
            
            return back()->with('error', 'Error marking attendance: ' . $e->getMessage());
        }
    }

    public function scanQr(Request $request, $memberNumber)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Find active member
        $member = Member::where('member_number', trim($memberNumber))->first();
        if (!$member) {
            return view('attendance.scan-result', [
                'status' => 'error',
                'message' => 'Mwanachama mwenye namba hii hajapatikana kwenye mfumo yetu.',
                'member' => null
            ]);
        }

        // Find today's active event or next upcoming event
        $event = Event::whereDate('date', '>=', \Carbon\Carbon::today()->subDays(1))
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$event) {
            return view('attendance.scan-result', [
                'status' => 'error',
                'message' => 'Hakuna ibada au tukio lolote lililosajiliwa leo kwa ajili ya mahudhurio.',
                'member' => $member
            ]);
        }

        // Mark attendance
        $attendance = Attendance::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        if ($attendance) {
            if ($attendance->status === 'registered') {
                $attendance->update([
                    'status' => 'present',
                    'scanned_by' => auth()->id(),
                    'scanned_at' => now(),
                ]);
                $status = 'success';
                $message = "Mahudhurio yamesajiliwa kwa mwanachama aliyekuwa amejiandikisha.";
            } else {
                $status = 'warning';
                $message = "Mwanachama huyu tayari ameshawekewa mahudhurio ya tukio hili.";
            }
        } else {
            Attendance::create([
                'event_id' => $event->id,
                'member_id' => $member->id,
                'scanned_by' => auth()->id(),
                'scanned_at' => now(),
                'status' => 'present',
            ]);
            $status = 'success';
            $message = "Mahudhurio ya mwanachama yamesajiliwa kikamilifu.";
        }

        return view('attendance.scan-result', [
            'status' => $status,
            'message' => $message,
            'member' => $member,
            'event' => $event
        ]);
    }
}
