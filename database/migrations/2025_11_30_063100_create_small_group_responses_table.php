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
        Schema::create('small_group_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('small_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->references('id')->on('small_group_questions')->onDelete('cascade');
            $table->date('week_starting'); // Saturday of the week
            $table->string('response_value')->nullable(); // Stores the answer
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Prevent duplicate responses for same member, week, and question
            $table->unique(['member_id', 'week_starting', 'question_id'], 'unique_member_week_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_group_responses');
    }
};
