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
            $table->foreignId('type_id')->constrained('frame_types')->onDelete('cascade');          
            $table->string('orientation')->default('portrait'); 
            $table->json('masks')->nullable();
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
