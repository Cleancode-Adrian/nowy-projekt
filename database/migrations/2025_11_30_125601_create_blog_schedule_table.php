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
        Schema::create('blog_schedules', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false);
            $table->string('time')->default('09:00'); // Format HH:mm
            $table->enum('frequency', ['daily', 'twice_daily', 'weekly', 'weekdays'])->default('daily');
            $table->integer('count')->default(1); // Liczba wpisów na raz
            $table->text('topics')->nullable(); // Tematy oddzielone nową linią
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('tags')->nullable(); // Tagi oddzielone przecinkami
            $table->boolean('download_image')->default(true);
            $table->boolean('auto_publish')->default(true); // false = szkic
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_schedule');
    }
};
