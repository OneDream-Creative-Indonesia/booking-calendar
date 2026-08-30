<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_event',
        'slug', // INI DITAMBAHIN
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