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
    Schema::table('ticketings', function (Blueprint $table) {
        $table->foreignId('event_id')->nullable()->after('id')->constrained('events')->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('ticketings', function (Blueprint $table) {
        $table->dropForeign(['event_id']);
        $table->dropColumn('event_id');
    });
}
};
