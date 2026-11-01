<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Ticketing extends Model
{
    protected $fillable = [
        'event_id',
        'nama',
        'email',
        'jumlah',
        'cetak',
        'telpon',
        'transaction_type',
        'queue_number',
        'status',
        'is_foto',
        'is_export',
        'is_print',
        'total_price',
    ];
    
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}