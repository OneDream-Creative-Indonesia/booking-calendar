<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $casts = [
        'price' => 'decimal:2',
        'price_notes' => 'decimal:2',
        'confirmation' => 'boolean',
    ];

    protected $fillable = ['name', 'whatsapp', 'people_count', 'date', 'time', 'package', 'package_id','email','confirmation','status', 'background_id','price', 'voucher_code', 'voucher_id', 'keychain_notes', 'price_notes'];

    public function package()
    {
        return $this->belongsTo(\App\Models\Package::class);
    }
    public function background()
    {
        return $this->belongsTo(\App\Models\Background::class);
    }
    public function voucher()
    {
        return $this->belongsTo(\App\Models\Voucher::class);
    }

}
