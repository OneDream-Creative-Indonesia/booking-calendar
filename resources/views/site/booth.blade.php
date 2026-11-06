@extends('site.layout')
@php
    use App\Support\Site;
    $waEvent = Site::wa((string) Site::get('booth_wa_text'));
@endphp
@section('title', Site::get('booth_title') . ' - ' . Site::get('site_name'))
@section('content')
<section class="pagehead">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">{{ Site::get('booth_eyebrow') }}</span>
            <h1 class="title">{{ Site::get('booth_title') }}</h1>
            <p class="lead" style="margin-top:14px">{{ Site::get('booth_text') }}</p>
            <div class="cta" style="margin-top:24px">
                <a class="btn btn-yellow" target="_blank" rel="noopener" href="{{ $waEvent }}">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ Site::get('booth_cta_label') }}</span>
                </a>
            </div>
        </div>
        <div class="hero-art">
            <div class="blob"></div>
            <img src="{{ Site::img('booth_hero_image', 'img/Pinpin-01.png') }}" alt="{{ Site::get('booth_title') }}">
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('booth_offers_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('booth_offers_title') }}</h2>
        </div>
        <div class="grid g4" style="margin-top:34px">
            @foreach(Site::get('booth_offers') as $i => $o)
                <div class="card">
                    <span class="num">{{ $i + 1 }}</span>
                    <h3>{{ $o['title'] ?? '' }}</h3>
                    <p>{{ $o['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="band-soft">
    <div class="container grid g2" style="align-items:center">
        <div>
            <span class="eyebrow">{{ Site::get('booth_frames_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('booth_frames_title') }}</h2>
            <p class="lead" style="margin-top:14px">{{ Site::get('booth_frames_text') }}</p>
            <div class="cta" style="margin-top:24px">
                <a class="btn btn-blue" target="_blank" rel="noopener" href="{{ $waEvent }}">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Konsultasi Desain Frame Event</span>
                </a>
            </div>
        </div>
        <div>
            <img src="{{ Site::img('booth_frames_image', 'img/site/custom-frames.png') }}" alt="Customizable Frame Snap Fun" style="border-radius:18px;border:3px solid var(--blue-d);box-shadow:0 8px 0 var(--blue-d);background:#fff;padding:12px" loading="lazy">
        </div>
    </div>
</section>

<section class="band">
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('booth_systems_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('booth_systems_title') }}</h2>
            <p class="lead" style="margin-top:8px">{{ Site::get('booth_systems_subtitle') }}</p>
        </div>
        <div class="grid g4" style="margin-top:34px;align-items:stretch">
            <div class="card" style="display:flex;align-items:center;justify-content:center;padding:16px;background:#fff">
                <img src="{{ Site::img('booth_systems_image', 'img/site/pinpin-blush.png') }}" alt="Pinpin" style="max-height:210px;width:auto;object-fit:contain" loading="lazy">
            </div>
            @foreach(Site::get('booth_systems') as $s)
                <div class="card y" style="display:flex;flex-direction:column;justify-content:center">
                    <h3 style="color:var(--blue-d);font-size:1.65rem">{{ $s['title'] ?? '' }}</h3>
                    <p style="color:var(--blue-d);margin-top:6px;font-weight:600">{{ $s['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="center" style="margin-bottom:28px">
            <span class="eyebrow">{{ Site::get('booth_req_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('booth_req_title') }}</h2>
        </div>

        <div class="booth-banner">
            <img src="{{ Site::img('booth_req_banner', 'img/site/booth-banner.jpg') }}" alt="Pop Up Self Photo Booth Snap Fun" loading="lazy">
        </div>

        <div class="grid g3">
            @foreach(Site::get('booth_requirements') as $r)
                <div class="card req-card center">
                    <h3>{{ $r['title'] ?? '' }}</h3>
                    <p style="font-weight:600">{{ $r['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>

        <div style="margin-top:52px">
            <div class="center" style="margin-bottom:24px">
                <span class="eyebrow">{{ Site::get('home_events_eyebrow') }}</span>
                <h3 style="font-size:2rem;color:var(--blue)">{{ Site::get('home_events_title') }}</h3>
            </div>
            <div class="event-grid">
                @foreach(Site::get('events') as $e)
                    <div class="event-card">
                        @if(!empty($e['logo']))
                            <img src="{{ Site::url($e['logo']) }}" alt="{{ $e['name'] ?? '' }}" loading="lazy">
                        @endif
                        <span>{{ $e['name'] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- GALERI DOKUMENTASI EVENT (TANPA TEKS & TANPA PEMBATAS FOTO) --}}
<section class="band-y">
    <div class="container">
        <div class="center">
            <span class="eyebrow" style="background:#fff">{{ Site::get('booth_gallery_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('booth_gallery_title') }}</h2>
        </div>
        <div style="margin-top:34px">
            @if($gallery->count())
                <div class="gallery">
                    @foreach($gallery as $g)
                        <div class="gallery-item">
                            <img src="{{ Site::url($g->image) }}" alt="Dokumentasi Event Snap Fun Booth" loading="lazy">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty" style="background:#fff">Dokumentasi event segera hadir.</div>
            @endif
        </div>
    </div>
</section>

<section>
    <div class="container center" style="max-width:780px">
        <h2 class="title">{{ Site::get('booth_closing_title') }}</h2>
        <p class="lead" style="margin:14px auto 26px">{{ Site::get('booth_closing_subtitle') }}</p>
        <a class="btn btn-green" target="_blank" rel="noopener" href="{{ $waEvent }}">
            <i class="fa-brands fa-whatsapp"></i>
            <span>{{ Site::get('booth_closing_btn_label') }}</span>
        </a>
    </div>
</section>
@endsection
