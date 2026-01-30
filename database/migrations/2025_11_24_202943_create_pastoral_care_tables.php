<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Visits table
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('visit_type', ['home', 'hospital', 'office', 'phone_call']);
            $table->date('visit_date');
            $table->text('purpose');
            $table->text('notes')->nullable();
            $table->text('outcome')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['member_id', 'visit_date']);
        });

        // Follow-ups table
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->date('due_date');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['assigned_to', 'status', 'due_date']);
        });

        // Prayer requests table
        Schema::create('prayer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->text('request');
            $table->date('request_date');
            $table->enum('status', ['active', 'answered', 'ongoing'])->default('active');
            $table->text('answer')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->boolean('is_private')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('visits');
    }
};
