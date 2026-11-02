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
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'video_path')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('video_path')->nullable()->after('gdrive_folder_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'video_path')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('video_path');
            });
        }
    }
};

