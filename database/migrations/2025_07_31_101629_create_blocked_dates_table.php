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
        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
             $table->time('start_time');
            $table->time('end_time');
            $table->json('package_ids');
            $table->timestamps();

            $table->unique(['date', 'start_time', 'end_time'], 'blocked_date_time_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_dates');
    }
};
