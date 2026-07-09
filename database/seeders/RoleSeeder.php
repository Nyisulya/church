<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // User Management
            'user-view',
            'user-create',
            'user-edit',
            'user-delete',

            // Member Management
            'member-view',
            'member-create',
            'member-edit',
            'member-delete',

            // Role Management
            'role-view',
            'role-create',
            'role-edit',
            'role-delete',

            // Finance & Giving
            'finance-view',
            'finance-create',
            'finance-edit',
            'finance-delete',

            // Attendance
            'attendance-view',
            'attendance-create',
            'attendance-edit',

            // Small Groups
            'smallgroup-view',
            'smallgroup-create',
            'smallgroup-edit',
            'smallgroup-delete',

            // Events
            'event-view',
            'event-create',
            'event-edit',
            'event-delete',
            
            // Departments
            'department-view',
            'department-create',
            'department-edit',
            
            // Reports
            'report-view',

            // Pastoral Care
            'pastoral-care-view',
            'pastoral-care-create',
            'pastoral-care-edit',
            'pastoral-care-delete',

            // Assets & Inventory
            'asset-view',
            'asset-create',
            'asset-edit',
            'asset-delete',

            // Volunteer Rostering
            'roster-view',
            'roster-create',
            'roster-edit',
            'roster-delete',

            // Prayer Wall
            'prayer-view',
            'prayer-create',
            'prayer-edit',
            'prayer-delete',

            // Celebrations (Birthdays/Anniversaries)
            'celebration-view',

            // Communication
            'communication-view',
            'communication-create', // Send messages

            // Projects
            'project-view',
            'project-create',
            'project-edit',
            'project-delete',

            // Pledges
            'pledge-view',
            'pledge-create',
            'pledge-edit',
            'pledge-delete',

            // Giving Categories
            'giving-category-view',
            'giving-category-create',
            'giving-category-edit',
            'giving-category-delete',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Permissions
        
        // Super Admin - gets all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        // Admin - gets most permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(\Spatie\Permission\Models\Permission::all());
        // You might want to revoke specific dangerous permissions from admin later if needed

        // Other Roles (permissions to be assigned via UI or specific logic)
        $roles = [
            'pastor',
            'department_leader',
            'member',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Treasurer - gets only finance, projects, pledges, giving categories, and reports permissions
        $treasurer = Role::firstOrCreate(['name' => 'treasurer']);
        $treasurerPermissions = [
            'finance-view',
            'finance-create',
            'finance-edit',
            'finance-delete',
            'project-view',
            'project-create',
            'project-edit',
            'project-delete',
            'pledge-view',
            'pledge-create',
            'pledge-edit',
            'pledge-delete',
            'giving-category-view',
            'giving-category-create',
            'giving-category-edit',
            'giving-category-delete',
            'report-view',
        ];
        $treasurer->syncPermissions($treasurerPermissions);

        // Pastor - gets pastoral care, members, visitors, small groups, events, roster, prayer wall, celebrations, communication, and view-only finance/pledges/projects
        $pastor = Role::firstOrCreate(['name' => 'pastor']);
        $pastorPermissions = [
            'pastoral-care-view',
            'pastoral-care-create',
            'pastoral-care-edit',
            'pastoral-care-delete',
            'member-view',
            'member-create',
            'member-edit',
            'event-view',
            'event-create',
            'event-edit',
            'event-delete',
            'roster-view',
            'roster-create',
            'roster-edit',
            'roster-delete',
            'smallgroup-view',
            'smallgroup-create',
            'smallgroup-edit',
            'smallgroup-delete',
            'prayer-view',
            'prayer-create',
            'prayer-edit',
            'prayer-delete',
            'celebration-view',
            'communication-view',
            'communication-create',
            'report-view',
            'project-view',
            'pledge-view',
            'giving-category-view',
            'finance-view',
        ];
        $pastor->syncPermissions($pastorPermissions);
    }
}
