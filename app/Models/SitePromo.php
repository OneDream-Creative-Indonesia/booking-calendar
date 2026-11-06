<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SitePromo extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean', 'starts_at' => 'date', 'ends_at' => 'date'];

    /** Promo aktif dan sedang berlaku (tanggal kosong = tanpa batas). */
    public function scopeRunning(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()->toDateString()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString()))
            ->orderBy('sort')->orderByDesc('id');
    }
}
