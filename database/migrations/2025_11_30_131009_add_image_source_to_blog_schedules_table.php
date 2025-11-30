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
        Schema::table('blog_schedules', function (Blueprint $table) {
            $table->enum('image_source', ['unsplash', 'dalle3'])->default('unsplash')->after('download_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_schedules', function (Blueprint $table) {
            $table->dropColumn('image_source');
        });
    }
};
