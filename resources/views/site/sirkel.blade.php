@extends('site.layout')
@php use App\Support\Site; @endphp
@section('title', Site::get('sirkel_title') . ' - ' . Site::get('site_name'))
@section('content')
<section class="pagehead">
    <div class="container">
        <span class="eyebrow">{{ Site::get('sirkel_eyebrow') }}</span>
        <h1 class="title">{{ Site::get('sirkel_title') }}</h1>
        <p class="lead" style="margin-top:14px">{{ Site::get('sirkel_intro') }}</p>
    </div>
</section>

<section>
    <div class="container grid g2" style="align-items:start">
        <div>
            <span class="eyebrow">{{ Site::get('sirkel_benefit_eyebrow') }}</span>
            <h2 class="title" style="margin-bottom:18px">{{ Site::get('sirkel_benefit_title') }}</h2>
            <ul class="checks">
                @foreach(Site::get('sirkel_benefits') as $b)
                    <li><b>{{ $b['title'] ?? '' }}</b>@if(!empty($b['text'])) - {{ $b['text'] }}@endif</li>
                @endforeach
            </ul>
            <div class="hero-art" style="margin-top:32px;max-width:300px">
                <div class="blob"></div>
                <img src="{{ Site::img('sirkel_image', 'img/Pinpin-01.png') }}" alt="Sirkel Snap Fun" width="300" height="300" loading="lazy" decoding="async">
            </div>
        </div>
        <div class="card">
            <h3>{{ Site::get('sirkel_form_title') }}</h3>
            <p style="margin-bottom:18px">{{ Site::get('sirkel_form_subtitle') }}</p>
            <form method="POST" action="{{ route('site.sirkel.store') }}">
                @csrf
                <div class="f">
                    <label for="name">Nama Lengkap *</label>
                    <input id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkapmu" required>
                    @error('name')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="row2">
                    <div class="f">
                        <label for="age">Usia</label>
                        <input id="age" type="number" name="age" value="{{ old('age') }}" min="10" max="80" placeholder="cth: 20">
                        @error('age')<span class="err">{{ $message }}</span>@enderror
                    </div>
                    <div class="f">
                        <label for="city">Domisili</label>
                        <input id="city" name="city" value="{{ old('city') }}" placeholder="cth: Kota Serang">
                        @error('city')<span class="err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="f">
                    <label for="whatsapp">Nomor WhatsApp Aktif *</label>
                    <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" required>
                    @error('whatsapp')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="row2">
                    <div class="f">
                        <label for="instagram">Username Instagram</label>
                        <input id="instagram" name="instagram" value="{{ old('instagram') }}" placeholder="@username">
                        @error('instagram')<span class="err">{{ $message }}</span>@enderror
                    </div>
                    <div class="f">
                        <label for="tiktok">Username TikTok</label>
                        <input id="tiktok" name="tiktok" value="{{ old('tiktok') }}" placeholder="@username">
                        @error('tiktok')<span class="err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="f">
                    <label for="reason">Kenapa pengen gabung Sirkel Snap Fun?</label>
                    <textarea id="reason" name="reason" rows="4" placeholder="Ceritain singkat alasan kamu pengen gabung...">{{ old('reason') }}</textarea>
                    @error('reason')<span class="err">{{ $message }}</span>@enderror
                </div>
                <button class="btn btn-yellow" type="submit" style="width:100%">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ Site::get('sirkel_form_btn_label') }}</span>
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
