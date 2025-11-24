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
        Schema::table('tags', function (Blueprint $table) {
            $table->enum('type', ['announcement', 'blog'])->default('announcement')->after('slug');
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
        });
        
        // Ustaw typ 'announcement' dla wszystkich istniejących tagów
        \DB::table('tags')->update(['type' => 'announcement']);
        
        // Dodaj unikalność dla name i slug w obrębie typu
        Schema::table('tags', function (Blueprint $table) {
            $table->unique(['name', 'type']);
            $table->unique(['slug', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(['name', 'type']);
            $table->dropUnique(['slug', 'type']);
            $table->dropColumn('type');
            $table->unique('name');
            $table->unique('slug');
        });
    }
};
