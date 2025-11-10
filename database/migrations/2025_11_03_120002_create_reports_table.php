<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->string('reportable_type'); // App\Models\User, App\Models\Announcement, etc.
            $table->unsignedBigInteger('reportable_id');
            $table->enum('reason', ['spam', 'inappropriate', 'fraud', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

