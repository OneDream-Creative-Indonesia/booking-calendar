@extends('site.layout')
@php
    use App\Support\Site;
    $cardColors = ['pkg-blue', 'pkg-pink', 'pkg-yellow'];
    $bgFallbackColors = ['#CE004F', '#64748b', '#1759CA', '#F6A820', '#0d9488', '#7c3aed'];
@endphp
@section('title', Site::get('studio_title') . ' - ' . Site::get('site_name'))
@section('content')
<section class="pagehead">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">{{ Site::get('studio_eyebrow') }}</span>
            <h1 class="title">{{ Site::get('studio_title') }}</h1>
            <p class="lead" style="margin-top:14px">{{ Site::get('studio_text') }}</p>
            <div class="cta" style="margin-top:24px">
                <a class="btn btn-yellow" href="{{ Site::get('studio_cta_url', '/booking') }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>{{ Site::get('studio_cta_label') }}</span>
                </a>
                <button type="button" class="btn btn-white" onclick="openCustomFrameModal()">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>{{ Site::get('custom_frame_btn_label') }}</span>
                </button>
            </div>
        </div>
        <div class="hero-art">
            <div class="blob"></div>
            <img src="{{ Site::img('studio_hero_image', 'img/Pinpin-01.png') }}" alt="{{ Site::get('studio_title') }}" width="380" height="380" fetchpriority="high" decoding="async">
        </div>
    </div>
</section>

{{-- 1. PAKET & LAYANAN STUDIO (DIAMBIL LANGSUNG DARI DATABASE WEBSITE BOOKING) --}}
<section>
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('studio_service_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('studio_service_title') }}</h2>
            @if(Site::get('studio_service_subtitle'))
                <p class="lead" style="margin-top:10px">{{ Site::get('studio_service_subtitle') }}</p>
            @endif
        </div>

        <div class="grid g3" style="margin-top:36px;align-items:stretch">
            @foreach($packages as $idx => $p)
                @php
                    $colorClass = $cardColors[$idx % count($cardColors)];
                    $descLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($p->description ?? '')))));
                    $lineIcons = ['fa-solid fa-user-group', 'fa-solid fa-print', 'fa-regular fa-images', 'fa-solid fa-camera', 'fa-solid fa-star'];
                    $pkgBgs = $p->backgrounds ?? collect();
                @endphp
                <div class="pkg-card {{ $colorClass }}">
                    <div class="pkg-head">
                        <h3>{{ $p->title }}</h3>
                        <span class="pkg-price">Rp{{ number_format((float) $p->price, 0, ',', '.') }}</span>
                    </div>

                    <ul class="pkg-list">
                        @foreach($descLines as $li => $line)
                            <li>
                                <span class="pkg-ic"><i class="{{ $lineIcons[$li % count($lineIcons)] }}"></i></span>
                                <span>{{ $line }}</span>
                            </li>
                        @endforeach
                        @if(!empty($p->duration_minutes))
                            <li>
                                <span class="pkg-ic"><i class="fa-solid fa-hourglass-half"></i></span>
                                <span>Durasi: {{ $p->duration_minutes }} menit</span>
                            </li>
                        @endif
                        @if(!empty($p->extras_people))
                            <li>
                                <span class="pkg-ic"><i class="fa-solid fa-user-plus"></i></span>
                                <span>Tambahan orang: Rp{{ is_numeric($p->extras_people) ? number_format((float) $p->extras_people, 0, ',', '.') . '/orang' : $p->extras_people }}</span>
                            </li>
                        @endif
                        @if(!empty($p->downpayment))
                            <li>
                                <span class="pkg-ic"><i class="fa-solid fa-wallet"></i></span>
                                <span>Bisa DP mulai Rp{{ number_format((float) $p->downpayment, 0, ',', '.') }}</span>
                            </li>
                        @endif
                    </ul>

                    @if($pkgBgs && count($pkgBgs))
                        <div>
                            <small style="display:block;font-weight:700;opacity:.9;margin-bottom:6px">Pilihan Background:</small>
                            <div class="pkg-bgs">
                                @foreach($pkgBgs as $b)
                                    <span class="pkg-bg-pill">{{ $b->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div style="margin-top:auto;padding-top:10px">
                        <a class="btn {{ $colorClass === 'pkg-yellow' ? 'btn-blue' : 'btn-yellow' }} btn-sm" style="width:100%" href="{{ Site::get('studio_cta_url', '/booking') }}">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>{{ Site::get('studio_package_btn_label') }}</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 2. PILIHAN BACKGROUND STUDIO (DIAMBIL LANGSUNG DARI DATABASE WEBSITE BOOKING) --}}
