<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->string('budget_currency')->default('PLN');
            $table->string('deadline')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'closed'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('proposals_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};

