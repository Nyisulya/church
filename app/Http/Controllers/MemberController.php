<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Department;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Member::class);
        
        $user = Auth::user();
        $isRegularMember = $user->hasRole('member') && !$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']);
        
        // Regular members can only see their own profile
        if ($isRegularMember) {
            $member = $user->member;
            if (!$member) {
                abort(404, 'No member profile found. Please contact administrator.');
            }
            return view('members.index', compact('member', 'isRegularMember'));
        }
        
        // Admins and staff see all members
        $query = Member::with('departments');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(15);
        $isRegularMember = false;
        
        return view('members.index', compact('members', 'isRegularMember'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Member::class);
        $departments = Department::all();
        return view('members.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $this->authorize('create', Member::class);
        
        $data = $request->validated();
        
        // Create User
        \App\Models\User::$createMemberProfile = false;
        try {
            $user = \App\Models\User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            ]);
        } finally {
            \App\Models\User::$createMemberProfile = true;
        }

        // Assign Role
        $role = $request->input('member_type', 'member');
        $user->assignRole($role);

        $data['user_id'] = $user->id;

        $member = Member::create($data);
        
        if (isset($data['departments'])) {
            $member->departments()->sync($data['departments']);
        }

        return redirect()->route('members.show', $member)->with('status', 'Member created successfully. Default password is "password123"');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): View
    {
        $this->authorize('view', $member);
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member): View
    {
        $this->authorize('update', $member);
        $departments = Department::all();
        return view('members.edit', compact('member', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $this->authorize('update', $member);
        $data = $request->validated();
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($member->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($member->profile_photo);
            }
            
            // Store new photo
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }
        
        $member->update($data);
        
        if (isset($data['departments'])) {
            $member->departments()->sync($data['departments']);
        } else {
            $member->departments()->detach();
        }

        return redirect()->route('members.show', $member)->with('status', 'Member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member): RedirectResponse
    {
        $this->authorize('delete', $member);
        $member->delete();
        return redirect()->route('members.index')->with('status', 'Member deleted successfully');
    }
}
