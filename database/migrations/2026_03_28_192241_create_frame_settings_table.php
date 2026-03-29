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
        Schema::create('frame_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');             
            $table->string('orientation')->default('portrait'); 
            $table->json('masks')->nullable();
            $table->string('folder_name')->after('name');
            $table->string('paper_size')->after('folder_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frame_settings');
    }
};
