@extends('site.layout')
@php use App\Support\Site; @endphp
@section('title', Site::get('express_title') . ' - ' . Site::get('site_name'))
@section('content')
<section class="pagehead">
    <div class="container">
        <span class="eyebrow">{{ Site::get('express_eyebrow') }}</span>
        <h1 class="title">{{ Site::get('express_title') }}</h1>
    </div>
</section>

<section>
    <div class="container grid g2" style="align-items:center">
        <div>
            @foreach(preg_split('/\n\s*\n/', trim((string) Site::get('express_text'))) as $para)
                <p class="lead" style="font-size:1.15rem;margin:0 0 16px">{{ $para }}</p>
            @endforeach
            <div class="cta" style="margin-top:26px">
                <a class="btn btn-green" target="_blank" rel="noopener" href="{{ Site::wa((string) Site::get('express_wa_text')) }}">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ Site::get('express_cta_label') }}</span>
                </a>
            </div>
        </div>
        <div class="hero-art">
            <div class="blob"></div>
            @if(Site::get('express_badge'))
                <span class="badge">{{ Site::get('express_badge') }}</span>
            @endif
            <img src="{{ Site::img('express_image', 'img/Pinpin-05.png') }}" alt="{{ Site::get('express_title') }}">
        </div>
    </div>
</section>

@if(!empty(Site::get('express_highlights')))
<section class="band-y">
    <div class="container">
        <div class="grid g3">
            @foreach(Site::get('express_highlights') as $i => $h)
                <div class="card">
                    <span class="num">{{ $i + 1 }}</span>
                    <h3>{{ $h['title'] ?? '' }}</h3>
                    <p>{{ $h['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
