@extends('site.layout')
@php use App\Support\Site; @endphp
@section('preload')
    <link rel="preload" as="image" href="{{ Site::img('hero_image', 'img/Pinpin-01.png') }}" fetchpriority="high">
@endsection
@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">{{ Site::get('hero_eyebrow') }}</span>
            <h1>{!! nl2br(e(Site::get('hero_title'))) !!}</h1>
            <p class="lead">{{ Site::get('hero_subtitle') }}</p>
            <div class="cta">
                <a class="btn btn-yellow" href="{{ Site::get('hero_btn1_url', '/booking') }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>{{ Site::get('hero_btn1_label') }}</span>
                </a>
                <a class="btn btn-green" target="_blank" rel="noopener" href="{{ Site::wa((string) Site::get('hero_btn2_wa_text')) }}">
                    <i class="fa-brands fa-whatsapp" style="font-size:1.15rem"></i>
                    <span>{{ Site::get('hero_btn2_label') }}</span>
                </a>
            </div>
            @if(!empty(Site::get('hero_highlights')))
                <div class="hero-highlights">
                    @foreach(Site::get('hero_highlights') as $hl)
                        <div class="hl-item">
                            <strong>{{ $hl['title'] ?? '' }}</strong>
                            <span>{{ $hl['text'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="hero-art">
            <div class="blob"></div>
            @if(Site::get('hero_badge'))
                <span class="badge">{{ Site::get('hero_badge') }}</span>
            @endif
            <img src="{{ Site::img('hero_image', 'img/Pinpin-01.png') }}" alt="Pinpin, maskot Snap Fun" width="380" height="380" fetchpriority="high" decoding="async">
        </div>
    </div>
    <div class="wave"><svg viewBox="0 0 1200 46" preserveAspectRatio="none"><path d="M0 46V20C150 0 300 40 450 24s300-30 450-8 200 18 300 4v26z" fill="#fff"/></svg></div>
</section>

<section>
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('home_promo_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('home_promo_title') }}</h2>
            @if(Site::get('home_promo_subtitle'))
                <p class="lead" style="margin-top:10px">{{ Site::get('home_promo_subtitle') }}</p>
            @endif
        </div>

        @if($promos->isNotEmpty())
            <div class="promo-slider" id="promoSlider">
                @if($promos->count() > 1)
                    <button type="button" class="promo-arrow prev" aria-label="Promo Sebelumnya" onclick="promoSlideStep(-1)">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" class="promo-arrow next" aria-label="Promo Berikutnya" onclick="promoSlideStep(1)">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                @endif

                <div class="promo-viewport">
                    <div class="promo-track" id="promoTrack">
                        @foreach($promos as $p)
                            @php
                                $bgUrl = $p->image ? Site::url($p->image) : asset('img/site/why-photo.jpg');
                                $hasText = !empty($p->title) || !empty($p->badge) || !empty($p->description);
                            @endphp
                            <div class="promo-slide {{ $hasText ? '' : 'image-only' }}" style="background-image: url('{{ $bgUrl }}')">
                                <div class="promo-slide-inner">
                                    <div class="promo-slide-copy">
                                        @if($p->badge)
                                            <span class="pb">{{ $p->badge }}</span>
                                        @endif
                                        @if($p->title)
                                            <h3>{{ $p->title }}</h3>
                                        @endif
                                        @if($p->description)
                                            <p>{{ $p->description }}</p>
                                        @endif
                                        @if($p->ends_at)
                                            <span class="promo-exp">
                                                <i class="fa-regular fa-clock"></i>
                                                <span>Berlaku s.d. {{ $p->ends_at->translatedFormat('d F Y') }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    @if($p->link_url)
                                        <div>
                                            <a class="btn btn-yellow" href="{{ $p->link_url }}">
                                                <span>{{ $p->link_label ?: 'Booking Sekarang' }}</span>
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($promos->count() > 1)
                    <div class="promo-dots" id="promoDots">
                        @foreach($promos as $idx => $p)
                            <button type="button" class="promo-dot {{ $idx === 0 ? 'active' : '' }}" aria-label="Slide {{ $idx + 1 }}" onclick="promoSlideGo({{ $idx }})"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($promos->count() > 1)
                <script>
                (function() {
                    var current = 0;
                    var total = {{ $promos->count() }};
                    var track = document.getElementById('promoTrack');
                    var dotsWrap = document.getElementById('promoDots');
                    var slider = document.getElementById('promoSlider');
                    var timer = null;

                    function render() {
                        if (!track) return;
                        track.style.transform = 'translateX(-' + (current * 100) + '%)';
                        if (dotsWrap) {
                            var dots = dotsWrap.querySelectorAll('.promo-dot');
                            for (var i = 0; i < dots.length; i++) {
                                dots[i].classList.toggle('active', i === current);
                            }
                        }
                    }

                    window.promoSlideGo = function(index) {
                        current = (index + total) % total;
                        render();
                        restart();
                    };

                    window.promoSlideStep = function(delta) {
                        window.promoSlideGo(current + delta);
                    };

                    function restart() {
                        if (timer) clearInterval(timer);
                        timer = setInterval(function() {
                            current = (current + 1) % total;
                            render();
                        }, 4500);
                    }

                    if (slider) {
                        slider.addEventListener('mouseenter', function() { if (timer) clearInterval(timer); });
                        slider.addEventListener('mouseleave', restart);
                    }
                    restart();
                })();
                </script>
            @endif
        @else
            <div class="empty" style="margin-top:32px">
                {{ Site::get('home_promo_empty') }}
                <a href="{{ Site::instagramUrl() }}" target="_blank" rel="noopener" style="text-decoration:underline">Cek &#64;{{ ltrim((string) Site::get('instagram'), '@') }}</a>
            </div>
        @endif
    </div>
</section>

{{-- HIGHLIGHT CUSTOM FRAME DI BERANDA --}}
<section style="padding-top:10px">
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
                        <a class="btn btn-white" href="{{ route('site.studio') }}">
                            <span>Lihat Snap Fun Studio</span>
                            <i class="fa-solid fa-arrow-right"></i>
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

<section class="band">
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('home_products_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('home_products_title') }}</h2>
            @if(Site::get('home_products_subtitle'))
                <p class="lead" style="margin-top:10px">{{ Site::get('home_products_subtitle') }}</p>
            @endif
        </div>
        <div class="grid g3" style="margin-top:36px">
            <a href="{{ route('site.studio') }}" class="card product">
                <div class="subbrand-head">
                    <span class="tag">{{ Site::get('studio_tag') }}</span>
                    <span class="subbrand-badge">Studio</span>
                </div>
                <h3>{{ Site::get('studio_card_title') }}</h3>
                <p>{{ Site::get('studio_summary') }}</p>
                <span class="more">
                    <span>{{ Site::get('studio_more_label') }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
            <a href="{{ route('site.booth') }}" class="card product">
                <div class="subbrand-head">
                    <span class="tag">{{ Site::get('booth_tag') }}</span>
                    <span class="subbrand-badge">Booth</span>
                </div>
                <h3>{{ Site::get('booth_card_title') }}</h3>
                <p>{{ Site::get('booth_summary') }}</p>
                <span class="more">
                    <span>{{ Site::get('booth_more_label') }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
            <a href="{{ route('site.express') }}" class="card product">
                <div class="subbrand-head">
                    <span class="tag">{{ Site::get('express_tag') }}</span>
                    <span class="subbrand-badge">Express</span>
                </div>
                <h3>{{ Site::get('express_card_title') }}</h3>
                <p>{{ Site::get('express_summary') }}</p>
                <span class="more">
                    <span>{{ Site::get('express_more_label') }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('home_why_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('home_why_title') }}</h2>
            @if(Site::get('home_why_subtitle'))
                <p class="lead" style="margin-top:10px">{{ Site::get('home_why_subtitle') }}</p>
            @endif
        </div>
        <div class="grid g3" style="margin-top:36px;align-items:stretch">
            @foreach(Site::get('why_us') as $i => $w)
                <div class="card">
                    <div class="why-head">
                        <span class="num">{{ $i + 1 }}</span>
                        @if(!empty($w['tag']))
                            <span class="why-tag">{{ $w['tag'] }}</span>
                        @endif
                    </div>
                    <h3>{{ $w['title'] ?? '' }}</h3>
                    <p>{{ $w['text'] ?? '' }}</p>
                </div>
            @endforeach
            @if(Site::bool('why_us_show_mascot'))
                <div class="card y" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:18px">
                    <img src="{{ Site::img('why_us_image', 'img/Pinpin-01.png') }}" alt="Karakter Snap Fun" width="170" height="170" loading="lazy" decoding="async" style="max-height:170px;width:auto;object-fit:contain">
                </div>
            @endif
        </div>
    </div>
</section>

<section class="band-y">
    <div class="container center">
        <span class="eyebrow" style="background:#fff">{{ Site::get('home_events_eyebrow') }}</span>
        <h2 class="title">{{ Site::get('home_events_title') }}</h2>
        <div class="event-grid" style="margin-top:32px">
            @foreach(Site::get('events') as $e)
                <div class="event-card">
                    @if(!empty($e['logo']))
                        <img src="{{ Site::url($e['logo']) }}" alt="{{ $e['name'] ?? '' }}" width="180" height="76" loading="lazy" decoding="async">
                    @endif
                    <span>{{ $e['name'] ?? '' }}</span>
                </div>
            @endforeach
        </div>
        @if(Site::get('home_events_more_chip'))
            <div style="margin-top:20px">
                <span class="chip">{{ Site::get('home_events_more_chip') }}</span>
            </div>
        @endif
        <div class="cta" style="justify-content:center;margin-top:30px">
            <a class="btn btn-blue" href="{{ route('site.booth') }}">
                <span>{{ Site::get('home_events_btn_label') }}</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section>
    <div class="container grid g2" style="align-items:center">
        <div>
            <span class="eyebrow">{{ Site::get('home_sirkel_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('home_sirkel_title') }}</h2>
            <p class="lead" style="margin:14px 0 24px">{{ Site::get('sirkel_intro') }}</p>
            <a class="btn btn-yellow" href="{{ route('site.sirkel') }}">
                <i class="fa-solid fa-users"></i>
                <span>{{ Site::get('home_sirkel_btn_label') }}</span>
            </a>
        </div>
        <div class="card b">
            <h3>{{ Site::get('home_visit_title') }}</h3>
            @if(Site::get('home_visit_subtitle'))
                <p style="margin-bottom:12px;opacity:.92">{{ Site::get('home_visit_subtitle') }}</p>
            @endif
            <p style="display:flex;align-items:center;gap:10px">
                <i class="fa-solid fa-location-dot" style="color:var(--yellow)"></i>
                <b>{{ Site::get('address') }}</b>
            </p>
            @if(Site::get('operational_hours'))
                <p style="margin-top:8px;display:flex;align-items:center;gap:10px">
                    <i class="fa-regular fa-clock" style="color:var(--yellow)"></i>
                    <span>{{ Site::get('operational_hours') }}</span>
                </p>
            @endif
            @if(Site::get('maps_embed_url'))
                <iframe class="map-frame" src="{{ Site::get('maps_embed_url') }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
            <div class="cta" style="margin-top:18px">
                @if(Site::get('maps_url'))
                    <a class="btn btn-yellow btn-sm" target="_blank" rel="noopener" href="{{ Site::get('maps_url') }}">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>{{ Site::get('home_visit_maps_label') }}</span>
                    </a>
                @endif
                <a class="btn btn-white btn-sm" href="{{ route('site.contact') }}">
                    <i class="fa-solid fa-headset"></i>
                    <span>{{ Site::get('home_visit_contact_label') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
