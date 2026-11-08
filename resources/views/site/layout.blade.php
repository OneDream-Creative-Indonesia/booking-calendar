@php
    use App\Support\Site;
    $modalPackages = Site::bookingPackages();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', Site::get('site_title'))</title>
    <meta name="description" content="@yield('description', Site::get('site_description'))">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="apple-touch-icon" href="/img/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    @yield('preload')
    <link href="https://fonts.googleapis.com/css2?family=Sniglet:wght@400;800&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v=5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"></noscript>
</head>
<body>
@php $is = fn (...$r) => request()->routeIs(...$r) ? 'active' : ''; @endphp
<header class="nav">
    <div class="container nav-in">
        <a href="{{ route('site.home') }}" class="nav-logo">
            <img src="{{ Site::img('nav_logo', 'img/logo/Logo Snapfun-01.png') }}" alt="{{ Site::get('site_name') }}" width="54" height="54" fetchpriority="high" decoding="async">
        </a>
        <button class="burger" aria-label="Menu" onclick="document.getElementById('menu').classList.toggle('open')">
            <i class="fa-solid fa-bars"></i>
        </button>
        <ul class="menu" id="menu">
            <li><a class="link {{ $is('site.home') }}" href="{{ route('site.home') }}">{{ Site::get('nav_home_label') }}</a></li>
            <li><a class="link {{ $is('site.about') }}" href="{{ route('site.about') }}">{{ Site::get('nav_about_label') }}</a></li>
            <li class="has-drop" onclick="this.classList.toggle('open')">
                <a class="link {{ $is('site.studio', 'site.booth', 'site.express') }}" href="javascript:void(0)">
                    <span>{{ Site::get('nav_product_label') }}</span>
                    <i class="fa-solid fa-chevron-down" style="font-size:.75rem"></i>
                </a>
                <div class="drop">
                    <a href="{{ route('site.studio') }}"><span>{{ Site::get('nav_studio_label') }}</span><i class="fa-solid fa-arrow-right" style="font-size:.8rem"></i></a>
                    <a href="{{ route('site.booth') }}"><span>{{ Site::get('nav_booth_label') }}</span><i class="fa-solid fa-arrow-right" style="font-size:.8rem"></i></a>
                    <a href="{{ route('site.express') }}"><span>{{ Site::get('nav_express_label') }}</span><i class="fa-solid fa-arrow-right" style="font-size:.8rem"></i></a>
                </div>
            </li>
            <li><a class="link {{ $is('site.sirkel') }}" href="{{ route('site.sirkel') }}">{{ Site::get('nav_sirkel_label') }}</a></li>
            <li><a class="link {{ $is('site.contact') }}" href="{{ route('site.contact') }}">{{ Site::get('nav_contact_label') }}</a></li>
            <li>
                <a class="btn btn-yellow btn-sm" href="{{ Site::get('nav_booking_url', '/booking') }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>{{ Site::get('nav_booking_label') }}</span>
                </a>
            </li>
        </ul>
    </div>
</header>

<main>@yield('content')</main>

