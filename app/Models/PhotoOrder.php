<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class PhotoOrder extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['name','status', 'type', 'layout_image','warna'];
}
