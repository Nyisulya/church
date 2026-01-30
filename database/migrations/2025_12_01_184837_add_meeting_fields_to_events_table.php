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
        Schema::table('events', function (Blueprint $table) {
            $table->text('agenda')->nullable()->after('end_time');
            $table->text('minutes')->nullable()->after('agenda');
            $table->string('location')->nullable()->after('minutes');
            $table->boolean('is_recurring')->default(false)->after('location');
            $table->string('recurrence_pattern')->nullable()->after('is_recurring'); // 'weekly', 'monthly', etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
