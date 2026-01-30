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
        Schema::create('small_group_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('small_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['member', 'co-leader'])->default('member');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['small_group_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_group_member');
    }
};
