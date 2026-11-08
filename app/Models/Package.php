<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Package extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'price',
        'extras_people',
        'extras_duration',
        'included_people',
        'max_people',
        'downpayment',
    ];

    public static function ensureExtraColumns(): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $checked = true;

        try {
            if (!Schema::hasTable('packages')) {
                return;
            }
            Schema::table('packages', function (Blueprint $table) {
                if (!Schema::hasColumn('packages', 'included_people')) {
                    $table->integer('included_people')->nullable()->after('duration_minutes');
                }
                if (!Schema::hasColumn('packages', 'max_people')) {
                    $table->integer('max_people')->nullable()->after('included_people');
                }
                if (!Schema::hasColumn('packages', 'extras_duration')) {
                    $table->integer('extras_duration')->nullable()->after('extras_people');
                }
            });
        } catch (\Throwable $e) {
            // Abaikan jika koneksi DB belum siap
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function backgrounds()
    {
        return $this->belongsToMany(Background::class);
    }
}
