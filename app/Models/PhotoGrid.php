<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoGrid extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
    ];
    public function frames()
    {
        return $this->belongsToMany(Frame::class, 'photo_grid_frame');
    }

}
