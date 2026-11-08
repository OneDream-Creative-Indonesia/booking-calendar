@extends('site.layout')
@php use App\Support\Site; @endphp
@section('title', Site::get('contact_title') . ' - ' . Site::get('site_name'))
@section('content')
<section class="pagehead">
    <div class="container">
        <span class="eyebrow">{{ Site::get('contact_eyebrow') }}</span>
        <h1 class="title">{{ Site::get('contact_title') }}</h1>
        <p class="lead" style="margin-top:14px">{{ Site::get('contact_subtitle') }}</p>
    </div>
</section>

<section>
    <div class="container">
        <div class="grid g3">
            @foreach(Site::get('contact_cards') as $card)
                @php
                    $isWa = ($card['type'] ?? 'wa') === 'wa';
                    $href = $isWa ? Site::wa((string) ($card['target'] ?? '')) : ($card['target'] ?? '#');
                    $btnStyle = $card['btn_style'] ?? 'btn-green';
                @endphp
                <div class="card product" style="min-height:210px">
                    <h3>{{ $card['title'] ?? '' }}</h3>
                    <p style="margin-bottom:16px">{{ $card['text'] ?? '' }}</p>
                    <a class="btn {{ $btnStyle }} btn-sm" style="align-self:flex-start;margin-top:auto"
                       @if($isWa) target="_blank" rel="noopener" @endif
                       href="{{ $href }}">
                        @if($isWa)
                            <i class="fa-brands fa-whatsapp"></i>
                        @else
                            <i class="fa-solid fa-arrow-right"></i>
                        @endif
                        <span>{{ Site::get('strip', $card['btn_label'] ?? 'Hubungi') }}</span>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="grid g2" style="margin-top:38px;align-items:center">
            <div class="card b">
                <h3>{{ Site::get('contact_box_title') }}</h3>
                @if(Site::get('contact_box_subtitle'))
                    <p style="margin:8px 0 16px;opacity:.92">{{ Site::get('contact_box_subtitle') }}</p>
                @endif
                @if(Site::get('contact_person_label'))
                    <p style="margin-bottom:10px;display:flex;align-items:center;gap:10px">
                        <i class="fa-solid fa-user" style="color:var(--yellow);width:18px;text-align:center"></i>
                        <b>{{ Site::get('contact_person_label') }}</b>
                    </p>
                @endif
                <p style="margin-bottom:10px;display:flex;align-items:center;gap:10px">
                    <i class="fa-brands fa-whatsapp" style="color:var(--yellow);width:18px;text-align:center"></i>
                    <span>WhatsApp: <a style="text-decoration:underline;font-weight:700" target="_blank" rel="noopener" href="{{ Site::wa((string) Site::get('wa_float_text')) }}">{{ Site::waDisplay() }}</a></span>
                </p>
                <p style="margin-bottom:10px;display:flex;align-items:center;gap:10px">
                    <i class="fa-brands fa-instagram" style="color:var(--yellow);width:18px;text-align:center"></i>
                    <span>Instagram: <a style="text-decoration:underline;font-weight:700" target="_blank" rel="noopener" href="{{ Site::instagramUrl() }}">&#64;{{ ltrim((string) Site::get('instagram'), '@') }}</a></span>
                </p>
                @if(Site::get('tiktok'))
                    <p style="margin-bottom:10px;display:flex;align-items:center;gap:10px">
                        <i class="fa-brands fa-tiktok" style="color:var(--yellow);width:18px;text-align:center"></i>
                        <span>TikTok: <a style="text-decoration:underline;font-weight:700" target="_blank" rel="noopener" href="{{ Site::tiktokUrl() }}">&#64;{{ ltrim((string) Site::get('tiktok'), '@') }}</a></span>
                    </p>
                @endif
                @if(Site::get('email'))
                    <p style="margin-bottom:10px;display:flex;align-items:center;gap:10px">
                        <i class="fa-regular fa-envelope" style="color:var(--yellow);width:18px;text-align:center"></i>
                        <span>Email: <a style="text-decoration:underline;font-weight:700" href="mailto:{{ Site::get('email') }}">{{ Site::get('email') }}</a></span>
                    </p>
                @endif
                @if(Site::get('operational_hours'))
                    <p style="margin-bottom:10px;display:flex;align-items:center;gap:10px">
                        <i class="fa-regular fa-clock" style="color:var(--yellow);width:18px;text-align:center"></i>
                        <span>Jam Operasional: {{ Site::get('operational_hours') }}</span>
                    </p>
                @endif
                <p style="display:flex;align-items:center;gap:10px">
                    <i class="fa-solid fa-location-dot" style="color:var(--yellow);width:18px;text-align:center"></i>
                    <span>{{ Site::get('address') }}</span>
                </p>

                @if(Site::get('maps_embed_url'))
                    <iframe class="map-frame" src="{{ Site::get('maps_embed_url') }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                @endif

                @if(Site::get('maps_url'))
                    <p style="margin-top:16px">
                        <a class="btn btn-yellow btn-sm" target="_blank" rel="noopener" href="{{ Site::get('maps_url') }}">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>{{ Site::get('home_visit_maps_label') }}</span>
                        </a>
                    </p>
                @endif
            </div>
            <div class="hero-art">
                <div class="blob"></div>
                <img src="{{ Site::img('contact_image', 'img/Pinpin-01.png') }}" alt="Pinpin" width="380" height="380" loading="lazy" decoding="async">
            </div>
        </div>
    </div>
</section>
@endsection
