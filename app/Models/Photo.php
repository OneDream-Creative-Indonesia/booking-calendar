<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Otomatis hitung ukuran file saat di-create/update
    protected static function booted()
    {
        static::saving(function ($photo) {
            if ($photo->isDirty('file_path') && Storage::disk('public')->exists($photo->file_path)) {
                $photo->file_size = Storage::disk('public')->size($photo->file_path);
            }
        });
    }
}