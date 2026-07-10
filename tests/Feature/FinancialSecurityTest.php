<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Contribution;
use App\Models\Transaction;
use App\Models\Pledge;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancialSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed Spatie Roles & Permissions
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    /** @test */
    public function treasurer_is_blocked_from_updating_or_deleting_contributions()
    {
        // 1. Create a treasurer user
        User::$createMemberProfile = false;
        $treasurer = User::create([
            'name' => 'Treasurer User',
            'email' => 'treasurer@example.com',
            'password' => bcrypt('password123'),
        ]);
        $treasurer->assignRole('treasurer');

        // 2. Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $admin->assignRole('admin');
        User::$createMemberProfile = true;

        // 3. Create a member for the contribution
        User::$createMemberProfile = false;
        $member = Member::create([
            'full_name' => 'Test Member',
            'email' => 'member@example.com',
            'status' => 'active',
        ]);
        User::$createMemberProfile = true;

        $contribution = Contribution::create([
            'member_id' => $member->id,
            'amount' => 1000.00,
            'type' => 'sadaka',
            'payment_method' => 'cash',
            'date' => now(),
            'recorded_by' => $admin->id,
        ]);

        // 4. Assert policy restrictions
        $this->assertFalse($treasurer->can('update', $contribution));
        $this->assertFalse($treasurer->can('delete', $contribution));
        
        $this->assertTrue($admin->can('update', $contribution));
        $this->assertTrue($admin->can('delete', $contribution));
    }

    /** @test */
    public function treasurer_is_blocked_from_updating_or_deleting_transactions()
    {
        User::$createMemberProfile = false;
        $treasurer = User::create([
            'name' => 'Treasurer User',
            'email' => 'treasurer@example.com',
            'password' => bcrypt('password123'),
        ]);
        $treasurer->assignRole('treasurer');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $admin->assignRole('admin');
        User::$createMemberProfile = true;

        $transaction = Transaction::create([
            'type' => 'income',
            'category' => 'Sadaka',
            'amount' => 1000.00,
            'payment_method' => 'Cash',
            'transaction_date' => now(),
            'recorded_by' => $admin->id,
        ]);

        $this->assertFalse($treasurer->can('update', $transaction));
        $this->assertFalse($treasurer->can('delete', $transaction));
        
        $this->assertTrue($admin->can('update', $transaction));
        $this->assertTrue($admin->can('delete', $transaction));
    }

    /** @test */
    public function treasurer_is_blocked_from_updating_or_deleting_pledges()
    {
        User::$createMemberProfile = false;
        $treasurer = User::create([
            'name' => 'Treasurer User',
            'email' => 'treasurer@example.com',
            'password' => bcrypt('password123'),
        ]);
        $treasurer->assignRole('treasurer');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $admin->assignRole('admin');
        User::$createMemberProfile = true;

        User::$createMemberProfile = false;
        $member = Member::create([
            'full_name' => 'Test Member',
            'email' => 'member@example.com',
            'status' => 'active',
        ]);
        User::$createMemberProfile = true;

        $pledge = Pledge::create([
            'member_id' => $member->id,
            'amount' => 5000.00,
            'purpose' => 'Building Fund',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $this->assertFalse($treasurer->can('update', $pledge));
        $this->assertFalse($treasurer->can('delete', $pledge));
        
        $this->assertTrue($admin->can('update', $pledge));
        $this->assertTrue($admin->can('delete', $pledge));
    }

    /** @test */
    public function treasurer_is_blocked_from_updating_or_deleting_projects()
    {
        User::$createMemberProfile = false;
        $treasurer = User::create([
            'name' => 'Treasurer User',
            'email' => 'treasurer@example.com',
            'password' => bcrypt('password123'),
        ]);
        $treasurer->assignRole('treasurer');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $admin->assignRole('admin');
        User::$createMemberProfile = true;

        $project = Project::create([
            'name' => 'Building Project',
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $this->assertFalse($treasurer->can('update', $project));
        $this->assertFalse($treasurer->can('delete', $project));
        
        $this->assertTrue($admin->can('update', $project));
        $this->assertTrue($admin->can('delete', $project));
    }

    /** @test */
    public function only_super_admin_can_access_audit_logs()
    {
        User::$createMemberProfile = false;
        $superAdmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $superAdmin->assignRole('super_admin');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $admin->assignRole('admin');
        User::$createMemberProfile = true;

        // 1. Act as admin (should return 403)
        $response = $this->actingAs($admin)->get(route('admin.audit-logs'));
        $response->assertStatus(403);

        // 2. Act as super admin (should return 200)
        $response = $this->actingAs($superAdmin)->get(route('admin.audit-logs'));
        $response->assertStatus(200);
    }
}
