<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnapLink extends Model
{
    use HasFactory;
    protected $primaryKey = 'album_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}
