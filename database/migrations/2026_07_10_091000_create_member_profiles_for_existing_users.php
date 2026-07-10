<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use App\Models\Member;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Temporarily disable the model observer/events if it's already active,
        // although it's not active yet. Just to be safe:
        if (property_exists(User::class, 'createMemberProfile')) {
            User::$createMemberProfile = false;
        }

        $users = User::all();

        foreach ($users as $user) {
            // Check if member already exists by user_id or email
            $exists = Member::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->exists();

            if (!$exists) {
                Member::create([
                    'user_id' => $user->id,
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'status' => 'active',
                ]);
            } else {
                // If member exists by email but user_id is null, link them!
                $member = Member::where('email', $user->email)->whereNull('user_id')->first();
                if ($member) {
                    $member->update(['user_id' => $user->id]);
                }
            }
        }

        if (property_exists(User::class, 'createMemberProfile')) {
            User::$createMemberProfile = true;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback required for data migration
    }
};