<footer>
    <div class="container">
        <div class="fgrid">
            <div>
                <img src="{{ Site::img('footer_logo', 'img/logo/Logo Snapfun-02.png') }}" alt="{{ Site::get('site_name') }}" width="68" height="68" loading="lazy" decoding="async" style="height:68px;width:auto;margin-bottom:12px">
                <p>{{ Site::get('footer_tagline') }}@if(Site::get('footer_subtext'))<br><small>{{ Site::get('footer_subtext') }}</small>@endif</p>
            </div>
            <div>
                <h4>{{ Site::get('footer_col1_title') }}</h4>
                <ul>
                    <li><a href="{{ route('site.home') }}">{{ Site::get('nav_home_label') }}</a></li>
                    <li><a href="{{ route('site.about') }}">{{ Site::get('nav_about_label') }}</a></li>
                    <li><a href="{{ route('site.sirkel') }}">{{ Site::get('nav_sirkel_label') }}</a></li>
                    <li><a href="{{ route('site.contact') }}">{{ Site::get('nav_contact_label') }}</a></li>
                </ul>
            </div>
            <div>
                <h4>{{ Site::get('footer_col2_title') }}</h4>
                <ul>
                    <li><a href="{{ route('site.studio') }}">{{ Site::get('nav_studio_label') }}</a></li>
                    <li><a href="{{ route('site.booth') }}">{{ Site::get('nav_booth_label') }}</a></li>
                    <li><a href="{{ route('site.express') }}">{{ Site::get('nav_express_label') }}</a></li>
                    <li><a href="{{ Site::get('nav_booking_url', '/booking') }}">{{ Site::get('nav_booking_label') }}</a></li>
                </ul>
            </div>
            <div>
                <h4>{{ Site::get('footer_col3_title') }}</h4>
                <ul>
                    <li>
                        <i class="fa-brands fa-whatsapp" style="color:var(--yellow)"></i>
                        <a href="{{ Site::wa(Site::get('wa_float_text')) }}" target="_blank" rel="noopener">{{ Site::waDisplay() }}</a>
                    </li>
                    <li>
                        <i class="fa-brands fa-instagram" style="color:var(--yellow)"></i>
                        <a href="{{ Site::instagramUrl() }}" target="_blank" rel="noopener">&#64;{{ ltrim((string) Site::get('instagram'), '@') }}</a>
                    </li>
                    @if(Site::get('tiktok'))
                        <li>
                            <i class="fa-brands fa-tiktok" style="color:var(--yellow)"></i>
                            <a href="{{ Site::tiktokUrl() }}" target="_blank" rel="noopener">&#64;{{ ltrim((string) Site::get('tiktok'), '@') }}</a>
                        </li>
                    @endif
                    @if(Site::get('operational_hours'))
                        <li>
                            <i class="fa-regular fa-clock" style="color:var(--yellow)"></i>
                            <span>{{ Site::get('operational_hours') }}</span>
                        </li>
                    @endif
                    <li>
                        <i class="fa-solid fa-location-dot" style="color:var(--yellow)"></i>
                        <span>{{ Site::get('address') }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="copy">&copy; {{ date('Y') }} {{ Site::get('footer_copyright') }}</div>
    </div>
</footer>

{{-- POPUP MODAL REGISTRASI CUSTOM FRAME SNAP FUN STUDIO --}}
<div class="cf-modal-backdrop" id="customFrameModal" onclick="if(event.target === this) closeCustomFrameModal()">
    <div class="cf-modal" role="dialog" aria-modal="true" aria-labelledby="cfModalTitle">
        <button type="button" class="cf-close" onclick="closeCustomFrameModal()" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <span class="eyebrow">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>{{ Site::get('custom_frame_badge') }}</span>
        </span>
        <h3 id="cfModalTitle" style="font-size:1.75rem;color:var(--blue);margin-bottom:8px">{{ Site::get('custom_frame_modal_title') }}</h3>
        <p style="font-size:.92rem;color:#3c3f5c;margin:0 0 18px">{{ Site::get('custom_frame_modal_subtitle') }}</p>

        <form method="POST" action="{{ route('site.custom-frame.store') }}">
            @csrf
            <div class="row2">
                <div class="f">
                    <label for="cf_name">Nama Lengkap *</label>
                    <input id="cf_name" name="name" placeholder="Nama lengkap kamu" required>
                </div>
                <div class="f">
                    <label for="cf_whatsapp">Nomor WhatsApp *</label>
                    <input id="cf_whatsapp" name="whatsapp" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>
            <div class="row2">
                <div class="f">
                    <label for="cf_date">Rencana Tanggal Sesi Foto *</label>
                    <input id="cf_date" type="date" name="session_date" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="f">
                    <label for="cf_package">Pilih Paket Studio</label>
                    <select id="cf_package" name="package_name">
                        @foreach($modalPackages as $mp)
                            <option value="{{ $mp->title }}">{{ $mp->title }} (Rp {{ number_format((float) $mp->price, 0, ',', '.') }})</option>
                        @endforeach
                        <option value="Custom / Konsultasi Paket">Custom / Konsultasi Paket</option>
                    </select>
                </div>
            </div>
            <div class="f">
                <label for="cf_theme">Tema / Konsep Custom Frame *</label>
                <input id="cf_theme" name="frame_theme" placeholder="cth: Birthday, Graduation, Idol/Fandom, Anniversary, Geng Bestie" required>
            </div>
            <div class="f">
                <label for="cf_text">Tulisan / Judul yang Ingin Dimasukkan di Frame</label>
                <input id="cf_text" name="frame_text" placeholder="cth: Happy 21st Birthday Alya! / Class of 2026">
            </div>
            <div class="f">
                <label for="cf_notes">Warna Dominan / Referensi Desain (Opsional)</label>
                <textarea id="cf_notes" name="notes" rows="2" placeholder="Ceritakan warna favorit, karakter, atau konsep desain yang diinginkan..."></textarea>
            </div>
            <button type="submit" class="btn btn-green" style="width:100%">
                <i class="fa-brands fa-whatsapp" style="font-size:1.15rem"></i>
                <span>{{ Site::get('custom_frame_modal_btn_label') }}</span>
            </button>
        </form>
    </div>
</div>

@if(Site::bool('wa_float_enabled'))
    <a class="wa-float" href="{{ Site::wa((string) Site::get('wa_float_text')) }}" target="_blank" rel="noopener" aria-label="WhatsApp" title="Chat Pinpin via WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
        <span>{{ Site::get('wa_float_label', 'Chat Pinpin') }}</span>
    </a>
@endif

<script>
function openCustomFrameModal() {
    var m = document.getElementById('customFrameModal');
    if (m) {
        m.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}
function closeCustomFrameModal() {
    var m = document.getElementById('customFrameModal');
    if (m) {
        m.classList.remove('open');
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCustomFrameModal();
});
</script>
</body>
</html>
