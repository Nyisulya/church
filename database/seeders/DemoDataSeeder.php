<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Department;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\Pledge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed demo data for testing and demonstration
     */
    public function run(): void
    {
        // Create demo users with roles
        $admin = User::updateOrCreate(
            ['email' => 'admin@church.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('super_admin');

        $pastor = User::updateOrCreate(
            ['email' => 'pastor@church.com'],
            [
                'name' => 'Pastor John Smith',
                'password' => Hash::make('password'),
            ]
        );
        $pastor->assignRole('pastor');

        $treasurer = User::updateOrCreate(
            ['email' => 'treasurer@church.com'],
            [
                'name' => 'Mary Johnson',
                'password' => Hash::make('password'),
            ]
        );
        $treasurer->assignRole('treasurer');

        // Create a regular member user
        $memberUser = User::updateOrCreate(
            ['email' => 'member@church.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
            ]
        );
        $memberUser->assignRole('member');

        // Create demo departments
        $worship = Department::firstOrCreate(
            ['name' => 'Worship Team'],
            ['description' => 'Music and worship ministry']
        );

        $youth = Department::firstOrCreate(
            ['name' => 'Youth Ministry'],
            ['description' => 'Ministry for young people']
        );

        $children = Department::firstOrCreate(
            ['name' => 'Children Ministry'],
            ['description' => 'Ministry for children']
        );

        // Create demo members and link to users
        $members = [];
        
        // Link pastor to member profile
        $pastorMember = Member::firstOrCreate(
            ['email' => 'pastor@church.com'],
            [
                'user_id' => $pastor->id,
                'full_name' => 'Pastor John Smith',
                'gender' => 'male',
                'date_of_birth' => '1975-04-12',
                'phone' => '555-1001',
                'marital_status' => 'married',
                'status' => 'active',
                'address' => '123 Church Street',
            ]
        );
        $members[] = $pastorMember;

        // Link treasurer to member profile
        $treasurerMember = Member::firstOrCreate(
            ['email' => 'treasurer@church.com'],
            [
                'user_id' => $treasurer->id,
                'full_name' => 'Mary Johnson',
                'gender' => 'female',
                'date_of_birth' => '1982-09-18',
                'phone' => '555-1002',
                'marital_status' => 'single',
                'status' => 'active',
                'address' => '456 Faith Avenue',
            ]
        );
        $members[] = $treasurerMember;

        // Link regular member user to profile
        $regularMember = Member::firstOrCreate(
            ['email' => 'member@church.com'],
            [
                'user_id' => $memberUser->id,
                'full_name' => 'John Doe',
                'gender' => 'male',
                'date_of_birth' => '1990-05-15',
                'phone' => '555-0101',
                'marital_status' => 'single',
                'status' => 'active',
                'address' => '789 Gospel Road',
            ]
        );
        $members[] = $regularMember;

        // Additional members without user accounts
        $memberData = [
            ['Jane Smith', 'female', '1985-08-20', 'jane@example.com', '555-0102'],
            ['Robert Johnson', 'male', '1978-03-10', 'robert@example.com', '555-0103'],
            ['Sarah Williams', 'female', '1992-11-25', 'sarah@example.com', '555-0104'],
            ['Michael Brown', 'male', '1988-07-08', 'michael@example.com', '555-0105'],
        ];

        foreach ($memberData as $data) {
            $member = Member::firstOrCreate(
                ['email' => $data[3]],
                [
                    'full_name' => $data[0],
                    'gender' => $data[1],
                    'date_of_birth' => $data[2],
                    'phone' => $data[4],
                    'marital_status' => 'single',
                    'status' => 'active',
                ]
            );
            $members[] = $member;
        }

        // Assign members to departments
        if (!empty($members)) {
            $members[0]->departments()->syncWithoutDetaching([$worship->id => ['role' => 'member', 'status' => 'active']]);
            $members[1]->departments()->syncWithoutDetaching([$youth->id => ['role' => 'leader', 'status' => 'active']]);
            $members[2]->departments()->syncWithoutDetaching([$children->id => ['role' => 'member', 'status' => 'active']]);
        }

        // Create demo events
        Event::firstOrCreate(
            ['name' => 'Sunday Service', 'date' => Carbon::today()->next('Sunday')],
            [
                'type' => 'service',
                'start_time' => Carbon::today()->next('Sunday')->setTime(9, 0),
                'end_time' => Carbon::today()->next('Sunday')->setTime(11, 0),
            ]
        );

        Event::firstOrCreate(
            ['name' => 'Wednesday Bible Study', 'date' => Carbon::today()->next('Wednesday')],
            [
                'type' => 'meeting',
                'start_time' => Carbon::today()->next('Wednesday')->setTime(18, 0),
                'end_time' => Carbon::today()->next('Wednesday')->setTime(20, 0),
            ]
        );

        // Create demo financial transactions
        Transaction::firstOrCreate(
            ['reference_number' => 'DEMO-INC-001'],
            [
                'type' => 'income',
                'category' => 'Tithes',
                'amount' => 5000.00,
                'payment_method' => 'Cash',
                'transaction_date' => Carbon::today(),
                'description' => 'Sunday service tithes',
                'recorded_by' => $treasurer->id,
            ]
        );

        Transaction::firstOrCreate(
            ['reference_number' => 'DEMO-EXP-001'],
            [
                'type' => 'expense',
                'category' => 'Utilities',
                'amount' => 1500.00,
                'payment_method' => 'Bank Transfer',
                'transaction_date' => Carbon::today()->subDays(2),
                'description' => 'Monthly electricity bill',
                'recorded_by' => $treasurer->id,
            ]
        );

        // Create demo pledge
        if (!empty($members)) {
            Pledge::firstOrCreate(
                ['member_id' => $members[0]->id, 'purpose' => 'Building Fund'],
                [
                    'amount' => 10000.00,
                    'amount_paid' => 3000.00,
                    'start_date' => Carbon::now()->startOfMonth(),
                    'end_date' => Carbon::now()->addMonths(6),
                    'status' => 'active',
                    'created_by' => $pastor->id,
                ]
            );
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('📧 Admin: admin@church.com | Password: password');
        $this->command->info('📧 Pastor: pastor@church.com | Password: password');
        $this->command->info('📧 Treasurer: treasurer@church.com | Password: password');
        $this->command->info('📧 Member: member@church.com | Password: password');
    }
}
