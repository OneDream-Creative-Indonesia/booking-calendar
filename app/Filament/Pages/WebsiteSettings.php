<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\Cms;
use App\Support\Site;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WebsiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Website CMS';
    protected static ?string $navigationLabel = 'Pengaturan Website';
    protected static ?string $title = 'Pengaturan Konten Website Utama';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.website-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Cms::allowed();
    }

    public function mount(): void
    {
        Site::ensureSeeded();

        $keys = array_keys(Site::defaults());
        $state = [];
        foreach ($keys as $k) {
            $state[$k] = SiteSetting::get($k, Site::defaults()[$k]);
        }

        $this->form->fill($state);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openSite')
                ->label('Lihat Website Utama')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('gray')
                ->url('/', shouldOpenInNewTab: true),
            Action::make('resetDefaults')
                ->label('Muat Ulang Default Compro')
                ->icon('heroicon-m-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Muat Ulang Konten Default Company Profile?')
                ->modalDescription('Tindakan ini akan mengembalikan teks pengaturan, promo awal, FAQ, dan galeri ke bawaan Company Profile Snap Fun.')
                ->action(function () {
                    abort_unless(Cms::allowed(), 403);
                    Site::ensureSeeded(true);
                    $this->mount();
                    Notification::make()
                        ->title('Seluruh konten default Company Profile berhasil dimuat ulang')
                        ->success()
                        ->send();
                }),
        ];
    }

    private static function itemsRepeater(string $name, string $label, string $titleLabel = 'Judul', string $textLabel = 'Deskripsi'): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->schema([
                Forms\Components\TextInput::make('title')->label($titleLabel)->required(),
                Forms\Components\Textarea::make('text')->label($textLabel)->rows(2),
            ])
            ->columns(2)
            ->reorderableWithButtons()
            ->collapsible()
            ->itemLabel(fn (array $s) => $s['title'] ?? null)
            ->defaultItems(0);
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Tabs::make('WebsiteCmsTabs')
                ->persistTabInQueryString()
                ->tabs([
                    // ==========================================
                    // TAB 1: IDENTITAS, NAVIGASI, KONTAK & FOOTER
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Umum, Navigasi & Footer')
                        ->icon('heroicon-m-cog-6-tooth')
                        ->schema([
                            Forms\Components\Section::make('Identitas Brand & SEO')
                                ->description('Atur nama brand, judul tab browser (SEO), deskripsi meta, serta logo di header & footer.')
                                ->schema([
                                    Forms\Components\TextInput::make('site_name')->label('Nama Brand / Website')->required(),
                                    Forms\Components\TextInput::make('site_company')->label('Nama Perusahaan (Sub-brand)')->placeholder('PT One Dream Creative'),
                                    Forms\Components\TextInput::make('site_title')->label('Judul Default Website (SEO Title)')->required()->columnSpanFull(),
                                    Forms\Components\Textarea::make('site_description')->label('Deskripsi Meta Website (SEO Description)')->rows(2)->columnSpanFull(),
                                    Forms\Components\FileUpload::make('nav_logo')
                                        ->label('Logo Header / Navbar (Opsional)')
                                        ->helperText('Kosongkan untuk memakai logo bawaan Snap Fun.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/brand'),
                                    Forms\Components\FileUpload::make('footer_logo')
                                        ->label('Logo Footer (Opsional)')
                                        ->helperText('Kosongkan untuk memakai logo putih bawaan Snap Fun.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/brand'),
                                ])->columns(2),

                            Forms\Components\Section::make('Label Menu Navigasi (Navbar)')
                                ->description('Seluruh teks menu pada bagian atas website dapat disesuaikan di sini.')
                                ->schema([
                                    Forms\Components\TextInput::make('nav_home_label')->label('Menu Beranda')->required(),
                                    Forms\Components\TextInput::make('nav_about_label')->label('Menu Tentang')->required(),
                                    Forms\Components\TextInput::make('nav_product_label')->label('Menu Dropdown Produk')->required(),
                                    Forms\Components\TextInput::make('nav_studio_label')->label('Sub-menu Produk 1 (Studio)')->required(),
                                    Forms\Components\TextInput::make('nav_booth_label')->label('Sub-menu Produk 2 (Booth)')->required(),
                                    Forms\Components\TextInput::make('nav_express_label')->label('Sub-menu Produk 3 (Express)')->required(),
                                    Forms\Components\TextInput::make('nav_sirkel_label')->label('Menu Daftar Sirkel')->required(),
                                    Forms\Components\TextInput::make('nav_contact_label')->label('Menu Kontak')->required(),
                                    Forms\Components\TextInput::make('nav_booking_label')->label('Teks Tombol Booking di Navbar')->required(),
                                    Forms\Components\TextInput::make('nav_booking_url')->label('Link Tombol Booking di Navbar')->required(),
                                ])->columns(3),

                            Forms\Components\Section::make('Kontak Utama, Sosial Media & Tombol WA Mengambang')
                                ->schema([
                                    Forms\Components\TextInput::make('contact_person_label')->label('Nama Contact Person')->placeholder('Snap Fun (Pinpin)'),
                                    Forms\Components\TextInput::make('wa_number')
                                        ->label('Nomor WhatsApp Admin (Pinpin)')
                                        ->helperText('Format internasional tanpa +, contoh: 6285117607254.')
                                        ->required(),
                                    Forms\Components\TextInput::make('instagram')->label('Username Instagram')->prefix('@')->required(),
                                    Forms\Components\TextInput::make('tiktok')->label('Username TikTok (Opsional)')->prefix('@'),
                                    Forms\Components\TextInput::make('email')->label('Email (Opsional)')->email(),
                                    Forms\Components\TextInput::make('operational_hours')->label('Jam Operasional Studio')->placeholder('Setiap Hari, 10.00 - 21.00 WIB'),
                                    Forms\Components\TextInput::make('address')->label('Alamat / Lokasi Studio')->required()->columnSpanFull(),
                                    Forms\Components\TextInput::make('maps_url')->label('Link Google Maps (Tombol Buka Maps)')->url(),
                                    Forms\Components\TextInput::make('maps_embed_url')->label('Link Embed Iframe Google Maps (Opsional)')->helperText('Isi URL src="https://www.google.com/maps/embed?..." jika ingin menampilkan peta interaktif.'),
                                    Forms\Components\Toggle::make('wa_float_enabled')->label('Tampilkan Tombol WhatsApp Mengambang di Pojok Kanan Bawah')->default(true),
                                    Forms\Components\TextInput::make('wa_float_text')->label('Pesan Otomatis Tombol WA Mengambang')->required(),
                                ])->columns(2),

                            Forms\Components\Section::make('Konten Footer')
                                ->schema([
                                    Forms\Components\Textarea::make('footer_tagline')->label('Deskripsi Singkat di Footer')->rows(2)->columnSpanFull(),
                                    Forms\Components\TextInput::make('footer_subtext')->label('Sub-teks Perusahaan di Footer'),
                                    Forms\Components\TextInput::make('footer_copyright')->label('Teks Copyright Bawah'),
                                    Forms\Components\TextInput::make('footer_col1_title')->label('Judul Kolom Footer 1'),
                                    Forms\Components\TextInput::make('footer_col2_title')->label('Judul Kolom Footer 2'),
                                    Forms\Components\TextInput::make('footer_col3_title')->label('Judul Kolom Footer 3'),
                                ])->columns(3),
                        ]),

                    // ==========================================
                    // TAB 2: BERANDA
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Beranda')
                        ->icon('heroicon-m-home')
                        ->schema([
                            Forms\Components\Section::make('Hero Banner Utama')
                                ->schema([
                                    Forms\Components\TextInput::make('hero_eyebrow')->label('Label Kecil di Atas Judul (Eyebrow)')->required(),
                                    Forms\Components\TextInput::make('hero_badge')->label('Badge Stiker Maskot (cth: Fun!)'),
                                    Forms\Components\Textarea::make('hero_title')->label('Judul Utama Hero')->rows(2)->required()->columnSpanFull(),
                                    Forms\Components\Textarea::make('hero_subtitle')->label('Sub-judul / Deskripsi Hero')->rows(3)->columnSpanFull(),
                                    Forms\Components\TextInput::make('hero_btn1_label')->label('Teks Tombol Utama (Booking)')->required(),
                                    Forms\Components\TextInput::make('hero_btn1_url')->label('Link Tombol Utama')->required(),
                                    Forms\Components\TextInput::make('hero_btn2_label')->label('Teks Tombol Kedua (Collab WA)')->required(),
                                    Forms\Components\TextInput::make('hero_btn2_wa_text')->label('Pesan WA Tombol Collab')->required(),
                                    Forms\Components\FileUpload::make('hero_image')
                                        ->label('Gambar Maskot / Ilustrasi Hero (Opsional)')
                                        ->helperText('Kosongkan untuk memakai ilustrasi Pinpin bawaan.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/hero')
                                        ->columnSpanFull(),
                                    self::itemsRepeater('hero_highlights', 'Poin Highlight di Bawah Hero', 'Judul Singkat', 'Keterangan')->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Promo Beranda')
                                ->schema([
                                    Forms\Components\TextInput::make('home_promo_eyebrow')->label('Label Eyebrow Promo')->required(),
                                    Forms\Components\TextInput::make('home_promo_title')->label('Judul Section Promo')->required(),
                                    Forms\Components\TextInput::make('home_promo_subtitle')->label('Sub-judul Section Promo')->columnSpanFull(),
                                    Forms\Components\TextInput::make('home_promo_empty')->label('Teks Saat Tidak Ada Promo Aktif')->columnSpanFull(),
                                    Forms\Components\Placeholder::make('promo_info')
                                        ->label('Daftar Item Promo')
                                        ->content('Untuk menambah, mengubah, atau menonaktifkan kartu promo beserta gambar & masa berlakunya, buka menu "Promo Beranda" di sidebar Website CMS.')
                                        ->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Highlight Custom Frame Studio & Pop Up Registrasi WA (Beranda & Halaman Studio)')
                                ->description('Bagian ini tampil sebagai Highlight di Beranda dan di halaman Snap Fun Studio, lengkap dengan Pop Up Registrasi yang langsung mengarah ke WhatsApp Admin.')
                                ->schema([
                                    Forms\Components\TextInput::make('custom_frame_eyebrow')->label('Label Eyebrow Highlight')->required(),
                                    Forms\Components\TextInput::make('custom_frame_badge')->label('Badge Pop Up')->required(),
                                    Forms\Components\TextInput::make('custom_frame_title')->label('Judul Highlight Custom Frame')->required()->columnSpanFull(),
                                    Forms\Components\Textarea::make('custom_frame_subtitle')->label('Deskripsi Highlight Custom Frame')->rows(3)->columnSpanFull(),
                                    Forms\Components\TextInput::make('custom_frame_btn_label')->label('Teks Tombol Buka Pop Up Registrasi')->required(),
                                    Forms\Components\FileUpload::make('custom_frame_image')
                                        ->label('Gambar Showcase Custom Frame (Opsional)')
                                        ->helperText('Kosongkan untuk memakai showcase frame bawaan Company Profile.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/custom-frame'),
                                    self::itemsRepeater('custom_frame_points', 'Poin Keunggulan Custom Frame Studio', 'Judul Poin', 'Keterangan')->columnSpanFull(),
                                    Forms\Components\Fieldset::make('Pengaturan Pop Up Form Registrasi Custom Frame (Terhubung ke WhatsApp)')->schema([
                                        Forms\Components\TextInput::make('custom_frame_modal_title')->label('Judul Pop Up Registrasi')->required(),
                                        Forms\Components\TextInput::make('custom_frame_modal_btn_label')->label('Teks Tombol Kirim ke WhatsApp')->required(),
                                        Forms\Components\Textarea::make('custom_frame_modal_subtitle')->label('Sub-judul / Keterangan di Pop Up')->rows(2)->columnSpanFull(),
                                        Forms\Components\TextInput::make('custom_frame_wa_intro')->label('Kalimat Pembuka Pesan WhatsApp Otomatis')->required()->columnSpanFull(),
                                    ])->columns(2),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Ringkasan 3 Produk (Layanan Snap Fun)')
                                ->schema([
                                    Forms\Components\TextInput::make('home_products_eyebrow')->label('Label Eyebrow Produk')->required(),
                                    Forms\Components\TextInput::make('home_products_title')->label('Judul Section 3 Produk')->required(),
                                    Forms\Components\TextInput::make('home_products_subtitle')->label('Sub-judul Section 3 Produk')->columnSpanFull(),

                                    Forms\Components\Fieldset::make('Kartu Produk 1: Snap Fun Studio')->schema([
                                        Forms\Components\TextInput::make('studio_tag')->label('Tag Badge')->required(),
                                        Forms\Components\TextInput::make('studio_card_title')->label('Judul Kartu')->required(),
                                        Forms\Components\TextInput::make('studio_more_label')->label('Teks Link')->required(),
                                        Forms\Components\Textarea::make('studio_summary')->label('Ringkasan di Kartu')->rows(3)->columnSpanFull(),
                                    ])->columns(3),

                                    Forms\Components\Fieldset::make('Kartu Produk 2: Snap Fun Booth')->schema([
                                        Forms\Components\TextInput::make('booth_tag')->label('Tag Badge')->required(),
                                        Forms\Components\TextInput::make('booth_card_title')->label('Judul Kartu')->required(),
                                        Forms\Components\TextInput::make('booth_more_label')->label('Teks Link')->required(),
                                        Forms\Components\Textarea::make('booth_summary')->label('Ringkasan di Kartu')->rows(3)->columnSpanFull(),
                                    ])->columns(3),

                                    Forms\Components\Fieldset::make('Kartu Produk 3: Snap Fun Express')->schema([
                                        Forms\Components\TextInput::make('express_tag')->label('Tag Badge')->required(),
                                        Forms\Components\TextInput::make('express_card_title')->label('Judul Kartu')->required(),
                                        Forms\Components\TextInput::make('express_more_label')->label('Teks Link')->required(),
                                        Forms\Components\Textarea::make('express_summary')->label('Ringkasan di Kartu')->rows(3)->columnSpanFull(),
                                    ])->columns(3),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Kenapa Harus Snap Fun?')
                                ->schema([
                                    Forms\Components\TextInput::make('home_why_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('home_why_title')->label('Judul Section')->required(),
                                    Forms\Components\TextInput::make('home_why_subtitle')->label('Sub-judul Section')->columnSpanFull(),
                                    Forms\Components\FileUpload::make('why_us_image')
                                        ->label('Ilustrasi Karakter Pendamping (Opsional)')
                                        ->helperText('Kosongkan untuk menampilkan karakter merah bawaan Compro.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/why')
                                        ->columnSpanFull(),
                                    self::itemsRepeater('why_us', 'Daftar Alasan Kenapa Harus Snap Fun')->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Showcase Event Bersama Snap Fun')
                                ->schema([
                                    Forms\Components\TextInput::make('home_events_eyebrow')->label('Label Eyebrow Event')->required(),
                                    Forms\Components\TextInput::make('home_events_title')->label('Judul Section Event')->required(),
                                    Forms\Components\TextInput::make('home_events_more_chip')->label('Teks Chip Penutup (cth: ...and many more!)'),
                                    Forms\Components\TextInput::make('home_events_btn_label')->label('Teks Tombol CTA ke Booth'),
                                    Forms\Components\Repeater::make('events')
                                        ->label('Daftar Nama & Logo Event yang Pernah Bekerja Sama')
                                        ->schema([
                                            Forms\Components\TextInput::make('name')->label('Nama Event')->required(),
                                            Forms\Components\FileUpload::make('logo')
                                                ->label('Logo Event (Opsional)')
                                                ->image()
                                                ->disk('public')
                                                ->directory('site/events'),
                                        ])
                                        ->columns(2)
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(fn (array $s) => $s['name'] ?? null)
                                        ->defaultItems(0)
                                        ->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Ajakan Sirkel & Kunjungi Studio (Bagian Bawah Beranda)')
                                ->schema([
                                    Forms\Components\TextInput::make('home_sirkel_eyebrow')->label('Eyebrow Sirkel')->required(),
                                    Forms\Components\TextInput::make('home_sirkel_title')->label('Judul Ajakan Sirkel')->required(),
                                    Forms\Components\TextInput::make('home_sirkel_btn_label')->label('Teks Tombol Daftar Sirkel')->required(),
                                    Forms\Components\TextInput::make('home_visit_title')->label('Judul Kartu Kunjungi Studio')->required(),
                                    Forms\Components\Textarea::make('home_visit_subtitle')->label('Deskripsi Kartu Kunjungi Studio')->rows(2)->columnSpanFull(),
                                    Forms\Components\TextInput::make('home_visit_maps_label')->label('Teks Tombol Google Maps')->required(),
                                    Forms\Components\TextInput::make('home_visit_contact_label')->label('Teks Tombol Kontak Pinpin')->required(),
                                ])->columns(2),
                        ]),

                    // ==========================================
                    // TAB 3: TENTANG
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Tentang')
                        ->icon('heroicon-m-information-circle')
                        ->schema([
                            Forms\Components\Section::make('Header & Cerita Tentang Snap Fun')
                                ->schema([
                                    Forms\Components\TextInput::make('about_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('about_title')->label('Judul Halaman Tentang')->required(),
                                    Forms\Components\TextInput::make('about_subtitle')->label('Sub-judul Halaman Tentang')->columnSpanFull(),
                                    Forms\Components\Textarea::make('about_text')
                                        ->label('Cerita & Deskripsi Lengkap Snap Fun')
                                        ->helperText('Pisahkan antar paragraf dengan baris kosong (Enter 2x).')
                                        ->rows(8)
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\FileUpload::make('about_image')
                                        ->label('Gambar Maskot / Ilustrasi Halaman Tentang (Opsional)')
                                        ->helperText('Kosongkan untuk memakai ilustrasi Pinpin bawaan.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/about')
                                        ->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Judul Section Layanan, Keunggulan & FAQ di Halaman Tentang')
                                ->schema([
                                    Forms\Components\TextInput::make('about_products_eyebrow')->label('Eyebrow Section Produk')->required(),
                                    Forms\Components\TextInput::make('about_products_title')->label('Judul Section Produk')->required(),
                                    Forms\Components\TextInput::make('about_why_eyebrow')->label('Eyebrow Section Kenapa Snap Fun')->required(),
                                    Forms\Components\TextInput::make('about_why_title')->label('Judul Section Kenapa Snap Fun')->required(),
                                    Forms\Components\TextInput::make('about_faq_eyebrow')->label('Eyebrow Section FAQ')->required(),
                                    Forms\Components\TextInput::make('about_faq_title')->label('Judul Section FAQ')->required(),
                                    Forms\Components\TextInput::make('about_faq_prompt')->label('Teks Ajakan di Bawah FAQ')->required(),
                                    Forms\Components\TextInput::make('about_faq_cta_label')->label('Teks Link Tanya Pinpin')->required(),
                                    Forms\Components\TextInput::make('about_faq_wa_text')->label('Pesan WA Link Tanya Pinpin')->required()->columnSpanFull(),
                                    Forms\Components\Placeholder::make('faq_note')
                                        ->label('Daftar Pertanyaan FAQ')
                                        ->content('Pertanyaan dan jawaban FAQ dapat ditambah, diurutkan, atau diubah melalui menu "FAQ" di sidebar Website CMS.')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    // ==========================================
                    // TAB 4: SNAP FUN STUDIO (SINKRON DENGAN BOOKING CMS)
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Snap Fun Studio')
                        ->icon('heroicon-m-camera')
                        ->schema([
                            Forms\Components\Section::make('Integrasi Otomatis dengan Database Website Booking')
                                ->description('Agar tidak bentrok dengan sistem Booking (/booking), data Paket Foto, Pilihan Background, Harga Add-ons, Photo Grid, Keychain, dan Jam Operasional di halaman Snap Fun Studio diambil secara otomatis dari menu Operational.')
                                ->schema([
                                    Forms\Components\Placeholder::make('booking_sync_info')
                                        ->label('Sumber Data Snap Fun Studio')
                                        ->content('Ketika Anda mengubah Paket di menu "Operational > Paket Settings", Background di "Operational > Background Settings", Add-ons di "Operational > Harga Add-ons", atau Jam Buka di "Operational > Operational Settings", halaman Snap Fun Studio di website utama otomatis ikut ter-update secara real-time.')
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('Header Halaman Snap Fun Studio')
                                ->schema([
                                    Forms\Components\TextInput::make('studio_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('studio_title')->label('Judul Halaman')->required(),
                                    Forms\Components\Textarea::make('studio_text')->label('Deskripsi Lengkap Snap Fun Studio')->rows(4)->required()->columnSpanFull(),
                                    Forms\Components\TextInput::make('studio_cta_label')->label('Teks Tombol CTA Utama')->required(),
                                    Forms\Components\TextInput::make('studio_cta_url')->label('Link Tombol CTA Utama')->required(),
                                    Forms\Components\FileUpload::make('studio_hero_image')
                                        ->label('Gambar Hero Halaman Studio (Opsional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/studio')
                                        ->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Judul Section Paket, Background & Add-ons (Data dari Booking)')
                                ->schema([
                                    Forms\Components\TextInput::make('studio_service_eyebrow')->label('Label Eyebrow Paket')->required(),
                                    Forms\Components\TextInput::make('studio_service_title')->label('Judul Section Paket')->required(),
                                    Forms\Components\TextInput::make('studio_service_subtitle')->label('Sub-judul Section Paket'),
                                    Forms\Components\TextInput::make('studio_package_btn_label')->label('Teks Tombol pada Kartu Paket Booking')->required(),
                                    Forms\Components\TextInput::make('studio_bg_eyebrow')->label('Label Eyebrow Background')->required(),
                                    Forms\Components\TextInput::make('studio_bg_title')->label('Judul Section Background')->required(),
                                    Forms\Components\TextInput::make('studio_bg_subtitle')->label('Sub-judul Section Background')->columnSpanFull(),
                                    Forms\Components\TextInput::make('studio_addons_eyebrow')->label('Label Eyebrow Add-ons & Operasional')->required(),
                                    Forms\Components\TextInput::make('studio_addons_title')->label('Judul Section Add-ons & Operasional')->required(),
                                    Forms\Components\TextInput::make('studio_addons_subtitle')->label('Sub-judul Section Add-ons & Operasional')->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Galeri Customer & CTA Bawah Studio')
                                ->schema([
                                    Forms\Components\TextInput::make('studio_gallery_eyebrow')->label('Label Eyebrow Galeri')->required(),
                                    Forms\Components\TextInput::make('studio_gallery_title')->label('Judul Galeri Customer')->required(),
                                    Forms\Components\TextInput::make('studio_gallery_subtitle')->label('Sub-judul Galeri Customer')->columnSpanFull(),
                                    Forms\Components\TextInput::make('studio_bottom_cta_title')->label('Judul Ajakan Booking di Bawah Galeri')->required(),
                                    Forms\Components\TextInput::make('studio_bottom_cta_label')->label('Teks Tombol Booking Bawah')->required(),
                                    Forms\Components\Placeholder::make('studio_gallery_hint')
                                        ->label('Foto-foto Galeri Studio')
                                        ->content('Kelola foto customer Snap Fun Studio di menu "Galeri Foto" (pilih kategori: Snap Fun Studio). Galeri ditampilkan bersih tanpa teks dan tanpa bingkai pembatas.')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    // ==========================================
                    // TAB 5: SNAP FUN BOOTH
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Snap Fun Booth')
                        ->icon('heroicon-m-sparkles')
                        ->schema([
                            Forms\Components\Section::make('Header Halaman Snap Fun Booth')
                                ->schema([
                                    Forms\Components\TextInput::make('booth_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('booth_title')->label('Judul Halaman')->required(),
                                    Forms\Components\Textarea::make('booth_text')->label('Deskripsi Lengkap Snap Fun Booth')->rows(4)->required()->columnSpanFull(),
                                    Forms\Components\TextInput::make('booth_cta_label')->label('Teks Tombol Hubungi Kerja Sama Event')->required(),
                                    Forms\Components\TextInput::make('booth_wa_text')->label('Template Pesan WhatsApp Kerja Sama Event')->required(),
                                    Forms\Components\FileUpload::make('booth_hero_image')
                                        ->label('Gambar Pendamping Header Booth (Opsional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/booth')
                                        ->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Apa yang Kami Tawarkan di Event')
                                ->schema([
                                    Forms\Components\TextInput::make('booth_offers_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('booth_offers_title')->label('Judul Section Penawaran')->required(),
                                    self::itemsRepeater('booth_offers', 'Daftar Keunggulan Snap Fun Booth di Event')->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Showcase Customizable Frame')
                                ->schema([
                                    Forms\Components\TextInput::make('booth_frames_eyebrow')->label('Label Eyebrow Frame')->required(),
                                    Forms\Components\TextInput::make('booth_frames_title')->label('Judul Section Frame Custom')->required(),
                                    Forms\Components\Textarea::make('booth_frames_text')->label('Deskripsi Frame Custom')->rows(3)->columnSpanFull(),
                                    Forms\Components\FileUpload::make('booth_frames_image')
                                        ->label('Gambar Showcase Frame Custom (Opsional)')
                                        ->helperText('Kosongkan untuk menampilkan showcase photostrip bawaan Company Profile.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/booth')
                                        ->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Sistem Kerja Sama Event')
                                ->schema([
                                    Forms\Components\TextInput::make('booth_systems_eyebrow')->label('Label Eyebrow Sistem Kerja Sama')->required(),
                                    Forms\Components\TextInput::make('booth_systems_title')->label('Judul Sistem Kerja Sama')->required(),
                                    Forms\Components\TextInput::make('booth_systems_subtitle')->label('Sub-judul Sistem Kerja Sama')->columnSpanFull(),
                                    Forms\Components\FileUpload::make('booth_systems_image')
                                        ->label('Gambar Maskot Pendamping Sistem Kerja Sama (Opsional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/booth')
                                        ->columnSpanFull(),
                                    self::itemsRepeater('booth_systems', '3 Sistem Kerja Sama (Sewa Tenant, Sponsorship, Bagi Hasil)')->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Kebutuhan Teknis di Lokasi')
                                ->schema([
                                    Forms\Components\TextInput::make('booth_req_eyebrow')->label('Label Eyebrow Kebutuhan Lokasi')->required(),
                                    Forms\Components\TextInput::make('booth_req_title')->label('Judul Kebutuhan Lokasi')->required(),
                                    Forms\Components\FileUpload::make('booth_req_banner')
                                        ->label('Foto Banner Booth di Lokasi (Opsional)')
                                        ->helperText('Kosongkan untuk menampilkan foto Pop Up Self Photo Booth dari Company Profile.')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/booth')
                                        ->columnSpanFull(),
                                    self::itemsRepeater('booth_requirements', 'Daftar Kebutuhan di Lokasi (Space, Listrik, Meja+Kursi)')->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Section Galeri Dokumentasi Event & Penutup')
                                ->schema([
                                    Forms\Components\TextInput::make('booth_gallery_eyebrow')->label('Label Eyebrow Galeri Event')->required(),
                                    Forms\Components\TextInput::make('booth_gallery_title')->label('Judul Galeri Dokumentasi Event')->required(),
                                    Forms\Components\TextInput::make('booth_closing_title')->label('Judul Ajakan Penutup Bawah')->required(),
                                    Forms\Components\TextInput::make('booth_closing_btn_label')->label('Teks Tombol WA Penutup')->required(),
                                    Forms\Components\Textarea::make('booth_closing_subtitle')->label('Deskripsi Ajakan Penutup')->rows(2)->columnSpanFull(),
                                    Forms\Components\Placeholder::make('booth_gallery_hint')
                                        ->label('Foto Dokumentasi Event')
                                        ->content('Kelola foto-foto dokumentasi event di menu "Galeri Foto" (pilih kategori: Snap Fun Booth).')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    // ==========================================
                    // TAB 6: SNAP FUN EXPRESS
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Snap Fun Express')
                        ->icon('heroicon-m-bolt')
                        ->schema([
                            Forms\Components\Section::make('Konten Halaman Snap Fun Express')
                                ->schema([
                                    Forms\Components\TextInput::make('express_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('express_title')->label('Judul Halaman')->required(),
                                    Forms\Components\TextInput::make('express_badge')->label('Badge Status (cth: Coming Soon)'),
                                    Forms\Components\TextInput::make('express_cta_label')->label('Teks Tombol Kontak Express')->required(),
                                    Forms\Components\Textarea::make('express_text')->label('Deskripsi Snap Fun Express')->rows(4)->required()->columnSpanFull(),
                                    Forms\Components\TextInput::make('express_wa_text')->label('Pesan WhatsApp Tombol Express')->required()->columnSpanFull(),
                                    Forms\Components\FileUpload::make('express_image')
                                        ->label('Gambar Ilustrasi / Maskot Express (Opsional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/express')
                                        ->columnSpanFull(),
                                    self::itemsRepeater('express_highlights', 'Poin Keunggulan Konsep Snap Fun Express')->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    // ==========================================
                    // TAB 7: DAFTAR JADI SIRKEL
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Daftar Sirkel')
                        ->icon('heroicon-m-user-group')
                        ->schema([
                            Forms\Components\Section::make('Header & Benefit Sirkel Snap Fun')
                                ->schema([
                                    Forms\Components\TextInput::make('sirkel_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('sirkel_title')->label('Judul Halaman Sirkel')->required(),
                                    Forms\Components\Textarea::make('sirkel_intro')->label('Deskripsi Pengantar Sirkel')->rows(3)->required()->columnSpanFull(),
                                    Forms\Components\TextInput::make('sirkel_benefit_eyebrow')->label('Eyebrow Section Benefit')->required(),
                                    Forms\Components\TextInput::make('sirkel_benefit_title')->label('Judul Section Benefit')->required(),
                                    Forms\Components\FileUpload::make('sirkel_image')
                                        ->label('Gambar Ilustrasi Sirkel (Opsional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/sirkel')
                                        ->columnSpanFull(),
                                    self::itemsRepeater('sirkel_benefits', 'Daftar Benefit Menjadi Sirkel')->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Pengaturan Formulir Pendaftaran Sirkel')
                                ->schema([
                                    Forms\Components\TextInput::make('sirkel_form_title')->label('Judul Box Formulir')->required(),
                                    Forms\Components\TextInput::make('sirkel_form_btn_label')->label('Teks Tombol Submit Formulir')->required(),
                                    Forms\Components\Textarea::make('sirkel_form_subtitle')->label('Keterangan di Atas Formulir')->rows(2)->columnSpanFull(),
                                    Forms\Components\TextInput::make('sirkel_wa_intro')->label('Kalimat Pembuka Pesan WhatsApp Otomatis')->required()->columnSpanFull(),
                                    Forms\Components\Placeholder::make('sirkel_data_hint')
                                        ->label('Data Pelamar Sirkel')
                                        ->content('Seluruh pendaftar yang mengisi form otomatis tersimpan di menu "Pendaftar Sirkel" pada sidebar Website CMS.')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    // ==========================================
                    // TAB 8: KONTAK PINPIN
                    // ==========================================
                    Forms\Components\Tabs\Tab::make('Kontak Pinpin')
                        ->icon('heroicon-m-chat-bubble-left-right')
                        ->schema([
                            Forms\Components\Section::make('Header & Kartu Pilihan Kontak')
                                ->schema([
                                    Forms\Components\TextInput::make('contact_eyebrow')->label('Label Eyebrow')->required(),
                                    Forms\Components\TextInput::make('contact_title')->label('Judul Halaman Kontak')->required(),
                                    Forms\Components\Textarea::make('contact_subtitle')->label('Sub-judul Halaman Kontak')->rows(2)->columnSpanFull(),
                                    Forms\Components\Repeater::make('contact_cards')
                                        ->label('Kartu Keperluan Kontak')
                                        ->schema([
                                            Forms\Components\TextInput::make('title')->label('Judul Kartu')->required(),
                                            Forms\Components\TextInput::make('btn_label')->label('Teks Tombol')->required(),
                                            Forms\Components\Select::make('type')
                                                ->label('Jenis Tujuan Tombol')
                                                ->options([
                                                    'wa' => 'Kirim Pesan WhatsApp ke Pinpin',
                                                    'link' => 'Buka Link Halaman / URL',
                                                ])
                                                ->default('wa')
                                                ->required(),
                                            Forms\Components\Select::make('btn_style')
                                                ->label('Warna Tombol')
                                                ->options([
                                                    'btn-green' => 'Hijau WhatsApp',
                                                    'btn-yellow' => 'Kuning Snap Fun',
                                                    'btn-blue' => 'Biru Snap Fun',
                                                ])
                                                ->default('btn-green')
                                                ->required(),
                                            Forms\Components\Textarea::make('text')->label('Deskripsi Singkat')->rows(2)->columnSpanFull(),
                                            Forms\Components\TextInput::make('target')
                                                ->label('Isi Pesan WhatsApp / URL Tujuan')
                                                ->helperText('Jika jenis = WhatsApp, isi teks pesan otomatis. Jika jenis = Link, isi URL seperti /sirkel atau /booking.')
                                                ->required()
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(4)
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(fn (array $s) => $s['title'] ?? null)
                                        ->defaultItems(0)
                                        ->columnSpanFull(),
                                ])->columns(2),

                            Forms\Components\Section::make('Box Contact Person Bawah')
                                ->schema([
                                    Forms\Components\TextInput::make('contact_box_title')->label('Judul Box Contact Person')->required(),
                                    Forms\Components\Textarea::make('contact_box_subtitle')->label('Sub-judul Box Contact Person')->rows(2)->columnSpanFull(),
                                    Forms\Components\FileUpload::make('contact_image')
                                        ->label('Gambar Maskot Pendamping Kontak (Opsional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/contact')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])->columnSpanFull(),
        ]);
    }

    public function save(): void
    {
        abort_unless(Cms::allowed(), 403);

        $state = $this->form->getState();
        foreach (array_keys(Site::defaults()) as $key) {
            if (array_key_exists($key, $state)) {
                SiteSetting::put($key, $state[$key]);
            }
        }

        Notification::make()
            ->title('Seluruh pengaturan konten website berhasil disimpan!')
            ->success()
            ->send();
    }
}
