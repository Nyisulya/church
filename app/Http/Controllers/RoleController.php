<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            // Group permissions by their prefix (e.g., 'user-create' -> 'user')
            $parts = explode('-', $permission->name);
            return ucfirst($parts[0]);
        });
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')
                ->with('error', 'The Super Admin role cannot be edited.');
        }

        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode('-', $permission->name);
            return ucfirst($parts[0]);
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')
                ->with('error', 'The Super Admin role cannot be edited.');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            } else {
                $role->syncPermissions([]);
            }
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin') {
            return back()->with('error', 'The Super Admin role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role because it is assigned to users.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
