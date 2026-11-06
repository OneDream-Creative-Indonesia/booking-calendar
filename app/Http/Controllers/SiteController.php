<?php

namespace App\Http\Controllers;

use App\Models\SirkelRegistration;
use App\Models\SiteGalleryItem;
use App\Models\SitePromo;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SiteController extends Controller
{
    public function home()
    {
        Site::ensureSeeded();

        return view('site.home', [
            'promos' => $this->safe(fn () => SitePromo::running()->get(), collect()),
            'packages' => Site::bookingPackages(),
            'operationalHours' => Site::bookingOperationalHours(),
        ]);
    }

    public function about()
    {
        Site::ensureSeeded();

        return view('site.about', ['faqs' => Site::faqs()]);
    }

    public function studio()
    {
        Site::ensureSeeded();

        return view('site.studio', [
            'packages' => Site::bookingPackages(),
            'backgrounds' => Site::bookingBackgrounds(),
            'addons' => Site::bookingAddons(),
            'photoGrids' => Site::bookingPhotoGrids(),
            'keychains' => Site::bookingKeychains(),
            'operationalHours' => Site::bookingOperationalHours(),
            'gallery' => $this->gallery('studio'),
        ]);
    }

    public function booth()
    {
        Site::ensureSeeded();

        return view('site.booth', ['gallery' => $this->gallery('booth')]);
    }

    public function express()
    {
        Site::ensureSeeded();

        return view('site.express');
    }

    public function sirkel()
    {
        Site::ensureSeeded();

        return view('site.sirkel');
    }

    public function sirkelStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'age' => 'nullable|integer|min:10|max:80',
            'city' => 'nullable|string|max:120',
            'whatsapp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'instagram' => 'nullable|string|max:80',
            'tiktok' => 'nullable|string|max:80',
            'reason' => 'nullable|string|max:1000',
        ], [
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid.',
        ]);

        $this->safe(fn () => SirkelRegistration::create($data), null);

        $lines = [
            (string) Site::get('sirkel_wa_intro', 'Halo Pinpin, aku mau daftar jadi Sirkel Snap Fun!'),
            '',
            'Nama: ' . $data['name'],
            'Usia: ' . ($data['age'] ?? '-'),
            'Domisili: ' . ($data['city'] ?? '-'),
            'WhatsApp: ' . $data['whatsapp'],
            'Instagram: ' . ($data['instagram'] ?? '-'),
            'TikTok: ' . ($data['tiktok'] ?? '-'),
            'Alasan gabung: ' . ($data['reason'] ?? '-'),
        ];

        return redirect()->away(Site::wa(implode("\n", $lines)));
    }

    public function customFrameStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'whatsapp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'session_date' => 'required|string|max:60',
            'package_name' => 'nullable|string|max:120',
            'frame_theme' => 'required|string|max:150',
            'frame_text' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:1000',
        ], [
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid.',
        ]);

        $lines = [
            (string) Site::get('custom_frame_wa_intro', 'Halo Pinpin, aku mau registrasi Custom Frame untuk foto di Snap Fun Studio!'),
            '',
            'Nama: ' . $data['name'],
            'WhatsApp: ' . $data['whatsapp'],
            'Tanggal Rencana Foto: ' . $data['session_date'],
            'Paket Studio: ' . ($data['package_name'] ?: '-'),
            'Tema / Konsep Frame: ' . $data['frame_theme'],
            'Tulisan di Frame: ' . ($data['frame_text'] ?: '-'),
            'Catatan Tambahan: ' . ($data['notes'] ?: '-'),
        ];

        return redirect()->away(Site::wa(implode("\n", $lines)));
    }

    public function contact()
    {
        Site::ensureSeeded();

        return view('site.contact');
    }

    protected function gallery(string $group)
    {
        return $this->safe(function () use ($group) {
            if (! Schema::hasTable('site_gallery_items')) {
                return collect(Site::defaultGalleryItems())
                    ->where('group', $group)
                    ->map(fn ($item) => (object) $item)
                    ->values();
            }

            return SiteGalleryItem::where('group', $group)
                ->where('is_active', true)
                ->orderBy('sort')
                ->orderBy('id')
                ->get();
        }, collect(Site::defaultGalleryItems())->where('group', $group)->map(fn ($item) => (object) $item)->values());
    }

    /** Halaman tetap tampil walau tabel CMS belum dimigrasi. */
    protected function safe(callable $fn, $fallback)
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            report($e);

            return $fallback;
        }
    }
}
