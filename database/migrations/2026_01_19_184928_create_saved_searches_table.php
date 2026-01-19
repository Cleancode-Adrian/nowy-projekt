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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // Nazwa zapisanego wyszukiwania
            $table->string('search')->nullable(); // Tekst wyszukiwania
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->decimal('hourly_rate_min', 10, 2)->nullable();
            $table->decimal('hourly_rate_max', 10, 2)->nullable();
            $table->json('tag_ids')->nullable(); // Array of tag IDs
            $table->boolean('is_urgent')->nullable();
            $table->boolean('notify_on_match')->default(true); // Powiadomienia o nowych pasujących ogłoszeniach
            $table->timestamp('last_notified_at')->nullable(); // Ostatnie powiadomienie
            $table->timestamps();

            $table->index('user_id');
            $table->index('notify_on_match');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
