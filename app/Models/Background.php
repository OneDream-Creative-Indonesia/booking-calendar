<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'name'
    ];
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

}
