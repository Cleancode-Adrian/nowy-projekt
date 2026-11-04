<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Lead (Meta) / generate_lead (GA4) wysłany - jeden na zlecenie.
            $table->timestamp('lead_tracked_at')->nullable();
            // client_id z ciasteczka _ga w chwili wysłania zlecenia - do zdarzeń
            // wysyłanych później z serwera (GA4 Measurement Protocol).
            $table->string('ga_client_id', 64)->nullable();
            // qualify_lead (akceptacja przez admina) wysłany - jeden na zlecenie.
            $table->timestamp('qualify_tracked_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['lead_tracked_at', 'ga_client_id', 'qualify_tracked_at']);
        });
    }
};
