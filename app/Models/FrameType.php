<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * Relasi ke FrameSetting
     */
    public function frames()
    {
        return $this->hasMany(FrameSetting::class, 'type_id');
    }
}