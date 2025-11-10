<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade'); // kto ocenia
            $table->foreignId('rated_id')->constrained('users')->onDelete('cascade'); // kto jest oceniany
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['announcement_id', 'rater_id', 'rated_id']);
            $table->index('rated_id');
        });

        // Dodaj kolumny do users dla średniej oceny
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('average_rating', 3, 2)->default(0)->after('bio');
            $table->integer('ratings_count')->default(0)->after('average_rating');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['average_rating', 'ratings_count']);
        });

        Schema::dropIfExists('ratings');
    }
};
