<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // freelancer
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('PLN');
            $table->integer('delivery_days');
            $table->text('description');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['announcement_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