<section class="band-y">
    <div class="container">
        <div class="center">
            <span class="eyebrow" style="background:#fff">{{ Site::get('studio_bg_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('studio_bg_title') }}</h2>
            @if(Site::get('studio_bg_subtitle'))
                <p class="lead" style="margin-top:10px;color:var(--blue-d)">{{ Site::get('studio_bg_subtitle') }}</p>
            @endif
        </div>

        <div class="grid g4" style="margin-top:34px">
            @foreach($backgrounds as $bIdx => $bg)
                @php
                    $bgMediaUrl = method_exists($bg, 'getFirstMediaUrl') ? $bg->getFirstMediaUrl('image') : ($bg->image_url ?? null);
                    $fallbackColor = $bg->color ?? $bgFallbackColors[$bIdx % count($bgFallbackColors)];
                    $bgPackages = $bg->packages ?? collect();
                @endphp
                <div class="card" style="padding:16px;text-align:center;display:flex;flex-direction:column">
                    @if($bgMediaUrl)
                        <img class="bg-thumb" src="{{ $bgMediaUrl }}" alt="{{ $bg->name }}" width="300" height="400" loading="lazy" decoding="async">
                    @else
                        <div class="bg-thumb" style="background:{{ $fallbackColor }}">
                            {{ $bg->name }}
                        </div>
                    @endif
                    <h3 style="font-size:1.3rem;margin-top:14px;margin-bottom:4px">{{ $bg->name }}</h3>
                    @if($bgPackages && count($bgPackages))
                        <p style="font-size:.82rem;font-weight:700;color:#475569;margin-top:auto">
                            Tersedia di: {{ $bgPackages->pluck('title')->join(', ') }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 3. ADD-ONS, PHOTO GRID, KEYCHAIN & JAM OPERASIONAL DARI SISTEM BOOKING --}}
@if($addons->count() || $photoGrids->count() || $keychains->count() || $operationalHours->count())
<section class="band-soft">
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('studio_addons_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('studio_addons_title') }}</h2>
            @if(Site::get('studio_addons_subtitle'))
                <p class="lead" style="margin-top:10px">{{ Site::get('studio_addons_subtitle') }}</p>
            @endif
        </div>

        <div class="grid g3" style="margin-top:34px;align-items:stretch">
            @if($addons->count())
                <div class="card">
                    <span class="tag" style="background:var(--yellow);color:var(--blue-d);padding:4px 12px;border-radius:99px;font-weight:800;font-size:.78rem;display:inline-block;margin-bottom:10px">
                        <i class="fa-solid fa-plus-circle"></i> Harga Add-ons Studio
                    </span>
                    <h3>Tambahan Layanan & Cetak</h3>
                    <ul style="list-style:none;padding:0;margin:14px 0 0;display:flex;flex-direction:column;gap:10px">
                        @foreach($addons as $addon)
                            <li style="display:flex;justify-content:space-between;align-items:center;padding-bottom:8px;border-bottom:1.5px dashed #cbd5e1;font-weight:700">
                                <span>{{ $addon->name }}</span>
                                <span style="color:var(--blue);font-family:'Sniglet',cursive,sans-serif;font-weight:800">
                                    Rp{{ number_format((float) ($addon->price - ($addon->discount ?? 0)), 0, ',', '.') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($photoGrids->count() || $keychains->count())
                <div class="card">
                    <span class="tag" style="background:var(--yellow);color:var(--blue-d);padding:4px 12px;border-radius:99px;font-weight:800;font-size:.78rem;display:inline-block;margin-bottom:10px">
                        <i class="fa-solid fa-table-cells-large"></i> Layout & Merchandise
                    </span>
                    <h3>Pilihan Grid & Keychain</h3>
                    @if($photoGrids->count())
                        <p style="font-size:.85rem;font-weight:700;margin:10px 0 6px;color:var(--blue-d)">Layout Photo Grid:</p>
                        <div style="display:flex;flex-wrap:wrap;gap:6px">
                            @foreach($photoGrids as $pg)
                                <span class="chip" style="padding:5px 12px;font-size:.8rem;box-shadow:0 2px 0 var(--blue-d)">{{ $pg->title }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($keychains->count())
                        <p style="font-size:.85rem;font-weight:700;margin:14px 0 6px;color:var(--blue-d)">Pilihan Bentuk Keychain:</p>
                        <div style="display:flex;flex-wrap:wrap;gap:6px">
                            @foreach($keychains as $kc)
                                <span class="chip" style="padding:5px 12px;font-size:.8rem;box-shadow:0 2px 0 var(--blue-d)">{{ $kc->title }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if($operationalHours->count())
                <div class="card">
                    <span class="tag" style="background:var(--yellow);color:var(--blue-d);padding:4px 12px;border-radius:99px;font-weight:800;font-size:.78rem;display:inline-block;margin-bottom:10px">
                        <i class="fa-regular fa-clock"></i> Jadwal Buka Studio
                    </span>
                    <h3>Jam Operasional Booking</h3>
                    <ul style="list-style:none;padding:0;margin:14px 0 0;display:flex;flex-direction:column;gap:8px;font-size:.9rem">
                        @foreach($operationalHours as $oh)
                            <li style="display:flex;justify-content:space-between;align-items:center;font-weight:700">
                                <span>{{ $oh->day }}</span>
                                @if($oh->is_open)
                                    <span style="color:var(--blue)">{{ substr((string) $oh->open_time, 0, 5) }} - {{ substr((string) $oh->close_time, 0, 5) }} WIB</span>
                                @else
                                    <span style="color:var(--pink)">Tutup</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- 4. SECTION CUSTOM FRAME SNAP FUN STUDIO + POPUP REGISTRASI WA --}}
<section>
    <div class="container">
        <div class="cf-box">
            <div class="grid g2" style="align-items:center;gap:36px">
                <div>
                    <span class="eyebrow">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>{{ Site::get('custom_frame_eyebrow') }}</span>
                    </span>
                    <h2 class="title">{{ Site::get('custom_frame_title') }}</h2>
                    <p class="lead" style="margin:14px 0 22px">{{ Site::get('custom_frame_subtitle') }}</p>

                    @if(!empty(Site::get('custom_frame_points')))
                        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:26px">
                            @foreach(Site::get('custom_frame_points') as $pt)
                                <div style="display:flex;align-items:flex-start;gap:12px">
                                    <span style="width:30px;height:30px;border-radius:50%;background:var(--yellow);color:var(--blue-d);border:2px solid var(--blue-d);display:inline-grid;place-items:center;flex-shrink:0;margin-top:2px">
                                        <i class="fa-solid fa-check" style="font-size:.8rem"></i>
                                    </span>
                                    <div>
                                        <strong class="cf-point-title">{{ $pt['title'] ?? '' }}</strong>
                                        <span style="font-size:.9rem;color:#e2ecff">{{ $pt['text'] ?? '' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="cta">
                        <button type="button" class="btn btn-yellow" onclick="openCustomFrameModal()">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <span>{{ Site::get('custom_frame_btn_label') }}</span>
                        </button>
                        <a class="btn btn-white" href="{{ Site::get('studio_cta_url', '/booking') }}">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Booking Jadwal Studio</span>
                        </a>
                    </div>
                </div>
                <div>
                    <div class="cf-preview">
                        <img src="{{ Site::img('custom_frame_image', 'img/site/custom-frames.png') }}" alt="Custom Frame Snap Fun Studio" width="680" height="461" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. GALERI FOTO CUSTOMER (TANPA TEKS & TANPA PEMBATAS FOTO) --}}
<section style="padding-top:10px">
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('studio_gallery_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('studio_gallery_title') }}</h2>
            @if(Site::get('studio_gallery_subtitle'))
                <p class="lead" style="margin-top:10px">{{ Site::get('studio_gallery_subtitle') }}</p>
            @endif
        </div>
        <div style="margin-top:34px">
            @if($gallery->count())
                <div class="gallery">
                    @foreach($gallery as $g)
                        <div class="gallery-item">
                            <img src="{{ Site::url($g->image) }}" alt="Galeri Snap Fun Studio" width="400" height="300" loading="lazy" decoding="async">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty">Galeri foto customer segera hadir.</div>
            @endif
        </div>
        <div class="center" style="margin-top:42px">
            <h3 style="font-size:1.75rem;color:var(--blue);margin-bottom:16px">{{ Site::get('studio_bottom_cta_title') }}</h3>
            <div class="cta" style="justify-content:center">
                <a class="btn btn-blue" href="{{ Site::get('studio_cta_url', '/booking') }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>{{ Site::get('studio_bottom_cta_label') }}</span>
                </a>
                <button type="button" class="btn btn-yellow" onclick="openCustomFrameModal()">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>{{ Site::get('custom_frame_btn_label') }}</span>
                </button>
            </div>
        </div>
    </div>
</section>
@endsection
