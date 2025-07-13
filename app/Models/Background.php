<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Background extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'name'
    ];
     public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }

}
