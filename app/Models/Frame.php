<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Frame extends Model implements HasMedia
{
      use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
    ];
     public function registerMediaCollections(): void
    {
        $this->addMediaCollection('frames')
            ->singleFile(); 
    }
    public function photoOrders()
    {
        return $this->hasMany(PhotoOrder::class);
    }
}
