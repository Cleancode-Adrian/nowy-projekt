<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // TL;DR - punkty rozdzielone znakiem nowej linii, renderowane jako lista.
            $table->text('tldr')->nullable()->after('excerpt');

            // Data ostatniej zmiany TREŚCI. updated_at nie nadaje się do pokazania
            // czytelnikowi ani do sitemapy, bo podbijał ją każdy licznik wyświetleń.
            $table->timestamp('content_updated_at')->nullable()->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['tldr', 'content_updated_at']);
        });
    }
};
