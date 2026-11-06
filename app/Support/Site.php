<?php

namespace App\Support;

use App\Models\Addon;
use App\Models\Background;
use App\Models\Keychain;
use App\Models\OperationalHour;
use App\Models\Package;
use App\Models\PhotoGrid;
use App\Models\SiteFaq;
use App\Models\SiteGalleryItem;
use App\Models\SitePromo;
use App\Models\SiteSetting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Pusat akses konten website utama Snap Fun Studio.
 * Terintegrasi langsung dengan database Booking Website (Package, Background, Addon, PhotoGrid, Keychain, OperationalHour)
 * tanpa bentrok dengan tabel maupun menu Operational Booking.
 */
class Site
{
    public static function defaults(): array
    {
        return [
            // === 1. UMUM, NAVIGASI & FOOTER ===
            'site_name' => 'Snap Fun Studio',
            'site_company' => 'PT One Dream Creative',
            'site_title' => 'Snap Fun Studio - Self Photo Studio, Photobooth & Event di Serang',
            'site_description' => 'Snap Fun Studio: self photo studio, photobox, dan photobooth event di Kota Serang, Banten. Bebas pencet shutter sendiri bareng Pinpin!',
            'nav_logo' => null,
            'footer_logo' => null,
            'nav_home_label' => 'Beranda',
            'nav_about_label' => 'Tentang',
            'nav_product_label' => 'Produk',
            'nav_studio_label' => 'Snap Fun Studio',
            'nav_booth_label' => 'Snap Fun Booth',
            'nav_express_label' => 'Snap Fun Express',
            'nav_sirkel_label' => 'Daftar Jadi Sirkel',
            'nav_contact_label' => 'Kontak Pinpin',
            'nav_booking_label' => 'Booking',
            'nav_booking_url' => '/booking',

            'wa_number' => '6285117607254',
            'contact_person_label' => 'Snap Fun (Pinpin)',
            'instagram' => 'snapfunstudio',
            'tiktok' => 'snapfunstudio',
            'email' => '',
            'operational_hours' => 'Setiap Hari, 10.00 - 21.00 WIB',
            'address' => 'Kota Serang, Banten',
            'maps_url' => '',
            'maps_embed_url' => '',
            'wa_float_enabled' => true,
            'wa_float_text' => 'Halo Pinpin, aku mau tanya tentang Snap Fun!',

            'footer_tagline' => 'Self Photo Studio, Photobox & Photobooth. Dari Kota Serang untuk semua momen serumu.',
            'footer_subtext' => 'By PT One Dream Creative',
            'footer_col1_title' => 'Jelajah',
            'footer_col2_title' => 'Produk',
            'footer_col3_title' => 'Hubungi Pinpin',
            'footer_copyright' => 'Snap Fun Studio - PT One Dream Creative. All rights reserved.',

            // === 2. BERANDA ===
            'hero_eyebrow' => 'Self Photo Studio · Photobox · Photobooth',
            'hero_title' => 'Abadikan momen serumu, bareng Pinpin!',
            'hero_subtitle' => 'Self Photo Studio, Photobox, dan Photobooth untuk event di Kota Serang, Banten. Bebas pencet shutter sendiri, pamer ekspresi paling gokil, dan pegang kendali penuh atas keseruannya.',
            'hero_badge' => 'Fun!',
            'hero_image' => null,
            'hero_btn1_label' => 'Booking Sekarang',
            'hero_btn1_url' => '/booking',
            'hero_btn2_label' => 'Collab Bareng Kami',
            'hero_btn2_wa_text' => 'Halo Pinpin, aku mau collab / kerja sama bareng Snap Fun!',
            'hero_highlights' => [
                ['title' => '100% Self-Shutter', 'text' => 'Bebas jepret sendiri tanpa rasa canggung'],
                ['title' => 'Standar Studio', 'text' => 'Lighting proper, wajah auto glowing & jernih'],
                ['title' => 'Cetak Instan', 'text' => 'Hasil cetak tajam, tebal & anti luntur'],
            ],

            'home_promo_eyebrow' => 'Promo Spesial',
            'home_promo_title' => 'Lagi ada promo nih!',
            'home_promo_subtitle' => 'Jangan lewatkan penawaran spesial buat seru-seruan bareng bestie atau orang tersayang.',
            'home_promo_empty' => 'Promo terbaru Snap Fun diumumkan di Instagram.',

            // === HIGHLIGHT CUSTOM FRAME STUDIO (BERANDA & HALAMAN STUDIO) ===
            'custom_frame_eyebrow' => 'Highlight · Snap Fun Studio',
            'custom_frame_badge' => 'Custom Frame',
            'custom_frame_title' => 'Bikin Custom Frame Sesuai Gayamu di Snap Fun Studio!',
            'custom_frame_subtitle' => 'Mau rayain ulang tahun, graduation, anniversary, event komunitas, atau comeback idol favorit pakai desain frame eksklusif? Daftarkan konsep Custom Frame kamu sebelum sesi foto di Snap Fun Studio!',
            'custom_frame_image' => null,
            'custom_frame_btn_label' => 'Registrasi Custom Frame',
            'custom_frame_points' => [
                ['title' => 'Bebas Tentukan Tema & Konsep', 'text' => 'Bisa request tema Birthday, Graduation, Idol/Fandom, Couple, atau konsep geng bestie kamu.'],
                ['title' => 'Dibantu Tim Desain Snap Fun', 'text' => 'Desain frame eksklusif dikerjakan oleh tim kreatif Snap Fun atau gunakan desain karyamu sendiri.'],
                ['title' => 'Siap Pakai Saat Sesi Foto', 'text' => 'Frame custom kamu langsung terpasang di studio saat jadwal sesi fotomu tiba.'],
            ],
            'custom_frame_modal_title' => 'Registrasi Custom Frame Studio',
            'custom_frame_modal_subtitle' => 'Isi detail konsep frame yang kamu inginkan di bawah ini. Setelah klik kirim, datamu akan langsung diteruskan ke WhatsApp Admin Pinpin.',
            'custom_frame_modal_btn_label' => 'Kirim Registrasi ke WhatsApp',
            'custom_frame_wa_intro' => 'Halo Pinpin, aku mau registrasi Custom Frame untuk foto di Snap Fun Studio!',

            'home_products_eyebrow' => 'Layanan Snap Fun',
            'home_products_title' => '3 Cara Seru Bareng Snap Fun',
            'home_products_subtitle' => 'Mulai dari studio eksklusif di Kota Serang, photobooth event, hingga photobox express.',
            'studio_tag' => 'Kota Serang',
            'studio_card_title' => 'Snap Fun Studio',
            'studio_summary' => 'Markas utama keseruan kita yang hadir eksklusif di Kota Serang! Safe space kamu buat berekspresi tanpa batas lewat layanan Self Photo Studio dan Photobox.',
            'studio_more_label' => 'Lihat Studio',
            'booth_tag' => 'Untuk Event',
            'booth_card_title' => 'Snap Fun Booth',
            'booth_summary' => 'Bawa keseruan Snap Fun langsung ke acaramu! Standar kualitas tinggi, lighting proper, dan hasil jernih ala self photo studio di acara sekolah, kampus, sampai konser.',
            'booth_more_label' => 'Lihat Booth',
            'express_tag' => 'Segera Hadir',
            'express_card_title' => 'Snap Fun Express',
            'express_summary' => 'Kejutan baru dengan konsep yang lebih compact, praktis, dan 100% self-service di tempat nongkrongmu. Tinggal masuk, jepret, dan cetak!',
            'express_more_label' => 'Lihat Express',

            'home_why_eyebrow' => 'Kenapa Harus Snap Fun?',
            'home_why_title' => 'Bikin foto jadi seru & anti awkward',
            'home_why_subtitle' => 'Playground kreatif tempat kamu pegang kendali penuh atas keseruannya.',
            'why_us_image' => null,
            'why_us' => [
                ['title' => 'Standar Studio', 'text' => 'Equipment yang kami pakai itu standar studio. Wajah auto glowing, nggak bakal gelap atau grainy.'],
                ['title' => 'Hasil Cetak Berkualitas', 'text' => 'Hasil cetak berkualitas, tajam, tebal, dan anti luntur.'],
                ['title' => 'Background Beragam', 'text' => 'Pilihan background beragam: ada tirai merah, tirai abu, tirai biru, American School, dan banyak lagi.'],
                ['title' => 'Frame Custom', 'text' => 'Layout/frame foto bakal kami desain khusus ngikutin tema event atau sesi spesial kalian. Jadi lebih memorable.'],
                ['title' => 'Crew Asik', 'text' => 'Crewnya asik-asik banget. Nggak cuma namanya doang yang Fun, tapi crew Snap Fun semuanya asik banget.'],
            ],

            'home_events_eyebrow' => 'Event Bersama Snap Fun',
            'home_events_title' => 'Event-event ini udah percaya sama Pinpin loh!!',
            'home_events_more_chip' => '...and many more!',
            'home_events_btn_label' => 'Lihat Snap Fun Booth',
            'events' => [
                ['name' => 'Banten Creative Fest Ramadhan', 'logo' => 'img/site/event-bcf.png'],
                ['name' => 'Civil Festival 2025', 'logo' => 'img/site/event-civil.png'],
                ['name' => 'Vintage - LKBB XI Sang Legenda', 'logo' => 'img/site/event-vintage.png'],
                ['name' => 'Sutorito Matsuri', 'logo' => 'img/site/event-sutorito.png'],
                ['name' => 'Kota Serang Fair 2026', 'logo' => 'img/site/event-ksf.png'],
                ['name' => 'PKKMB Untirta 2026', 'logo' => 'img/site/event-pkkmb.png'],
                ['name' => 'Crestara 2026', 'logo' => 'img/site/event-crestara.png'],
                ['name' => 'Crystalin Run Xperience 2026', 'logo' => 'img/site/event-crystalin.png'],
            ],

            'home_sirkel_eyebrow' => 'Jadi Bagian dari Kami',
            'home_sirkel_title' => 'Gabung Sirkel Snap Fun',
            'home_sirkel_btn_label' => 'Daftar Jadi Sirkel',
            'home_visit_title' => 'Kunjungi Studio Kami',
            'home_visit_subtitle' => 'Yuk mampir dan abadikan tawa lepas bareng orang tersayang di markas Pinpin!',
            'home_visit_maps_label' => 'Buka Google Maps',
            'home_visit_contact_label' => 'Kontak Pinpin',

            // === 3. TENTANG ===
            'about_eyebrow' => 'Tentang Kami',
            'about_title' => 'Tentang Snap Fun',
            'about_subtitle' => 'By PT One Dream Creative - Kota Serang, Banten',
            'about_text' => "Pernah ngerasa sayang banget kalau momen seru bareng orang tersayang cuma lewat gitu aja? Berawal dari situ, Snap Fun Studio yang lahir di bawah naungan PT One Dream Creative di Kota Serang, Banten hadir buat jadi partner seru-seruanmu!\n\nKami sadar, kadang difotoin orang itu bikin awkward dan mati gaya. Makanya, kami menciptakan playground kreatif lewat pengalaman Self Photo Studio, Photobox, dan layanan Photobooth. Di sini, nggak ada aturan pose yang kaku. Kamu bebas pencet shutter sendiri, pamer ekspresi paling gokil, dan pegang kendali penuh atas keseruannya.\n\nBagi kami, setiap lembar foto adalah kapsul waktu yang menyimpan cerita seru di baliknya. Yuk, abadikan tawa lepas dan momen paling spontanmu bareng Snap Fun Studio! Karena kami percaya, kenangan terbaik itu nggak perlu disutradarai, cukup dirayakan dengan bahagia.",
            'about_image' => null,
            'about_products_eyebrow' => 'Produk Kami',
            'about_products_title' => 'Layanan Snap Fun',
            'about_why_eyebrow' => 'Keunggulan Kami',
            'about_why_title' => 'Kenapa Harus Snap Fun?',
            'about_faq_eyebrow' => 'FAQ',
            'about_faq_title' => 'Pertanyaan yang Sering Ditanyakan',
            'about_faq_prompt' => 'Masih penasaran atau punya pertanyaan lain?',
            'about_faq_cta_label' => 'Tanya Pinpin langsung via WhatsApp',
            'about_faq_wa_text' => 'Halo Pinpin, aku mau tanya tentang Snap Fun...',

            // === 4. SNAP FUN STUDIO (DATA PAKET, BACKGROUND, ADDONS DIAMBIL DARI DATABASE BOOKING) ===
            'studio_eyebrow' => 'Produk · Kota Serang',
            'studio_title' => 'Snap Fun Studio',
            'studio_text' => 'Markas utama keseruan kita yang saat ini hadir eksklusif di Kota Serang! Snap Fun Studio adalah safe space kamu buat berekspresi tanpa batas lewat layanan Self Photo Studio dan Photobox. Nggak perlu jaim atau canggung difotoin orang, karena kamu sendiri yang pegang shutter-nya. Mau gaya cool, estetik, sampai pose paling absurd bareng bestie, semua bebas kamu lakuin di sini.',
            'studio_cta_label' => 'Booking Sekarang',
            'studio_cta_url' => '/booking',
            'studio_hero_image' => null,
            'studio_service_eyebrow' => 'Paket & Layanan Studio',
            'studio_service_title' => 'Pilih Paket Foto Seru Kamu',
            'studio_service_subtitle' => 'Seluruh paket, durasi, dan fasilitas di bawah ini terhubung langsung dengan sistem Booking Snap Fun Studio.',
            'studio_package_btn_label' => 'Booking Paket Ini',
            'studio_bg_eyebrow' => 'Pilihan Background',
            'studio_bg_title' => 'Background Snap Fun Studio',
            'studio_bg_subtitle' => 'Pilihan background beragam buat menyesuaikan mood dan konsep outfit kamu.',
            'studio_addons_eyebrow' => 'Add-ons & Merchandise',
            'studio_addons_title' => 'Tambahan Cetak, Photo Grid & Keychain',
            'studio_addons_subtitle' => 'Bikin hasil fotomu makin lengkap dengan tambahan cetak, pilihan layout grid, dan gantungan kunci akrilik.',
            'studio_gallery_eyebrow' => 'Galeri Studio',
            'studio_gallery_title' => 'Mereka yang Udah Foto di Snap Fun Studio',
            'studio_gallery_subtitle' => 'Setiap lembar foto adalah kapsul waktu yang menyimpan cerita seru di baliknya.',
            'studio_bottom_cta_title' => 'Siap bikin kenangan seru bareng bestie?',
            'studio_bottom_cta_label' => 'Gabung Foto Seru, Booking!',

            // === 5. SNAP FUN BOOTH ===
            'booth_eyebrow' => 'Produk · Event Photobooth',
            'booth_title' => 'Snap Fun Booth',
            'booth_text' => 'Bawa keseruan Snap Fun langsung ke acaramu! Snap Fun Booth bukan photobooth biasa. Kami membawa standar kualitas tinggi, lighting proper, dan hasil jernih ala self photo studio langsung ke event kamu. Mulai dari acara sekolah, kampus, sampai konser.',
            'booth_cta_label' => 'Hubungi untuk Kerja Sama Event',
            'booth_wa_text' => 'Halo Pinpin, aku mau kerja sama Snap Fun Booth untuk event. Nama event: ... Tanggal: ... Lokasi: ...',
            'booth_hero_image' => null,
            'booth_offers_eyebrow' => 'Yang Kami Tawarkan',
            'booth_offers_title' => 'Kalau Snap Fun Buka di Event Kamu',
            'booth_offers' => [
                ['title' => 'Kualitas ala Studio', 'text' => 'Equipment standar studio dengan lighting proper. Wajah auto glowing, nggak bakal gelap atau grainy.'],
                ['title' => 'Cetak Langsung Berkualitas', 'text' => 'Hasil cetak tajam, tebal, dan anti luntur dalam hitungan detik.'],
                ['title' => 'Frame Custom Eksklusif', 'text' => 'Layout/frame foto didesain khusus mengikuti tema event kamu biar makin memorable.'],
                ['title' => 'Crew Asik & Ramah', 'text' => 'Tim yang siap bantu arahkan dan bikin suasana booth di event kamu ramai dan pecah!'],
            ],
            'booth_frames_eyebrow' => 'Customizable Frame',
            'booth_frames_title' => 'Frame Foto Didesain Khusus Mengikuti Tema Event',
            'booth_frames_text' => 'Bikin acara sekolah, pensi, kampus, atau festival kamu punya souvenir foto eksklusif! Tim desain Snap Fun siap buatkan frame custom lengkap dengan logo dan identitas acara kamu.',
            'booth_frames_image' => null,
            'booth_systems_eyebrow' => 'Sistem Kerja Sama',
            'booth_systems_title' => 'Mutualan (Saling Untung)',
            'booth_systems_subtitle' => 'Pinpin tawarin sistem kerja sama yang simpel banget:',
            'booth_systems_image' => null,
            'booth_systems' => [
                ['title' => 'Sewa Tenant', 'text' => 'Snap Fun bakal sewa tenant ke panitia/penyelenggara sesuai harga yang akan disepakati.'],
                ['title' => 'Sponsorship', 'text' => 'Snap Fun bakal sponsorin acara kamu, khususnya acara pelajar atau mahasiswa, biar kita bisa seru-seruan bareng.'],
                ['title' => 'Bagi Hasil', 'text' => 'Dari harga Rp20.000 - Rp35.000, kita bagi hasil dengan persentase yang akan disepakati.'],
            ],
            'booth_req_eyebrow' => 'Kebutuhan Snap Fun',
            'booth_req_title' => 'Pinpin Butuh Ini di Lokasi',
            'booth_req_banner' => null,
            'booth_requirements' => [
                ['title' => '3x3 m', 'text' => 'Pinpin butuh space booth ukuran minimal 3x3 meter.'],
                ['title' => 'Listrik', 'text' => 'Pinpin butuh banget akses sumber listrik di dekat area booth.'],
                ['title' => 'Meja + Kursi', 'text' => 'Biar nggak berdiri, Pinpin butuh ini (*nggak wajib banget, kabarin aja kalau nggak ada).'],
            ],
            'booth_gallery_eyebrow' => 'Galeri Event',
            'booth_gallery_title' => 'Dokumentasi Event Bersama Snap Fun',
            'booth_closing_title' => 'Yuk, seru-seruan bareng kami..',
            'booth_closing_subtitle' => 'Yakin nggak mau event kalian keliatan beda? Yuk ngobrol lebih lanjut buat teknisnya. Kita siap bawa contoh hasil cetak biar kalian bisa cek sendiri kualitasnya!',
            'booth_closing_btn_label' => 'Chat Pinpin via WhatsApp',

            // === 6. SNAP FUN EXPRESS ===
            'express_eyebrow' => 'Produk · Segera Hadir',
            'express_title' => 'Snap Fun Express',
            'express_badge' => 'Coming Soon',
            'express_text' => 'Kejutan baru yang lagi kami siapkan buat kamu! Snap Fun Express akan hadir dengan konsep yang lebih compact, praktis, dan 100% self-service. Sebentar lagi, kamu bisa nemuin kami di tempat kamu nongkrong. Tinggal masuk, jepret, dan cetak!',
            'express_image' => null,
            'express_highlights' => [
                ['title' => 'Compact & Estetik', 'text' => 'Desain booth modern dan ringkas yang pas hadir di cafe, mall, dan tempat nongkrong favorit.'],
                ['title' => '100% Self-Service', 'text' => 'Transaksi cashless otomatis, tinggal masuk, pilih frame di layar sentuh, jepret, dan cetak.'],
                ['title' => 'Kualitas Tetap Nomor Satu', 'text' => 'Tetap mengusung pencahayaan glowing dan kertas foto tebal anti luntur khas Snap Fun.'],
            ],
            'express_cta_label' => 'Hubungi untuk Info & Kerja Sama Lokasi',
            'express_wa_text' => 'Halo Pinpin, aku mau tau lebih lanjut soal Snap Fun Express!',

            // === 7. DAFTAR JADI SIRKEL ===
            'sirkel_eyebrow' => 'Sirkel Snap Fun',
            'sirkel_title' => 'Daftar Jadi Sirkel',
            'sirkel_intro' => 'Sirkel Snap Fun adalah komunitas model & brand ambassador yang jadi wajah keseruan Snap Fun bareng Pinpin. Seru-seruan, bikin konten kreatif, dan tumbuh bareng kami!',
            'sirkel_image' => null,
            'sirkel_benefit_eyebrow' => 'Benefit Sirkel',
            'sirkel_benefit_title' => 'Yang Bakal Kamu Dapat',
            'sirkel_benefits' => [
                ['title' => 'Sesi Foto Gratis', 'text' => 'Dapat kuota sesi foto gratis di Snap Fun Studio Kota Serang.'],
                ['title' => 'Akses Duluan (Early Access)', 'text' => 'Jadi orang pertama yang nyobain konsep background, frame, dan fitur terbaru.'],
                ['title' => 'Merchandise Spesial', 'text' => 'Dapat keychain, sticker pack, dan merchandise eksklusif Snap Fun.'],
                ['title' => 'Tampil di Sosmed Resmi', 'text' => 'Kesempatan tampil dan di-mention di Instagram & TikTok resmi @snapfunstudio.'],
                ['title' => 'Komunitas Kreatif & Seru', 'text' => 'Tambah relasi bareng teman-teman konten kreator dan talent seru di Banten.'],
            ],
            'sirkel_form_title' => 'Form Pendaftaran Sirkel',
            'sirkel_form_subtitle' => 'Isi data dirimu di bawah ini. Setelah klik kirim, datamu tersimpan di sistem dan otomatis terhubung ke WhatsApp Pinpin.',
            'sirkel_form_btn_label' => 'Kirim & Lanjut ke WhatsApp',
            'sirkel_wa_intro' => 'Halo Pinpin, aku mau daftar jadi Sirkel Snap Fun!',

            // === 8. KONTAK PINPIN ===
            'contact_eyebrow' => 'Kontak Kami',
            'contact_title' => 'Kontak Pinpin',
            'contact_subtitle' => 'Ada pertanyaan, mau booking studio, atau ajak kerja sama event? Pilih keperluanmu, Pinpin siap bantu!',
            'contact_cards' => [
                [
                    'title' => 'Info & Booking Studio',
                    'text' => 'Tanya soal paket foto studio, ketersediaan jadwal, voucher, dan pilihan background.',
                    'btn_label' => 'Chat WhatsApp',
                    'type' => 'wa',
                    'target' => 'Halo Pinpin, aku mau tanya soal booking Snap Fun Studio.',
                    'btn_style' => 'btn-green',
                ],
                [
                    'title' => 'Kerja Sama Event (Booth)',
                    'text' => 'Undang Snap Fun Booth ke acara sekolah, kampus, festival, pernikahan, atau konsermu.',
                    'btn_label' => 'Chat WhatsApp Event',
                    'type' => 'wa',
                    'target' => 'Halo Pinpin, aku mau kerja sama untuk event. Nama event: ... Tanggal: ... Lokasi: ...',
                    'btn_style' => 'btn-yellow',
                ],
                [
                    'title' => 'Sirkel Snap Fun',
                    'text' => 'Mau gabung jadi model / brand ambassador seru bareng Pinpin? Yuk daftar sekarang.',
                    'btn_label' => 'Daftar Jadi Sirkel',
                    'type' => 'link',
                    'target' => '/sirkel',
                    'btn_style' => 'btn-blue',
                ],
            ],
            'contact_box_title' => 'Contact Person',
            'contact_box_subtitle' => 'Yakin nggak mau event atau momen kalian keliatan beda? Yuk ngobrol lebih lanjut buat teknisnya!',
            'contact_image' => null,
        ];
    }

