<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('pages')->onDelete('cascade');
            $table->string('route_name')->nullable()->after('slug'); // dla linków do istniejących route (np. blog.index, faq)
            $table->string('external_url')->nullable()->after('route_name'); // dla linków zewnętrznych
            $table->string('icon')->nullable()->after('menu_order'); // ikona FontAwesome dla menu
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'route_name', 'external_url', 'icon']);
        });
    }
};

