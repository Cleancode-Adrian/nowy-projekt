<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'verification_status')) {
                $table->enum('verification_status', ['none', 'pending', 'approved', 'rejected'])
                    ->default('none')
                    ->after('verification_document');
            }
            if (!Schema::hasColumn('users', 'verification_note')) {
                $table->string('verification_note')->nullable()->after('verification_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'verification_note']);
        });
    }
};
