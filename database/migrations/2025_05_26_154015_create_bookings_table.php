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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->foreignId('background_id')->nullable()->constrained('backgrounds');
            $table->integer('price')->nullable();
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->foreign('voucher_id')->references('id')->on('vouchers')->nullOnDelete();
            $table->string('name');
            $table->string('whatsapp');
            $table->integer('people_count');
            $table->enum('status', ['pending', 'confirmed', 'cancelled','success'])->default('pending');
            $table->boolean('confirmation')->default(false);
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
