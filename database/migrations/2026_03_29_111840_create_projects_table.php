<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique(); // ex: CA1489
            $table->string('type'); // ex: PHOTOBOX, SELF PHOTO
            $table->string('name'); // ex: Desvitha
            $table->dateTime('expired_at')->nullable(); // Untuk hitung sisa waktu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
