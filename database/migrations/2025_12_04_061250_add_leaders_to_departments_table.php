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
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('chairman_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('secretary_id')->nullable()->constrained('members')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['chairman_id']);
            $table->dropForeign(['secretary_id']);
            $table->dropColumn(['chairman_id', 'secretary_id']);
        });
    }
};
