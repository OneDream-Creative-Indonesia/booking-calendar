<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_voucher',
        'discount_percent',
        'is_active',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count'
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function getSafeDiscountPercentAttribute()
    {
        return max(0, min($this->discount_percent ?? 0, 100));
    }

    public function setDiscountPercentAttribute($value)
    {
        $this->attributes['discount_percent'] = max(0, min($value, 100));
    }
    public function calculateDiscountAmount($originalPrice)
    {
        return intval(round($originalPrice * ($this->safe_discount_percent / 100)));
    }

    /**
     * Cek apakah voucher valid
     */
    public function isValid()
    {
        $now = Carbon::now()->toDateString();

        // Cek apakah voucher aktif
        if (!$this->is_active) {
            return false;
        }

        // Cek tanggal mulai
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        // Cek tanggal berakhir
        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        // Cek limit penggunaan
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Increment used count
     */
    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    /**
     * Relasi dengan booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'voucher_id');
    }
}
