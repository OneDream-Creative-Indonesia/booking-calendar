<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    
    // Ganti guarded dengan fillable dan daftarkan semua nama kolomnya
    protected $fillable = [
        'nama_event',
        'tanggal_event',
        'is_active',
        'gdrive_folder_id',
    ];

    protected $casts = [
        'tanggal_event' => 'date',
        'is_active' => 'boolean',
    ];

    public function ticketings(): HasMany
    {
        return $this->hasMany(Ticketing::class);
    }
}