<?php

namespace App\Http\Controllers;

use App\Models\Package;

class PackageController extends Controller
{
    public static function formatPackages($packages = null)
    {
        Package::ensureExtraColumns();

        $fallbacks = [
            asset('img/group(3).png'),
            asset('img/group(4).png'),
            asset('img/group(2).png'),
            asset('img/group(1).png'),
        ];

        $defaultExtraDuration = 10000;
        try {
            $durAddon = \App\Models\Addon::where('name', 'like', '%durasi%')
                ->orWhere('name', 'like', '%menit%')
                ->first();
            if ($durAddon && (int) $durAddon->price > 0) {
                $defaultExtraDuration = (int) $durAddon->price;
            }
        } catch (\Throwable $e) {
        }

        $items = $packages ?? Package::with(['media', 'backgrounds.media'])->orderBy('id')->get();

        return $items->values()->map(function ($pkg, $idx) use ($fallbacks, $defaultExtraDuration) {
            $coverUrl = null;
            try {
                if (method_exists($pkg, 'getFirstMediaUrl')) {
                    $coverUrl = $pkg->getFirstMediaUrl('image');
                }
            } catch (\Throwable $e) {
                $coverUrl = null;
            }

            $bgList = [];
            if (!empty($pkg->backgrounds)) {
                foreach (collect($pkg->backgrounds)->values() as $bgIdx => $bg) {
                    $bgUrl = null;
                    try {
                        if (method_exists($bg, 'getFirstMediaUrl')) {
                            $bgUrl = $bg->getFirstMediaUrl('image');
                        } elseif (!empty($bg->image_url)) {
                            $bgUrl = $bg->image_url;
                        }
                    } catch (\Throwable $e) {
                        $bgUrl = null;
                    }
                    if (!$coverUrl && $bgUrl) {
                        $coverUrl = $bgUrl;
                    }
                    $bgList[] = [
                        'id' => $bg->id ?? ($bgIdx + 1),
                        'name' => $bg->name ?? ('Background ' . ($bgIdx + 1)),
                        'image_url' => $bgUrl ?: $fallbacks[$bgIdx % count($fallbacks)],
                    ];
                }
            }

            if (!$coverUrl) {
                $coverUrl = $fallbacks[$idx % count($fallbacks)];
            }

            $desc = (string) ($pkg->description ?? '');

            // 1. Untuk berapa orang (included_people)
            $includedPeople = !empty($pkg->included_people) ? (int) $pkg->included_people : 0;
            if ($includedPeople <= 0) {
                if (preg_match('/(?:buat|untuk|include|termasuk)\s*(\d+)\s*orang/i', $desc, $m)) {
                    $includedPeople = (int) $m[1];
                } else {
                    $includedPeople = ((int) $pkg->id === 2) ? 2 : 1;
                }
            }

            // 2. Maksimal orang (max_people)
            $maxPeople = !empty($pkg->max_people) ? (int) $pkg->max_people : 0;
            if ($maxPeople <= 0) {
                if (preg_match('/(?:max(?:imal)?|maks(?:imal)?)\s*(\d+)\s*orang/i', $desc, $mMax)) {
                    $maxPeople = (int) $mMax[1];
                } else {
                    $maxPeople = 8;
                }
            }

            // 3. Tambah durasi per 3 menit (extras_duration)
            $extrasDuration = isset($pkg->extras_duration) && $pkg->extras_duration !== null && $pkg->extras_duration !== ''
                ? (int) $pkg->extras_duration
                : $defaultExtraDuration;

            return [
                'id' => (int) $pkg->id,
                'title' => (string) $pkg->title,
                'description' => $desc,
                'duration_minutes' => (int) ($pkg->duration_minutes ?? 15),
                'price' => (int) ($pkg->price ?? 0),
                'extras_people' => (int) ($pkg->extras_people ?? 0),
                'extras_duration' => $extrasDuration,
                'downpayment' => (int) ($pkg->downpayment ?? 0),
                'included_people' => $includedPeople,
                'max_people' => $maxPeople,
                'backgrounds_count' => count($bgList),
                'image_url' => $coverUrl,
                'backgrounds' => $bgList,
            ];
        });
    }

    public function index()
    {
        try {
            $raw = Package::with(['media', 'backgrounds.media'])->orderBy('id')->get();
            if ($raw->isEmpty()) {
                $raw = \App\Support\Site::bookingPackages();
            }
            return response()->json(self::formatPackages($raw));
        } catch (\Throwable $e) {
            return response()->json(self::formatPackages(\App\Support\Site::bookingPackages()));
        }
    }
}
