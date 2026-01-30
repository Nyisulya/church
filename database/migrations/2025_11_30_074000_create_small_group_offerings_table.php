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
        Schema::create('small_group_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('small_group_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Emergency Fund"
            $table->text('description')->nullable();
            $table->decimal('amount_per_member', 12, 2)->nullable(); // If null, voluntary amount
            $table->decimal('target_amount', 12, 2)->nullable(); // Total group goal
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('members'); // Leader who created it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_group_offerings');
    }
};
