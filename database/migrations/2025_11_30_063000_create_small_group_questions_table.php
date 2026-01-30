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
        Schema::create('small_group_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_sw'); // Question in Swahili
            $table->text('question_en'); // Question in English
            $table->enum('response_type', ['number', 'yes_no', 'text', 'amount'])->default('text');
            $table->enum('category', ['evangelism', 'bible_study', 'community_service', 'other'])->default('other');
            $table->integer('order')->default(0); // For ordering questions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_group_questions');
    }
};
