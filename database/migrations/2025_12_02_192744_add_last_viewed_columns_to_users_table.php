<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_viewed_birthdays_at')->nullable();
            $table->timestamp('last_viewed_announcements_at')->nullable();
            $table->timestamp('last_viewed_roster_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_viewed_birthdays_at',
                'last_viewed_announcements_at',
                'last_viewed_roster_at'
            ]);
        });
    }
};
