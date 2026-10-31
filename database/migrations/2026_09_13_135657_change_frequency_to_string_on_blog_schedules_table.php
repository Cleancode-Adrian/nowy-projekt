<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Zamiana enum -> string, żeby dodawanie nowych częstotliwości (np. "co 2 dni")
     * nie wymagało kolejnej migracji przebudowującej CHECK constraint.
     */
    public function up(): void
    {
        Schema::table('blog_schedules', function (Blueprint $table) {
            $table->string('frequency')->default('daily')->change();
        });
    }

    public function down(): void
    {
        Schema::table('blog_schedules', function (Blueprint $table) {
            $table->enum('frequency', ['daily', 'twice_daily', 'weekly', 'weekdays'])->default('daily')->change();
        });
    }
};
