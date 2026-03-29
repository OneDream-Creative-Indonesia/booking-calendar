<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FrameSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'type_id',
        'orientation',
        'masks',
    ];

    protected $casts = [
        'masks' => 'array',
    ];

    /**
     * Relasi ke FrameType
     */
    public function type()
    {
        return $this->belongsTo(FrameType::class, 'type_id');
    }

    /**
     * MediaLibrary collection
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('framesSettings')
             ->singleFile();
    }
}