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
        Schema::table('announcements', function (Blueprint $table) {
            $table->decimal('hourly_rate_min', 10, 2)->nullable()->after('budget_max');
            $table->decimal('hourly_rate_max', 10, 2)->nullable()->after('hourly_rate_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['hourly_rate_min', 'hourly_rate_max']);
        });
    }
};
