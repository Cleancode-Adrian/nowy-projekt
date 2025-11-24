<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('path')->unique();
            $table->string('filename');
            $table->string('extension')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->boolean('is_image')->default(false);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('webp_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};

