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
        Schema::create('small_group_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('small_group_id')->constrained()->onDelete('cascade');
            $table->dateTime('meeting_date');
            $table->string('topic')->nullable();
            $table->text('notes')->nullable();
            $table->integer('attendees_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_group_meetings');
    }
};
