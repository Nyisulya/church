<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserMemberSyncTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_automatically_creates_member_profile_on_user_creation()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function it_synchronizes_name_and_email_updates_to_member_profile()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user->update([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    /** @test */
    public function it_does_not_create_member_profile_when_disabled()
    {
        User::$createMemberProfile = false;

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        User::$createMemberProfile = true;

        $this->assertDatabaseMissing('members', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_migrates_existing_users_without_member_profiles()
    {
        // 1. Disable profile creation so we create a user without a member profile
        User::$createMemberProfile = false;
        $user = User::create([
            'name' => 'Legacy User',
            'email' => 'legacy@example.com',
            'password' => bcrypt('password123'),
        ]);
        User::$createMemberProfile = true;

        $this->assertDatabaseMissing('members', [
            'user_id' => $user->id,
        ]);

        // 2. Run the migration to sync
        $migration = require database_path('migrations/2026_07_10_091000_create_member_profiles_for_existing_users.php');
        $migration->up();

        // 3. Assert member profile is created and linked
        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'full_name' => 'Legacy User',
            'email' => 'legacy@example.com',
        ]);
    }
}
