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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('member_number')->unique();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->date('date_of_birth');
            $table->enum('marital_status', ['single', 'married', 'widowed', 'divorced']);
            $table->date('wedding_date')->nullable();
            $table->text('address')->nullable();
            $table->date('salvation_date')->nullable();
            $table->date('baptism_date')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->string('registration_type')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
