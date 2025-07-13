<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'price',
        'extras',
    ];
    public function backgrounds()
    {
        return $this->belongsToMany(Background::class);
    }

}
