<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteFaq extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site_faqs_active'));
        static::deleted(fn () => Cache::forget('site_faqs_active'));
    }
}
