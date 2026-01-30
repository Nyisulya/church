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
        Schema::table('announcements', function (Blueprint $table) {
            $table->boolean('is_general')->default(true)->after('department_id'); // Church-wide vs department
            $table->date('announcement_date')->nullable()->after('body'); // When to announce
            $table->boolean('is_active')->default(true)->after('announcement_date'); // Active/inactive
            $table->integer('priority')->default(0)->after('is_active'); // Order of display
            
            // Make department_id nullable for general announcements
            $table->foreignId('department_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['is_general', 'announcement_date', 'is_active', 'priority']);
        });
    }
};
