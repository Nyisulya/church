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
        Schema::create('small_group_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('small_group_offering_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('transaction_reference')->nullable(); // Link to main financial system if needed
            $table->timestamp('paid_at')->useCurrent();
            $table->enum('payment_method', ['cash', 'mobile_money', 'system', 'other'])->default('cash');
            $table->foreignId('recorded_by')->nullable()->constrained('users'); // User who recorded the payment
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_group_payments');
    }
};
