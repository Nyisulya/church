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
            $table->foreignId('pledge_id')->nullable()->constrained('pledges')->nullOnDelete();
            $table->foreignId('small_group_offering_id')->nullable()->constrained('small_group_offerings')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['pledge_id']);
            $table->dropColumn('pledge_id');
            $table->dropForeign(['small_group_offering_id']);
            $table->dropColumn('small_group_offering_id');
        });
    }
};
