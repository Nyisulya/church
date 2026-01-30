<?php

namespace App\Http\Controllers;

use App\Models\MinistryPledge;
use App\Models\MinistryContribution;
use App\Models\Department;
use Illuminate\Http\Request;

class MinistryPledgeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'You must have a member profile to view ministry pledges.');
        }

        // Get all departments the user is a member of
        $departmentIds = $member->departments->pluck('id');

        // Get pledges from user's ministries
        $pledges = MinistryPledge::whereIn('department_id', $departmentIds)
            ->with(['department', 'creator', 'contributions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ministry-pledges.index', compact('pledges'));
    }

    public function create(Department $department)
    {
        $user = auth()->user();
        $member = $user->member;

        // Check if user is a leader of this ministry
        if (!$member || ($department->chairman_id != $member->id && $department->secretary_id != $member->id)) {
            abort(403, 'Only ministry leaders can create pledges.');
        }

        return view('ministry-pledges.create', compact('department'));
    }

    public function store(Request $request, Department $department)
    {
        $user = auth()->user();
        $member = $user->member;

        // Check if user is a leader
        if (!$member || ($department->chairman_id != $member->id && $department->secretary_id != $member->id)) {
            abort(403, 'Only ministry leaders can create pledges.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'target_date' => 'nullable|date|after:today',
        ]);

        $validated['department_id'] = $department->id;
        $validated['created_by'] = $user->id;

        MinistryPledge::create($validated);

        // Notify ministry members
        $pledge = MinistryPledge::latest()->first();
        $members = $department->members;
        
        foreach ($members as $member) {
            if ($member->user) {
                $member->user->notify(new \App\Notifications\NewMinistryPledgeNotification($pledge));
            }
        }

        return redirect()->route('departments.show', $department)
            ->with('success', 'Pledge created successfully.');
    }

    public function show(MinistryPledge $ministryPledge)
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'You must have a member profile.');
        }

        // Check if user is a member of this ministry
        $isMember = $member->departments->contains($ministryPledge->department_id);

        if (!$isMember) {
            abort(403, 'You can only view pledges from your ministries.');
        }

        // Mark notifications as read
        $user->unreadNotifications()
            ->where('type', 'App\Notifications\NewMinistryPledgeNotification')
            ->get()
            ->filter(function ($notification) use ($ministryPledge) {
                return isset($notification->data['pledge_id']) && $notification->data['pledge_id'] == $ministryPledge->id;
            })
            ->each->markAsRead();

        $ministryPledge->load(['department', 'creator', 'contributions.member']);

        // Check if user is a leader
        $isLeader = $ministryPledge->department->chairman_id == $member->id 
                 || $ministryPledge->department->secretary_id == $member->id;

        // Get user's contributions to this pledge
        $myContributions = $ministryPledge->contributions()
            ->where('member_id', $member->id)
            ->orderBy('contribution_date', 'desc')
            ->get();

        return view('ministry-pledges.show', compact('ministryPledge', 'isLeader', 'myContributions'));
    }

    public function contribute(Request $request, MinistryPledge $ministryPledge)
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return back()->with('error', 'You must have a member profile.');
        }

        // Check if user is a member of this ministry
        $isMember = $member->departments->contains($ministryPledge->department_id);

        if (!$isMember) {
            return back()->with('error', 'You can only contribute to pledges from your ministries.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['ministry_pledge_id'] = $ministryPledge->id;
        $validated['member_id'] = $member->id;

        MinistryContribution::create($validated);

        return back()->with('success', 'Contribution recorded successfully.');
    }
}
