<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'photos' => 'array', // Beritahu Laravel ini adalah array JSON
        'expired_at' => 'datetime',
    ];

    // FUNGSI RELASI photos() HARUS DIHAPUS AGAR TIDAK BENTROK

    // Helper untuk menampilkan sisa waktu
    public function getTimeRemainingAttribute()
    {
        if (!$this->expired_at) return 'Tidak ada batas';
        $diff = $this->expired_at->diff(now());
        return $diff->d . ' HARI ' . $diff->h . ' JAM';
    }
}