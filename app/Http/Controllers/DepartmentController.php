<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        // Admins and leaders can see all departments
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader'])) {
            $departments = Department::withCount('members')
                ->with(['chairman', 'secretary'])
                ->get();
        } else {
            // Regular members only see their own departments
            $departments = $user->member 
                ? $user->member->departments()->withCount('members')->get()
                : collect();
        }
        
        return view('departments.index', compact('departments'));
    }

    public function show(Department $department): View
    {
        $user = auth()->user();
        
        // Check if user can view this department
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader'])) {
            // Regular members can only view departments they belong to
            if (!$user->member || !$user->member->departments->contains($department->id)) {
                abort(403, 'You do not have access to this department.');
            }
        }
        
        $department->load(['members', 'announcements.author']);

        // Mark announcement notifications as read for this department
        $user->unreadNotifications()
            ->where('type', 'App\Notifications\NewMinistryAnnouncementNotification')
            ->get()
            ->filter(function ($notification) use ($department) {
                return isset($notification->data['department_id']) && $notification->data['department_id'] == $department->id;
            })
            ->each->markAsRead();
        
        return view('departments.show', compact('department'));
    }

    public function create()
    {
        $members = \App\Models\Member::where('status', 'active')
            ->orderBy('full_name')
            ->get();
            
        return view('departments.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
            'chairman_id' => 'nullable|exists:members,id',
            'secretary_id' => 'nullable|exists:members,id',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Ministry created successfully.');
    }

    public function edit(Department $department)
    {
        $members = \App\Models\Member::where('status', 'active')
            ->orderBy('full_name')
            ->get();
            
        return view('departments.edit', compact('department', 'members'));
    }

    public function update(Request $request, Department $department)
    {
        $validated= $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'chairman_id' => 'nullable|exists:members,id',
            'secretary_id' => 'nullable|exists:members,id',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')->with('success', 'Ministry updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Ministry deleted successfully.');
    }

    public function storeAnnouncement(Request $request, Department $department)
    {
        $user = auth()->user();
        $member = $user->member;

        // Check if user is a leader of this ministry
        if (!$member || ($department->chairman_id != $member->id && $department->secretary_id != $member->id)) {
            abort(403, 'Only ministry leaders can post announcements.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $announcement = $department->announcements()->create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_general' => false,
            'is_active' => true,
            'announcement_date' => now(),
        ]);

        // Notify ministry members
        $members = $department->members;
        foreach ($members as $member) {
            if ($member->user) {
                $member->user->notify(new \App\Notifications\NewMinistryAnnouncementNotification($announcement));
            }
        }

        return redirect()->route('departments.show', $department)->with('success', 'Announcement posted successfully.');
    }
}
