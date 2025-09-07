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
}
