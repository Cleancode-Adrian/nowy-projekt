<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('announcement_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['announcement_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_tag');
        Schema::dropIfExists('tags');
    }
};

