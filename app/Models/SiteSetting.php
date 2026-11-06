<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Pengaturan konten website (key => value). Nilai array disimpan sebagai JSON.
 */
class SiteSetting extends Model
{
    protected $guarded = ['id'];

    public static function all_(): array
    {
        return Cache::rememberForever('site_settings', function () {
            try {
                return static::query()->pluck('value', 'key')->map(function ($v) {
                    $d = json_decode((string) $v, true);
                    return json_last_error() === JSON_ERROR_NONE ? $d : $v;
                })->all();
            } catch (\Throwable $e) {
                return []; // tabel belum dimigrasi
            }
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $v = static::all_()[$key] ?? null;

        return ($v === null || $v === '' || $v === []) ? $default : $v;
    }

    public static function put(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
        Cache::forget('site_settings');
    }
}