    /** Bersihkan karakter emoticon/emoji agar UI 100% hanya teks, SVG Brand Guideline, atau Font Awesome. */
    public static function stripEmoji(string $text): string
    {
        return trim((string) preg_replace('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}\x{FE0F}]/u', '', $text));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $fallback = $default ?? (self::defaults()[$key] ?? null);
        $val = SiteSetting::get($key, $fallback);

        if (is_array($fallback) && ! is_array($val)) {
            return $fallback;
        }

        if ($val === null || $val === '') {
            return $fallback;
        }

        if (is_string($val)) {
            return self::stripEmoji($val);
        }

        return $val;
    }

    public static function bool(string $key): bool
    {
        $val = SiteSetting::get($key, self::defaults()[$key] ?? false);

        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    public static function waNumber(): string
    {
        return preg_replace('/\D+/', '', (string) self::get('wa_number'));
    }

    /** Link WhatsApp ke admin dengan pesan template. */
    public static function wa(string $text = ''): string
    {
        return 'https://wa.me/' . self::waNumber() . ($text !== '' ? '?text=' . rawurlencode($text) : '');
    }

    public static function waDisplay(): string
    {
        $n = self::waNumber();

        return str_starts_with($n, '62') ? '0' . substr($n, 2) : $n;
    }

    public static function instagramUrl(): string
    {
        return 'https://instagram.com/' . ltrim((string) self::get('instagram'), '@');
    }

    public static function tiktokUrl(): string
    {
        return 'https://tiktok.com/@' . ltrim((string) self::get('tiktok'), '@');
    }

    /**
     * Konversi path gambar (baik asset bawaan di public/img/... maupun hasil upload di storage/app/public/...)
     * menjadi URL publik yang valid.
     */
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'img/')) {
            return asset($path);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Ambil URL gambar berdasarkan key setting CMS, dengan fallback ke asset bawaan.
     */
    public static function img(string $key, string $defaultAsset): string
    {
        $custom = SiteSetting::get($key);

        if (is_string($custom) && $custom !== '') {
            return self::url($custom) ?? asset($defaultAsset);
        }

        return asset($defaultAsset);
    }

    /**
     * Ambil daftar Paket dari database Booking Website (App\Models\Package beserta relasi backgrounds).
     */
    public static function bookingPackages(): Collection
    {
        try {
            if (Schema::hasTable('packages')) {
                $pkgs = Package::with('backgrounds')->orderBy('id')->get();
                if ($pkgs->isNotEmpty()) {
                    return $pkgs;
                }
            }
        } catch (\Throwable $e) {
        }

        // Fallback selaras dengan struktur Package di website booking jika tabel kosong
        return collect([
            (object) [
                'id' => 1,
                'title' => 'Self Photo Studio',
                'price' => 50000,
                'duration_minutes' => 15,
                'downpayment' => 25000,
                'extras_people' => 15000,
                'description' => "Maksimal 2 orang (bisa tambah orang)\nGratis cetak foto ukuran 4R / Strip\nSemua softfile digital via Google Drive",
                'backgrounds' => collect([(object) ['name' => 'Tirai Merah'], (object) ['name' => 'Tirai Biru'], (object) ['name' => 'Tirai Abu']]),
            ],
            (object) [
                'id' => 2,
                'title' => 'Group / Bestie Package',
                'price' => 85000,
                'duration_minutes' => 25,
                'downpayment' => 40000,
                'extras_people' => 15000,
                'description' => "Cocok untuk grup & sahabat\nTermasuk cetak foto + pilihan Photo Grid\nSemua softfile original tanpa batas jepretan",
                'backgrounds' => collect([(object) ['name' => 'American School'], (object) ['name' => 'Tirai Merah'], (object) ['name' => 'Tirai Biru']]),
            ],
            (object) [
                'id' => 3,
                'title' => 'Photobox & Keychain Package',
                'price' => 65000,
                'duration_minutes' => 15,
                'downpayment' => 30000,
                'extras_people' => 15000,
                'description' => "Bebas pilih layout frame & Custom Frame\nTermasuk cetak Photostrip + Keychain Akrilik\nSemua softfile foto & GIF digital",
                'backgrounds' => collect([(object) ['name' => 'Tirai Biru'], (object) ['name' => 'Tirai Merah']]),
            ],
        ]);
    }

    /**
     * Ambil daftar Background dari database Booking Website (App\Models\Background beserta foto Spatie MediaLibrary & paketnya).
     */
    public static function bookingBackgrounds(): Collection
    {
        try {
            if (Schema::hasTable('backgrounds')) {
                $bgs = Background::with(['media', 'packages'])->orderBy('id')->get();
                if ($bgs->isNotEmpty()) {
                    return $bgs;
                }
            }
        } catch (\Throwable $e) {
        }

        return collect([
            (object) ['id' => 1, 'name' => 'Tirai Merah', 'color' => '#CE004F', 'image_url' => null, 'packages' => collect([(object) ['title' => 'Self Photo Studio']])],
            (object) ['id' => 2, 'name' => 'Tirai Abu', 'color' => '#64748b', 'image_url' => null, 'packages' => collect([(object) ['title' => 'Self Photo Studio']])],
            (object) ['id' => 3, 'name' => 'Tirai Biru', 'color' => '#1759CA', 'image_url' => null, 'packages' => collect([(object) ['title' => 'Self Photo Studio']])],
            (object) ['id' => 4, 'name' => 'American School', 'color' => '#F6A820', 'image_url' => null, 'packages' => collect([(object) ['title' => 'Group / Bestie Package']])],
        ]);
    }

    /**
     * Ambil daftar Add-ons dari database Booking Website (App\Models\Addon).
     */
    public static function bookingAddons(): Collection
    {
        try {
            if (Schema::hasTable('addons')) {
                return Addon::orderBy('id')->get();
            }
        } catch (\Throwable $e) {
        }

        return collect();
    }

    /**
     * Ambil daftar PhotoGrid dari database Booking Website (App\Models\PhotoGrid).
     */
    public static function bookingPhotoGrids(): Collection
    {
        try {
            if (Schema::hasTable('photo_grids')) {
                return PhotoGrid::orderBy('id')->get();
            }
        } catch (\Throwable $e) {
        }

        return collect();
    }

    /**
     * Ambil daftar Keychain dari database Booking Website (App\Models\Keychain).
     */
    public static function bookingKeychains(): Collection
    {
        try {
            if (Schema::hasTable('keychains')) {
                return Keychain::orderBy('id')->get();
            }
        } catch (\Throwable $e) {
        }

        return collect();
    }

    /**
     * Ambil daftar Jam Operasional dari database Booking Website (App\Models\OperationalHour).
     */
    public static function bookingOperationalHours(): Collection
    {
        try {
            if (Schema::hasTable('operational_hours')) {
                return OperationalHour::orderBy('id')->get();
            }
        } catch (\Throwable $e) {
        }

        return collect();
    }

    public static function defaultFaqs(): array
    {
        return [
            ['question' => 'Snap Fun itu apa?', 'answer' => 'Snap Fun adalah brand dari PT One Dream Creative di Kota Serang, Banten, yang menghadirkan pengalaman Self Photo Studio, Photobox, dan Photobooth untuk event. Kamu sendiri yang pegang shutter-nya.'],
            ['question' => 'Apa bedanya Snap Fun Studio, Booth, dan Express?', 'answer' => 'Studio adalah markas utama kami di Kota Serang untuk Self Photo Studio dan Photobox. Booth adalah layanan photobooth yang kami bawa ke event kamu. Express adalah konsep baru yang compact dan 100% self-service di tempat nongkrong, dan akan segera hadir.'],
            ['question' => 'Bagaimana cara booking Snap Fun Studio?', 'answer' => 'Klik tombol Booking Sekarang di website ini, pilih paket, background, tanggal, dan jam sesi, lalu ikuti langkah pembayarannya.'],
            ['question' => 'Apakah di Snap Fun Studio bisa bikin Custom Frame?', 'answer' => 'Bisa banget! Kamu bisa klik tombol Registrasi Custom Frame di halaman Beranda atau halaman Snap Fun Studio, isi konsep desain yang diinginkan, dan konfirmasi langsung ke WhatsApp Admin Pinpin.'],
            ['question' => 'Bisa nggak Snap Fun hadir di event kami?', 'answer' => 'Bisa banget! Kami melayani acara sekolah, kampus, festival, pernikahan, sampai konser. Hubungi Pinpin lewat WhatsApp untuk ngobrol soal teknisnya.'],
            ['question' => 'Apa saja sistem kerja sama event?', 'answer' => 'Ada tiga: Sewa Tenant, Sponsorship (khususnya acara pelajar/mahasiswa), dan Bagi Hasil (dari harga Rp20.000 - Rp35.000 dengan persentase yang disepakati).'],
            ['question' => 'Apa saja yang dibutuhkan Snap Fun Booth di lokasi?', 'answer' => 'Space booth minimal 3x3 m dan akses listrik. Meja dan kursi tidak wajib, kabari saja kalau tidak ada.'],
        ];
    }

    public static function defaultGalleryItems(): array
    {
        return [
            // Studio Gallery
            ['group' => 'studio', 'image' => 'img/group(3).png', 'caption' => '', 'sort' => 1],
            ['group' => 'studio', 'image' => 'img/group(4).png', 'caption' => '', 'sort' => 2],
            ['group' => 'studio', 'image' => 'img/group(2).png', 'caption' => '', 'sort' => 3],
            ['group' => 'studio', 'image' => 'img/group(1).png', 'caption' => '', 'sort' => 4],
            ['group' => 'studio', 'image' => 'img/site/why-photo.jpg', 'caption' => '', 'sort' => 5],
            ['group' => 'studio', 'image' => 'img/site/custom-frames.png', 'caption' => '', 'sort' => 6],

            // Booth Event Gallery (dari Company Profile)
            ['group' => 'booth', 'image' => 'img/site/doc-1.jpg', 'caption' => '', 'sort' => 1],
            ['group' => 'booth', 'image' => 'img/site/doc-2.jpg', 'caption' => '', 'sort' => 2],
            ['group' => 'booth', 'image' => 'img/site/doc-3.jpg', 'caption' => '', 'sort' => 3],
            ['group' => 'booth', 'image' => 'img/site/doc-4.jpg', 'caption' => '', 'sort' => 4],
            ['group' => 'booth', 'image' => 'img/site/doc-9.jpg', 'caption' => '', 'sort' => 5],
            ['group' => 'booth', 'image' => 'img/site/doc-10.jpg', 'caption' => '', 'sort' => 6],
            ['group' => 'booth', 'image' => 'img/site/doc-11.jpg', 'caption' => '', 'sort' => 7],
            ['group' => 'booth', 'image' => 'img/site/doc-12.jpg', 'caption' => '', 'sort' => 8],
            ['group' => 'booth', 'image' => 'img/site/doc-13.jpg', 'caption' => '', 'sort' => 9],
            ['group' => 'booth', 'image' => 'img/site/doc-7.jpg', 'caption' => '', 'sort' => 10],
            ['group' => 'booth', 'image' => 'img/site/doc-8.jpg', 'caption' => '', 'sort' => 11],
            ['group' => 'booth', 'image' => 'img/site/doc-14.jpg', 'caption' => '', 'sort' => 12],
        ];
    }

    public static function faqs(): array
    {
        self::ensureSeeded();

        $rows = [];
        try {
            $rows = SiteFaq::where('is_active', true)->orderBy('sort')->orderBy('id')->get(['question', 'answer'])->toArray();
        } catch (\Throwable $e) {
        }

        return $rows ?: self::defaultFaqs();
    }

    /**
     * Salin aset bawaan dari public/img ke storage/app/public/img agar Filament FileUpload
     * dapat menampilkan preview gambar bawaan dan tidak menghapusnya saat form disimpan.
     */
    public static function syncDefaultAssetsToPublicDisk(): void
    {
        try {
            $disk = Storage::disk('public');
            $files = [
                'img/Pinpin-01.png',
                'img/Pinpin-05.png',
                'img/group(1).png',
                'img/group(2).png',
                'img/group(3).png',
                'img/group(4).png',
                'img/logo/Logo Snapfun-01.png',
                'img/logo/Logo Snapfun-02.png',
            ];

            if (is_dir(public_path('img/site'))) {
                foreach (scandir(public_path('img/site')) ?: [] as $f) {
                    if ($f !== '.' && $f !== '..' && is_file(public_path('img/site/' . $f))) {
                        $files[] = 'img/site/' . $f;
                    }
                }
            }

            foreach ($files as $rel) {
                $src = public_path($rel);
                if (is_file($src) && ! $disk->exists($rel)) {
                    $disk->put($rel, file_get_contents($src));
                }
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Pastikan tabel-tabel CMS Website tersedia secara otomatis di server produksi (mis. Hostinger Git Deploy)
     * tanpa mengganggu tabel Website Booking.
     */
    public static function ensureTablesExist(): void
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                Schema::create('site_settings', function (Blueprint $table) {
                    $table->id();
                    $table->string('key')->unique();
                    $table->longText('value')->nullable();
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('site_promos')) {
                Schema::create('site_promos', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('badge')->nullable();
                    $table->text('description')->nullable();
                    $table->string('image')->nullable();
                    $table->string('link_label')->nullable();
                    $table->string('link_url')->nullable();
                    $table->date('starts_at')->nullable();
                    $table->date('ends_at')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->unsignedInteger('sort')->default(0);
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('site_gallery_items')) {
                Schema::create('site_gallery_items', function (Blueprint $table) {
                    $table->id();
                    $table->string('group')->default('studio');
                    $table->string('image');
                    $table->string('caption')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->unsignedInteger('sort')->default(0);
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('site_faqs')) {
                Schema::create('site_faqs', function (Blueprint $table) {
                    $table->id();
                    $table->string('question');
                    $table->text('answer');
                    $table->boolean('is_active')->default(true);
                    $table->unsignedInteger('sort')->default(0);
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('sirkel_registrations')) {
                Schema::create('sirkel_registrations', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->unsignedSmallInteger('age')->nullable();
                    $table->string('city')->nullable();
                    $table->string('whatsapp');
                    $table->string('instagram')->nullable();
                    $table->string('tiktok')->nullable();
                    $table->text('reason')->nullable();
                    $table->string('status')->default('baru');
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika koneksi DB belum siap
        }
    }

    /**
     * Pastikan data awal Company Profile sudah terisi di tabel CMS saat pertama kali dijalankan,
     * serta membersihkan emoticon lama dari versi sebelumnya.
     */
    public static function ensureSeeded(bool $force = false): void
    {
        static $checked = false;
        if ($checked && ! $force) {
            return;
        }
        $checked = true;

        self::ensureTablesExist();
        self::syncDefaultAssetsToPublicDisk();

        try {
            if (! Schema::hasTable('site_settings')) {
                return;
            }

            if (! $force && SiteSetting::get('_cms_seeded_v3') === '1') {
                return;
            }

            foreach (self::defaults() as $key => $val) {
                if ($val === null) {
                    continue;
                }
                $existing = SiteSetting::query()->where('key', $key)->first();
                if (! $existing || $force) {
                    SiteSetting::put($key, $val);
                } elseif (is_string($val) && is_string($existing->value)) {
                    // Bersihkan emoticon pada teks setting yang sudah ada di DB
                    $cleaned = self::stripEmoji($existing->value);
                    $cleaned = str_replace('→', '', $cleaned);
                    if ($cleaned !== $existing->value) {
                        SiteSetting::put($key, trim($cleaned));
                    }
                }
            }

            if (Schema::hasTable('site_promos') && (SitePromo::count() === 0 || $force)) {
                if ($force) {
                    SitePromo::query()->delete();
                }
                SitePromo::create([
                    'title' => 'Promo Spesial Seru-seruan Bareng Pinpin!',
                    'badge' => 'Promo Spesial',
                    'description' => 'Ajak bestie, pasangan, atau keluargamu dan abadikan momen tanpa batas di Snap Fun Studio Kota Serang.',
                    'link_label' => 'Booking Sekarang',
                    'link_url' => '/booking',
                    'is_active' => true,
                    'sort' => 1,
                ]);
            }

            if (Schema::hasTable('site_faqs') && (SiteFaq::count() === 0 || $force)) {
                if ($force) {
                    SiteFaq::query()->delete();
                }
                foreach (self::defaultFaqs() as $i => $faq) {
                    SiteFaq::create([
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                        'sort' => $i + 1,
                        'is_active' => true,
                    ]);
                }
            }

            if (Schema::hasTable('site_gallery_items') && (SiteGalleryItem::count() === 0 || $force)) {
                if ($force) {
                    SiteGalleryItem::query()->delete();
                }
                foreach (self::defaultGalleryItems() as $item) {
                    SiteGalleryItem::create([
                        'group' => $item['group'],
                        'image' => $item['image'],
                        'caption' => $item['caption'],
                        'sort' => $item['sort'],
                        'is_active' => true,
                    ]);
                }
            }

            SiteSetting::put('_cms_seeded_v3', '1');
        } catch (\Throwable $e) {
            // Abaikan jika database belum siap/migrasi belum dijalankan
        }
    }
}
