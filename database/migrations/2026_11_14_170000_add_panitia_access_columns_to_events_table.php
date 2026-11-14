<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'panitia_pin')) {
                    $table->string('panitia_pin', 32)->nullable()->after('video_path');
                }
                if (!Schema::hasColumn('events', 'revenue_share_percent')) {
                    $table->decimal('revenue_share_percent', 5, 2)->default(0)->after('additional_price');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'panitia_pin')) {
                    $table->dropColumn('panitia_pin');
                }
                if (Schema::hasColumn('events', 'revenue_share_percent')) {
                    $table->dropColumn('revenue_share_percent');
                }
            });
        }
    }
};
