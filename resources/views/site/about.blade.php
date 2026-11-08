@extends('site.layout')
@php use App\Support\Site; @endphp
@section('title', Site::get('about_title') . ' - ' . Site::get('site_name'))
@section('content')
<section class="pagehead">
    <div class="container">
        <span class="eyebrow">{{ Site::get('about_eyebrow') }}</span>
        <h1 class="title">{{ Site::get('about_title') }}</h1>
        @if(Site::get('about_subtitle'))
            <p class="lead" style="margin-top:10px">{{ Site::get('about_subtitle') }}</p>
        @endif
    </div>
</section>

<section>
    <div class="container grid g2" style="align-items:center">
        <div>
            @foreach(preg_split('/\n\s*\n/', trim((string) Site::get('about_text'))) as $para)
                <p class="lead" style="margin:0 0 16px">{{ $para }}</p>
            @endforeach
        </div>
        <div class="hero-art">
            <div class="blob"></div>
            <img src="{{ Site::img('about_image', 'img/Pinpin-05.png') }}" alt="Pinpin" width="380" height="380" fetchpriority="high" decoding="async">
        </div>
    </div>
</section>

<section class="band">
    <div class="container">
        <div class="center">
            <span class="eyebrow">{{ Site::get('about_products_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('about_products_title') }}</h2>
        </div>
        <div class="grid g3" style="margin-top:32px">
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
            <span class="eyebrow">{{ Site::get('about_why_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('about_why_title') }}</h2>
        </div>
        <div class="grid g3" style="margin-top:32px">
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
        </div>
    </div>
</section>

<section class="band-y">
    <div class="container" style="max-width:860px">
        <div class="center" style="margin-bottom:28px">
            <span class="eyebrow" style="background:#fff">{{ Site::get('about_faq_eyebrow') }}</span>
            <h2 class="title">{{ Site::get('about_faq_title') }}</h2>
        </div>
        <div class="faq">
            @foreach($faqs as $idx => $f)
                <details @if($idx === 0) open @endif>
                    <summary>{{ $f['question'] }}</summary>
                    <p>{{ $f['answer'] }}</p>
                </details>
            @endforeach
        </div>
        <div class="wa-cta-banner">
            <div>
                <h3>{{ Site::get('about_faq_prompt') }}</h3>
                <p>Tim Snap Fun & Pinpin siap jawab pertanyaanmu seputar Studio, Photobox, maupun Event Booth.</p>
            </div>
            <a class="btn-wa-lg" target="_blank" rel="noopener" href="{{ Site::wa((string) Site::get('about_faq_wa_text')) }}">
                <i class="fa-brands fa-whatsapp"></i>
                <span>{{ Site::get('about_faq_cta_label') }}</span>
            </a>
        </div>
    </div>
</section>
@endsection
