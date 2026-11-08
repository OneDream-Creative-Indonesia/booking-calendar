<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteGalleryItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('site_gallery_studio');
            Cache::forget('site_gallery_booth');
        });
        static::deleted(function () {
            Cache::forget('site_gallery_studio');
            Cache::forget('site_gallery_booth');
        });
    }
}
