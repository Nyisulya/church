<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop Stripe specific columns
            $table->dropColumn(['stripe_id', 'payment_intent_id']);

            // Add Flutterwave/Mobile Money specific columns
            $table->string('transaction_id')->nullable()->after('member_id'); // Flutterwave Transaction ID
            $table->string('reference')->unique()->after('transaction_id'); // Our unique reference
            $table->string('phone_number')->after('amount');
            $table->string('network')->nullable()->after('phone_number'); // Vodacom, Airtel, Tigo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'reference', 'phone_number', 'network']);
            $table->string('stripe_id');
            $table->string('payment_intent_id')->nullable();
        });
    }
};
