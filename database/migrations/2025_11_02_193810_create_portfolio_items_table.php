<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable(); // main image
            $table->json('images')->nullable(); // gallery
            $table->string('url')->nullable(); // link do projektu
            $table->json('technologies')->nullable(); // ['React', 'Laravel', ...]
            $table->date('completed_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
