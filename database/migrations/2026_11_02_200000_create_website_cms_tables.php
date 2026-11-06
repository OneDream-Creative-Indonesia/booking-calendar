<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('site_promos')) {
            Schema::create('site_promos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('badge')->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->string('link_label')->nullable();
                $table->string('link_url')->nullable();
                $table->date('starts_at')->nullable();
                $table->date('ends_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('site_gallery_items')) {
            Schema::create('site_gallery_items', function (Blueprint $table) {
                $table->id();
                $table->string('group')->default('studio'); // studio | booth
                $table->string('image');
                $table->string('caption')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('site_faqs')) {
            Schema::create('site_faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sirkel_registrations')) {
            Schema::create('sirkel_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedSmallInteger('age')->nullable();
                $table->string('city')->nullable();
                $table->string('whatsapp');
                $table->string('instagram')->nullable();
                $table->string('tiktok')->nullable();
                $table->text('reason')->nullable();
                $table->string('status')->default('baru'); // baru | dihubungi | diterima | ditolak
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach (['sirkel_registrations', 'site_faqs', 'site_gallery_items', 'site_promos', 'site_settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
