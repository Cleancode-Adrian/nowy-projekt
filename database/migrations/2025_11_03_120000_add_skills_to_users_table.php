<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'experience_level')) {
                $table->string('experience_level')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'github_url')) {
                $table->string('github_url')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_approved');
            }
            if (!Schema::hasColumn('users', 'verification_document')) {
                $table->string('verification_document')->nullable()->after('is_approved');
            }
            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('is_approved');
            }
            if (!Schema::hasColumn('users', 'completed_projects')) {
                $table->integer('completed_projects')->default(0)->after('bio');
            }
            if (!Schema::hasColumn('users', 'profile_views')) {
                $table->integer('profile_views')->default(0)->after('bio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['skills', 'experience_level', 'website', 'linkedin_url', 'github_url',
                        'is_verified', 'verification_document', 'verified_at',
                        'completed_projects', 'profile_views'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
