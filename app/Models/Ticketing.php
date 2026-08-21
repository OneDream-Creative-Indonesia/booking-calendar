<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticketing extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'email',
        'jumlah',
        'cetak',
        'telpon',
        'transaction_type',
        'queue_number', 
        'status',
        'is_foto',   // <--- PASTIKAN INI ADA
        'is_export', // <--- PASTIKAN INI ADA
        'is_print'   // <--- PASTIKAN INI ADA
    ];
}