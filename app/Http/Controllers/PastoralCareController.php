<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\FollowUp;
use App\Models\PrayerRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PastoralCareController extends Controller
{
    public function dashboard(): View
    {
        $recentVisits = Visit::with(['member', 'visitor'])
            ->orderBy('visit_date', 'desc')
            ->limit(10)
            ->get();

        $pendingFollowUps = FollowUp::with(['member', 'assignedTo'])
            ->pending()
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $overdueFollowUps = FollowUp::with(['member', 'assignedTo'])
            ->overdue()
            ->count();

        $activePrayerRequests = PrayerRequest::with('member')
            ->active()
            ->orderBy('request_date', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_visits' => Visit::whereMonth('visit_date', now()->month)->count(),
            'pending_followups' => FollowUp::pending()->count(),
            'overdue_followups' => $overdueFollowUps,
            'active_prayers' => PrayerRequest::active()->count(),
        ];

        return view('pastoral-care.dashboard', compact(
            'recentVisits',
            'pendingFollowUps',
            'activePrayerRequests',
            'stats'
        ));
    }

    public function visits(): View
    {
        $visits = Visit::with(['member', 'visitor'])
            ->orderBy('visit_date', 'desc')
            ->paginate(20);

        return view('pastoral-care.visits', compact('visits'));
    }

    public function storeVisit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'visitor_id' => 'required|exists:users,id',
            'visit_type' => 'required|in:home,hospital,office,phone_call',
            'visit_date' => 'required|date',
            'purpose' => 'required|string',
            'notes' => 'nullable|string',
            'outcome' => 'nullable|string',
            'follow_up_required' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        Visit::create($validated);

        return redirect()->route('pastoral-care.visits')
            ->with('status', 'Visit recorded successfully');
    }

    public function followUps(Request $request): View
    {
        $query = FollowUp::with(['member', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $followUps = $query->orderBy('due_date')->paginate(20);
        $users = User::orderBy('name')->get();

        return view('pastoral-care.follow-ups', compact('followUps', 'users'));
    }

    public function storeFollowUp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        $validated['created_by'] = auth()->id();

        FollowUp::create($validated);

        return redirect()->route('pastoral-care.follow-ups')
            ->with('status', 'Follow-up task created successfully');
    }

    public function completeFollowUp(FollowUp $followUp): RedirectResponse
    {
        $followUp->markComplete();

        return redirect()->route('pastoral-care.follow-ups')
            ->with('status', 'Follow-up marked as completed');
    }

    public function prayerRequests(): View
    {
        $prayers = PrayerRequest::with('member')
            ->orderBy('request_date', 'desc')
            ->paginate(20);

        return view('pastoral-care.prayers', compact('prayers'));
    }

    public function storePrayerRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'request' => 'required|string',
            'request_date' => 'required|date',
            'is_private' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        PrayerRequest::create($validated);

        return redirect()->route('pastoral-care.prayers')
            ->with('status', 'Prayer request added successfully');
    }

    public function memberHistory(Member $member): View
    {
        $visits = Visit::where('member_id', $member->id)
            ->with('visitor')
            ->orderBy('visit_date', 'desc')
            ->get();

        $followUps = FollowUp::where('member_id', $member->id)
            ->with('assignedTo')
            ->orderBy('due_date', 'desc')
            ->get();

        $prayers = PrayerRequest::where('member_id', $member->id)
            ->orderBy('request_date', 'desc')
            ->get();

        return view('pastoral-care.member-history', compact('member', 'visits', 'followUps', 'prayers'));
    }
}
