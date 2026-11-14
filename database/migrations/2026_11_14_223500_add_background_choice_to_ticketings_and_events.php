<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'is_background_active')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('is_background_active')->default(true)->after('additional_price');
            });
        }

        if (Schema::hasTable('ticketings') && !Schema::hasColumn('ticketings', 'bg_choice')) {
            Schema::table('ticketings', function (Blueprint $table) {
                $table->unsignedTinyInteger('bg_choice')->nullable()->after('additional');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'is_background_active')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('is_background_active');
            });
        }

        if (Schema::hasTable('ticketings') && Schema::hasColumn('ticketings', 'bg_choice')) {
            Schema::table('ticketings', function (Blueprint $table) {
                $table->dropColumn('bg_choice');
            });
        }
    }
};
