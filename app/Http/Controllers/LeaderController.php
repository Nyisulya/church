<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Department;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;

class LeaderController extends Controller
{
    /**
     * Export leaders to PDF or CSV.
     */
    public function export(Request $request)
    {
        // Reuse index logic to get leaders (refactor if needed, but for now copy-paste or extract method is fine)
        // For simplicity and speed, I'll extract the data fetching logic or just re-run it.
        // Let's re-run it as it's cleaner than refactoring the whole controller right now.
        
        $systemLeaderRoles = ['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader'];
        
        $systemLeaders = Member::whereHas('user', function ($query) use ($systemLeaderRoles) {
            $query->whereHas('roles', function ($q) use ($systemLeaderRoles) {
                $q->whereIn('name', $systemLeaderRoles);
            });
        })->with(['user.roles'])->get();

        $departmentLeaders = Member::whereHas('departments', function ($query) {
            $query->where('department_member.role', 'leader');
        })->with(['departments' => function($q) {
            $q->wherePivot('role', 'leader');
        }])->get();

        $leaders = collect();

        foreach ($systemLeaders as $member) {
            foreach ($member->user->roles as $role) {
                if (in_array($role->name, $systemLeaderRoles)) {
                    $leaders->push([
                        'name' => $member->full_name,
                        'type' => __('System Role'),
                        'role' => ucfirst(str_replace('_', ' ', $role->name)),
                        'context' => 'Global',
                        'email' => $member->email,
                        'phone' => $member->phone,
                    ]);
                }
            }
        }

        foreach ($departmentLeaders as $member) {
            foreach ($member->departments as $dept) {
                $leaders->push([
                    'name' => $member->full_name,
                    'type' => __('Department Leader'),
                    'role' => 'Leader',
                    'context' => $dept->name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                ]);
            }
        }

        if ($request->format === 'csv') {
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=church_leaders.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $columns = [__('Name'), __('Type'), __('Role'), __('Context'), __('Email'), __('Phone')];

            $callback = function() use ($leaders, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($leaders as $leader) {
                    fputcsv($file, [
                        $leader['name'],
                        $leader['type'],
                        $leader['role'],
                        $leader['context'],
                        $leader['email'],
                        $leader['phone']
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to PDF
        $pdf = Pdf::loadView('leaders.pdf', compact('leaders'));
        return $pdf->download('church_leaders.pdf');
    }

    /**
     * Display a listing of the leaders.
     */
    public function index(Request $request)
    {
        // 1. Get Members with System Leadership Roles
        $systemLeaderRoles = ['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader'];
        
        $systemLeadersQuery = Member::whereHas('user', function ($query) use ($systemLeaderRoles) {
            $query->whereHas('roles', function ($q) use ($systemLeaderRoles) {
                $q->whereIn('name', $systemLeaderRoles);
            });
        })->with(['user.roles']);

        // 2. Get Department Leaders
        $departmentLeadersQuery = Member::whereHas('departments', function ($query) {
            $query->where('department_member.role', 'leader');
        })->with(['departments' => function($q) {
            $q->wherePivot('role', 'leader');
        }]);

        // Apply Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $systemLeadersQuery->where('full_name', 'like', "%{$search}%");
            $departmentLeadersQuery->where('full_name', 'like', "%{$search}%");
        }

        $systemLeaders = $systemLeadersQuery->get();
        $departmentLeaders = $departmentLeadersQuery->get();

        // Merge and format for the view
        $leaders = collect();

        foreach ($systemLeaders as $member) {
            foreach ($member->user->roles as $role) {
                if (in_array($role->name, $systemLeaderRoles)) {
                    // Filter by type if requested
                    if ($request->has('type') && $request->type == 'department') continue;

                    $leaders->push([
                        'member' => $member,
                        'type' => __('System Role'),
                        'role_name' => ucfirst(str_replace('_', ' ', $role->name)),
                        'role_slug' => $role->name,
                        'context' => 'Global',
                        'is_system' => true,
                        'role_id' => $role->id,
                        'department_id' => null
                    ]);
                }
            }
        }

        foreach ($departmentLeaders as $member) {
            foreach ($member->departments as $dept) {
                // Filter by type if requested
                if ($request->has('type') && $request->type == 'system') continue;

                $leaders->push([
                    'member' => $member,
                    'type' => __('Department Leader'),
                    'role_name' => 'Leader',
                    'context' => $dept->name,
                    'is_system' => false,
                    'role_id' => null,
                    'department_id' => $dept->id
                ]);
            }
        }

        // Statistics
        $stats = [
            'total' => $leaders->count(), // Note: This might double count if someone has multiple roles, but for "Leadership Positions" it's accurate. Unique people would be $leaders->pluck('member.id')->unique()->count()
            'unique_leaders' => $leaders->pluck('member.id')->unique()->count(),
            'system_leaders' => $leaders->where('is_system', true)->count(),
            'department_leaders' => $leaders->where('is_system', false)->count(),
        ];

        return view('leaders.index', compact('leaders', 'stats'));
    }

    /**
     * Show the form for creating a new leader.
     */
    public function create()
    {
        $members = Member::orderBy('full_name')->get();
        $departments = Department::orderBy('name')->get();
        // Filter out 'member' role, only show leadership roles
        $roles = Role::whereIn('name', ['admin', 'pastor', 'treasurer', 'department_leader'])->get();

        return view('leaders.create', compact('members', 'departments', 'roles'));
    }

    /**
     * Store a newly created leader in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'type' => 'required|in:system,department',
            'role_id' => 'required_if:type,system|exists:roles,id',
            'department_id' => 'required_if:type,department|exists:departments,id',
        ]);

        $member = Member::findOrFail($request->member_id);

        if ($request->type === 'system') {
            $role = Role::findOrFail($request->role_id);
            
            // Ensure member has a user account
            if (!$member->user) {
                return back()->with('error', __('This member does not have a user account. Please create one first.'));
            }

            $member->user->assignRole($role);
            
            return redirect()->route('leaders.index')->with('success', __('System leadership role assigned successfully.'));
        } 
        
        if ($request->type === 'department') {
            // Check if already a member of the department
            $exists = $member->departments()->where('department_id', $request->department_id)->exists();

            if ($exists) {
                // Update existing pivot to be leader
                $member->departments()->updateExistingPivot($request->department_id, ['role' => 'leader']);
            } else {
                // Attach as leader
                $member->departments()->attach($request->department_id, ['role' => 'leader', 'joined_at' => now()]);
            }

            return redirect()->route('leaders.index')->with('success', __('Department leader assigned successfully.'));
        }

        return back()->with('error', __('Invalid selection.'));
    }

    /**
     * Remove the specified leader role.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'type' => 'required|in:system,department',
            'role_name' => 'required_if:type,system',
            'department_id' => 'required_if:type,department|exists:departments,id',
        ]);

        $member = Member::findOrFail($request->member_id);

        if ($request->type === 'system') {
            if ($member->user) {
                // Don't remove super_admin if it's the only one or logged in user (basic protection)
                if ($request->role_name === 'super_admin' && auth()->id() === $member->user_id) {
                     return back()->with('error', __('You cannot remove your own Super Admin role.'));
                }
                
                $member->user->removeRole(str_replace(' ', '_', strtolower($request->role_name))); // Convert back to slug if needed, but we passed the display name? 
                // Wait, in index we passed 'role_name' as display name. Let's fix index to pass slug or ID.
                // Actually, let's pass the role name slug in the form for easier handling.
            }
        } elseif ($request->type === 'department') {
            // Downgrade to 'member' instead of detaching completely? Or detach?
            // Requirement says "manage leaders", so removing leadership status.
            // Let's set role to 'member' if they are in the department.
            $member->departments()->updateExistingPivot($request->department_id, ['role' => 'member']);
        }

        return redirect()->route('leaders.index')->with('success', __('Leadership role removed successfully.'));
    }
    
    // Helper to fix the destroy logic:
    // I need to accept parameters to identify WHAT to remove. 
    // Since it's not a standard resource ID, I'll use a custom route or query params.
    // Let's use a custom 'remove' method or just use destroy with query params if I mock a resource.
    // Better: POST to a specific remove route.
    
    public function remove(Request $request)
    {
         $request->validate([
            'member_id' => 'required|exists:members,id',
            'context_type' => 'required|in:system,department', // system or department
            'context_id' => 'required', // role name (string) or department id (int)
        ]);

        $member = Member::findOrFail($request->member_id);

        if ($request->context_type === 'system') {
            if ($member->user) {
                 // Convert display name back to slug? No, let's pass the slug from the view.
                 $roleName = $request->context_id;
                 $member->user->removeRole($roleName);
            }
        } elseif ($request->context_type === 'department') {
            $departmentId = $request->context_id;
            // Set role back to 'member'
            $member->departments()->updateExistingPivot($departmentId, ['role' => 'member']);
        }

        return redirect()->route('leaders.index')->with('success', __('Leader removed successfully.'));
    }
}
