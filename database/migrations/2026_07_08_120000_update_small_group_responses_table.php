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
        Schema::table('small_group_responses', function (Blueprint $table) {
            // Drop the old foreign keys to change the columns
            // Actually, in PostgreSQL we can just alter columns to drop NOT NULL constraint.
            // Laravel table change() does this automatically.
            $table->unsignedBigInteger('member_id')->nullable()->change();
            $table->unsignedBigInteger('small_group_id')->nullable()->change();
            
            // Drop unique constraint to allow multiple group reports (which have member_id = null)
            // we will handle validation in PHP.
            try {
                $table->dropUnique('unique_member_week_question');
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('small_group_responses', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
            $table->unsignedBigInteger('small_group_id')->nullable(false)->change();
            
            try {
                $table->unique(['member_id', 'week_starting', 'question_id'], 'unique_member_week_question');
            } catch (\Exception $e) {
                // Ignore
            }
        });
    }
};
