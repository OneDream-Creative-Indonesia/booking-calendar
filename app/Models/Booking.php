<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $casts = [
        'confirmation' => 'boolean',
    ];

    protected $fillable = ['name', 'whatsapp', 'people_count', 'date', 'time', 'package', 'package_id','email','confirmation','status'];

    public function package()
    {
        return $this->belongsTo(\App\Models\Package::class);
    }
}
