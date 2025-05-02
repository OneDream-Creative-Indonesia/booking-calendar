<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('google_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('access_token');
            $table->string('refresh_token')->nullable();
            $table->integer('expires_in');
            $table->string('token_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_credentials');
    }
};
