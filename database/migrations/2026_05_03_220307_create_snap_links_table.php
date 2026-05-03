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
        Schema::create('snap_links', function (Blueprint $table) {
        $table->string('album_id', 10)->primary(); // ID Unik (contoh: E3E8A4)
        $table->string('name');
        $table->string('paket');
        $table->text('drive_link');
        $table->string('folder_id');
        $table->string('group_name')->nullable();
        $table->timestamp('expires_at');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snap_links');
    }
};
