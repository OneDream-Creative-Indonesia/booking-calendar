<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Snap Fun Studio – Booking Sesi Foto</title>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
<link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
  html,body{margin:0;padding:0;background:#E6EEFA;font-family:'Plus Jakarta Sans',sans-serif;color:#111;min-height:100vh}
  button,input,select{font-family:inherit}

  /* App Shell: Mobile-first full height, centered mobile frame on desktop */
  .app-shell{
    max-width:440px;
    min-height:100vh;
    margin:0 auto;
    background:#F3F6FD;
    position:relative;
    display:flex;
    flex-direction:column;
    box-shadow:0 0 40px rgba(16,32,63,.10);
    overflow-x:hidden;
  }

  /* Keyframe Animations from Design Spec */
  @keyframes rise{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
  @keyframes pop{0%{opacity:0;transform:scale(.85)}70%{opacity:1;transform:scale(1.04)}100%{opacity:1;transform:scale(1)}}
  @keyframes slideup{from{opacity:0;transform:translateY(48px)}to{opacity:1;transform:none}}
  @keyframes fade{from{opacity:0}to{opacity:1}}
  @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}

  .rise{animation:rise .55s cubic-bezier(.2,.8,.2,1) both}
  .pop{animation:pop .45s cubic-bezier(.2,.8,.2,1) both}
  .slideup{animation:slideup .55s cubic-bezier(.2,.8,.2,1) both}
  .fade{animation:fade .6s ease both}
  .float{animation:float 2.6s ease-in-out infinite}

  /* Animasi khusus Detail Paket (geser ke atas sedikit + fade in) & Pilih Background (pop perlahan satu per satu + fade in) */
  @keyframes detailSheetRise{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
  @keyframes bgPopSlow{0%{opacity:0;transform:scale(.88) translateY(10px)}70%{opacity:1;transform:scale(1.02) translateY(0)}100%{opacity:1;transform:scale(1) translateY(0)}}
  .detail-sheet-enter{animation:detailSheetRise .55s cubic-bezier(.22,1,.36,1) both}
  .bg-pop-enter{animation:bgPopSlow .55s cubic-bezier(.22,1,.36,1) both}

  /* Fixed Bottom Action Bar */
  .bottom-bar-fixed {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 440px;
    background: #fff;
    border-radius: 24px 24px 0 0;
    padding: 14px 20px calc(14px + env(safe-area-inset-bottom, 0px));
    box-shadow: 0 -6px 20px rgba(23,89,202,.12);
    border-top: 1px solid #EEF2FA;
    z-index: 50;
    box-sizing: border-box;
  }

  .press{transition:transform .2s,box-shadow .2s}
  .press:hover{transform:translateY(-3px)}
  .press:active,button:active{transform:scale(.97)}

  /* Screen visibility */
  .screen{display:none;flex-direction:column;min-height:100vh;width:100%}
  .screen.active{display:flex}

  /* Hide scrollbar on horizontal date strip */
  .no-scrollbar::-webkit-scrollbar{display:none}
  .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}

  /* Toast notification */
  .toast-msg{
    position:fixed;
    top:18px;
    left:50%;
    transform:translateX(-50%) translateY(-20px);
    background:#111827;
    color:#fff;
    padding:11px 18px;
    border-radius:14px;
    font-size:13px;
    font-weight:600;
    z-index:999;
    box-shadow:0 10px 25px rgba(0,0,0,.22);
    opacity:0;
    pointer-events:none;
    transition:opacity .25s ease,transform .25s ease;
    max-width:380px;
    width:calc(100% - 32px);
    text-align:center;
  }
  .toast-msg.show{
    opacity:1;
    transform:translateX(-50%) translateY(0);
  }
  .toast-msg.error{background:#CE004F}
  .toast-msg.success{background:#15803D}

  /* Promo carousel slide */
  .promo-slide{display:none}
  .promo-slide.active{display:flex}
</style>
</head>
<body>

<div id="toast" class="toast-msg" role="status" aria-live="polite"></div>

<div class="app-shell">

  <!-- =====================================================================
       SCREEN 1: PILIH PAKET (Langkah 1 dari 4)
       ===================================================================== -->
  <section id="screen-1" class="screen active" style="padding:16px 20px 40px">
    <!-- HEADER + PROGRESS -->
    <div class="rise" style="animation-delay:.02s;margin-bottom:16px">
      <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#555;margin-bottom:10px">
        <a href="{{ url('/') }}" style="text-decoration:none;color:#1759CA;font-weight:800;font-size:15px;display:inline-flex;align-items:center;gap:6px" title="Kembali ke Beranda Snap Fun">
          <img src="{{ asset('img/logo/logo-snapfun-160.webp') }}" alt="Snap Fun" width="28" height="20" style="height:20px;width:auto;object-fit:contain" onerror="this.style.display='none'">
          <span>Snap Fun</span>
        </a>
        <span>Langkah <strong style="color:#111">1</strong> dari 4</span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
        <div style="height:8px;border-radius:99px;background:#1759CA"></div>
        <div style="height:8px;border-radius:99px;background:#C8D3EA"></div>
        <div style="height:8px;border-radius:99px;background:#C8D3EA"></div>
        <div style="height:8px;border-radius:99px;background:#C8D3EA"></div>
      </div>
    </div>

    <!-- HEADING -->
    <div class="rise" style="animation-delay:.06s">
      <h1 style="font-size:24px;font-weight:700;letter-spacing:-.02em;margin:0 0 4px">Mau pilih yang mana?</h1>
      <p style="font-size:14px;color:#444;margin:0 0 16px">Pilih paket sesuai yang kamu mau!</p>
    </div>

    <!-- PROMO BANNER (Otomatis sinkron dengan Promo di Website Utama / SitePromo) -->
    @php
      $activePromos = isset($promos) && count($promos) > 0 ? $promos : collect([
        (object)[
          'id' => 0,
          'badge' => 'PROMO',
          'title' => 'Klaim promo khusus buat kamu!',
          'description' => 'Masukin kode voucher pas konfirmasi ya~',
          'image' => 'img/Pinpin-01.png',
          'link_url' => null,
        ]
      ]);
    @endphp
    <div class="rise" style="animation-delay:.12s;margin-bottom:20px;position:relative">
      @foreach($activePromos as $pIdx => $promo)
        @php
          $promoImg = \App\Support\Site::url($promo->image ?? null) ?: asset('img/Pinpin-01.png');
          $promoBadge = trim((string)($promo->badge ?? '')) ?: 'PROMO';
          $promoTitle = trim((string)($promo->title ?? '')) ?: 'Klaim promo khusus buat kamu!';
          $promoDesc = trim((string)($promo->description ?? '')) ?: 'Masukin kode voucher pas konfirmasi ya~';
        @endphp
        <div class="promo-slide {{ $pIdx === 0 ? 'active' : '' }}" data-promo-index="{{ $pIdx }}" onclick="openPromoModal('{{ e($promoImg) }}', '{{ e($promoTitle) }}', '{{ e($promoDesc) }}', '{{ e($promoBadge) }}')" style="background:#1759CA;border-radius:18px;padding:12px;align-items:center;gap:12px;color:#fff;box-shadow:0 8px 20px rgba(23,89,202,.20);cursor:pointer">
          <div style="flex:1;min-width:0">
            <span style="display:inline-block;background:#FEDD03;color:#111;font-size:10px;font-weight:800;padding:3px 8px;border-radius:99px;margin-bottom:6px;letter-spacing:.04em">{{ strtoupper($promoBadge) }}</span>
            <div style="font-size:14px;font-weight:700;line-height:1.25">{{ $promoTitle }}</div>
            <div style="font-size:11px;color:#DCE8FF;margin-top:3px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $promoDesc }}</div>
          </div>
          <div style="width:76px;height:76px;border-radius:12px;background:#0F3F91 url('{{ $promoImg }}') center/cover no-repeat;flex-shrink:0;border:2px solid rgba(255,255,255,.18)"></div>
        </div>
      @endforeach

      @if(count($activePromos) > 1)
        <div style="display:flex;justify-content:center;gap:6px;margin-top:8px" id="promo-dots">
          @foreach($activePromos as $pIdx => $promo)
            <button type="button" onclick="showPromoSlide({{ $pIdx }})" aria-label="Slide promo {{ $pIdx + 1 }}" class="promo-dot" style="height:6px;width:{{ $pIdx === 0 ? '18px' : '6px' }};border-radius:99px;border:0;padding:0;background:{{ $pIdx === 0 ? '#1759CA' : '#C8D3EA' }};transition:all .25s ease;cursor:pointer"></button>
          @endforeach
        </div>
      @endif
    </div>

    <!-- PACKAGE CARDS -->
    <div id="package-list" style="display:flex;flex-direction:column;gap:14px">
      <!-- Di-render otomatis dari database -->
    </div>
  </section>

  <!-- =====================================================================
       SCREEN 1B: DETAIL PAKET
       ===================================================================== -->
  <section id="screen-1b" class="screen" style="background:#F3F6FD">
    <!-- TOP PHOTO + BACK BUTTON -->
    <div id="detail-pkg-hero" class="fade" style="position:relative;height:280px;overflow:hidden;background:#D7E0F2">
      <img id="detail-pkg-img" src="{{ asset('img/group(3).png') }}" alt="Foto Paket" style="width:100%;height:100%;object-fit:cover;display:block">
      <button type="button" onclick="goToScreen('screen-1')" aria-label="Kembali" style="position:absolute;top:16px;left:16px;width:44px;height:44px;border-radius:99px;border:0;background:rgba(255,255,255,.92);box-shadow:0 4px 12px rgba(0,0,0,.15);display:grid;place-items:center;cursor:pointer">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
      </button>
    </div>

    <!-- CONTENT SHEET -->
    <div id="detail-pkg-sheet" class="detail-sheet-enter" style="position:relative;margin-top:-28px;background:#fff;border-radius:28px 28px 0 0;padding:22px 20px 120px;flex:1;box-shadow:0 -4px 20px rgba(23,89,202,.06)">
      <h1 id="detail-pkg-title" style="font-size:24px;font-weight:700;letter-spacing:-.02em;margin:0 0 14px">-</h1>

      <div id="detail-pkg-attributes" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px">
        <!-- Diisi dinamis dari kolom paket DB -->
      </div>

      <div style="font-size:13px;font-weight:700;margin:0 0 6px">Deskripsi Paket</div>
      <ul id="detail-pkg-points" style="font-size:12px;line-height:1.65;color:#444;margin:0;padding-left:18px">
        <!-- Diisi langsung dari poin deskripsi paket di DB -->
      </ul>
    </div>

    <!-- FIXED BOTTOM BAR -->
    <div class="bottom-bar-fixed" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
      <div>
        <div style="font-size:11px;color:#666">Harga</div>
        <div id="detail-pkg-price" style="font-size:20px;font-weight:800;color:#CE004F">Rp0</div>
      </div>
      <button type="button" onclick="confirmPackageAndChooseBg()" style="background:#1759CA;color:#fff;border:0;border-radius:16px;padding:14px 20px;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;cursor:pointer;box-shadow:0 6px 16px rgba(23,89,202,.25)">
        Pilih Background
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </button>
    </div>
  </section>

  <!-- =====================================================================
       SCREEN 2: PILIH BACKGROUND (Langkah 2 dari 4)
       ===================================================================== -->
  <section id="screen-2" class="screen">
    <div style="padding:16px 20px 120px;flex:1">
      <!-- HEADER + PROGRESS -->
      <div style="margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <button type="button" onclick="goToScreen('screen-1b')" aria-label="Kembali ke Detail Paket" style="width:40px;height:40px;border-radius:99px;border:1.5px solid #D7E0F2;background:#fff;display:grid;place-items:center;cursor:pointer">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <span style="font-size:13px;color:#555">Langkah <strong style="color:#111">2</strong> dari 4</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#C8D3EA"></div>
          <div style="height:8px;border-radius:99px;background:#C8D3EA"></div>
        </div>
      </div>

      <h1 style="font-size:24px;font-weight:700;letter-spacing:-.02em;margin:0 0 6px">Pilih Background</h1>
      <p style="font-size:13px;color:#555;margin:0 0 14px">Pilih konsep background yang tersedia untuk paket ini.</p>

      <!-- 2-COLUMN GRID -->
      <div id="background-options" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <!-- Di-render dinamis dari /api/backgrounds -->
      </div>
    </div>

    <!-- FIXED BOTTOM BAR -->
    <div class="bottom-bar-fixed" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
      <div style="min-width:0">
        <div id="step2-pkg-name" style="font-size:12px;font-weight:700;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">-</div>
        <div id="step2-pkg-price" style="font-size:18px;font-weight:800;color:#CE004F">Rp0</div>
      </div>
      <button type="button" onclick="proceedToDateTime()" style="background:#1759CA;color:#fff;border:0;border-radius:16px;padding:14px 24px;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;cursor:pointer;box-shadow:0 6px 16px rgba(23,89,202,.25);flex-shrink:0">
        Lanjut
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </button>
    </div>
  </section>

  <!-- =====================================================================
       SCREEN 3: PILIH TANGGAL & WAKTU (Langkah 3 dari 4)
       ===================================================================== -->
  <section id="screen-3" class="screen">
    <div style="padding:16px 20px 120px;flex:1">
      <!-- HEADER + PROGRESS -->
      <div style="margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <button type="button" onclick="goToScreen('screen-2')" aria-label="Kembali ke Pilih Background" style="width:40px;height:40px;border-radius:99px;border:1.5px solid #D7E0F2;background:#fff;display:grid;place-items:center;cursor:pointer">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <span style="font-size:13px;color:#555">Langkah <strong style="color:#111">3</strong> dari 4</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#C8D3EA"></div>
        </div>
      </div>

      <div>
        <h1 style="font-size:24px;font-weight:700;letter-spacing:-.02em;margin:0 0 4px">Pilih Tanggal &amp; Waktu</h1>
        <p style="font-size:13px;color:#444;margin:0 0 18px">Pilih slot yang tersedia untuk sesi fotomu.</p>
      </div>

      <!-- MONTH HEADER + TOGGLE SEMUA TANGGAL + KALENDER -->
      <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:10px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:6px">
          <button type="button" id="prev-month-btn" onclick="changeMonth(-1)" aria-label="Bulan sebelumnya" style="width:30px;height:30px;border-radius:99px;border:1.5px solid #D7E0F2;background:#fff;display:grid;place-items:center;cursor:pointer;color:#111">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <span id="month-year-label" style="font-size:13px;font-weight:800;letter-spacing:.04em;color:#111">-</span>
          <button type="button" id="next-month-btn" onclick="changeMonth(1)" aria-label="Bulan berikutnya" style="width:30px;height:30px;border-radius:99px;border:1.5px solid #D7E0F2;background:#fff;display:grid;place-items:center;cursor:pointer;color:#111">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
          </button>
        </div>

        <div style="display:flex;align-items:center;gap:6px">
          <button type="button" id="toggle-all-dates-btn" onclick="toggleAllDatesView()" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:#1759CA;background:#fff;border:1.5px solid #B9CCF0;border-radius:99px;padding:5px 10px;cursor:pointer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span id="toggle-all-dates-text">Semua Tanggal</span>
          </button>

          <label style="position:relative;display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:#1759CA;background:#fff;border:1.5px solid #B9CCF0;border-radius:99px;padding:5px 10px;cursor:pointer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <span>Kalender</span>
            <input type="date" id="booking-date" onchange="onNativeDateInputChange(this.value)" style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%">
          </label>
        </div>
      </div>

      <!-- DATE PILLS WITH LEFT/RIGHT SLIDE BUTTONS -->
      <div style="position:relative;display:flex;align-items:center;gap:6px;margin-bottom:14px">
        <button type="button" id="date-scroll-left" onclick="scrollDateStrip(-1)" aria-label="Geser tanggal ke kiri" style="width:32px;height:48px;border-radius:12px;border:1.5px solid #D7E0F2;background:#fff;color:#1759CA;display:grid;place-items:center;cursor:pointer;flex-shrink:0;box-shadow:0 2px 8px rgba(23,89,202,.08)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>

        <div id="date-pills-container" class="no-scrollbar" style="flex:1;display:flex;gap:8px;overflow-x:auto;padding:4px 2px;scroll-behavior:smooth">
          <!-- Diisi dinamis per bulan -->
        </div>

        <button type="button" id="date-scroll-right" onclick="scrollDateStrip(1)" aria-label="Geser tanggal ke kanan" style="width:32px;height:48px;border-radius:12px;border:1.5px solid #D7E0F2;background:#fff;color:#1759CA;display:grid;place-items:center;cursor:pointer;flex-shrink:0;box-shadow:0 2px 8px rgba(23,89,202,.08)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>

      <!-- TIME SLOTS -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div style="font-size:14px;font-weight:700">Jam tersedia</div>
        <div style="display:flex;align-items:center;gap:10px;font-size:11px;color:#555">
          <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:3px;background:#fff;border:1.5px solid #D7E0F2;display:inline-block"></span>Tersedia</span>
          <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:3px;background:#1759CA;display:inline-block"></span>Dipilih</span>
          <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:3px;background:#E3E8F2;display:inline-block"></span>Penuh</span>
        </div>
      </div>

      <div id="time-slots" style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
        <!-- Di-render dinamis dari /api/time-slots, /booked-times, /blocked-dates -->
      </div>
    </div>

    <!-- FIXED BOTTOM BAR -->
    <div class="bottom-bar-fixed" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
      <div style="min-width:0">
        <div id="step3-schedule-summary" style="font-size:12px;font-weight:700;color:#111">Pilih tanggal &amp; jam</div>
        <div id="step3-pkg-price" style="font-size:18px;font-weight:800;color:#CE004F">Rp0</div>
      </div>
      <button type="button" onclick="proceedToConfirmation()" style="background:#1759CA;color:#fff;border:0;border-radius:16px;padding:14px 24px;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;cursor:pointer;box-shadow:0 6px 16px rgba(23,89,202,.25);flex-shrink:0">
        Lanjut
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </button>
    </div>
  </section>

  <!-- =====================================================================
       SCREEN 4: KONFIRMASI BOOKING KAMU (Langkah 4 dari 4)
       ===================================================================== -->
  <section id="screen-4" class="screen">
    <div style="padding:16px 20px 250px;flex:1">
      <!-- HEADER + PROGRESS -->
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <button type="button" onclick="goToScreen('screen-3')" aria-label="Kembali ke Tanggal dan Waktu" style="width:40px;height:40px;border-radius:99px;border:1.5px solid #D7E0F2;background:#fff;display:grid;place-items:center;cursor:pointer">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <span style="font-size:13px;color:#555">Langkah <strong style="color:#111">4</strong> dari 4</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
          <div style="height:8px;border-radius:99px;background:#1759CA"></div>
        </div>
      </div>

      <div>
        <h1 style="font-size:22px;font-weight:700;letter-spacing:-.02em;margin:0 0 4px">Konfirmasi Booking Kamu</h1>
        <p style="font-size:12px;color:#444;margin:0 0 14px;line-height:1.45">Biar sesi foto kamu nggak diambil orang, isi dulu data di bawah ini ya!</p>
      </div>

      <!-- RINGKASAN PILIHAN -->
      <div style="background:#fff;border:1.5px solid #D7E0F2;border-radius:16px;padding:10px 12px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:10px">
        <div style="min-width:0">
          <div id="confirm-summary-title" style="font-size:13px;font-weight:700;color:#1759CA;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">-</div>
          <div id="confirm-summary-sub" style="font-size:11px;color:#555;margin-top:2px">-</div>
        </div>
        <button type="button" onclick="goToScreen('screen-3')" style="background:#F3F6FD;color:#1759CA;border:0;border-radius:10px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;flex-shrink:0">Ubah</button>
      </div>

      <!-- FORM FIELDS -->
      <div style="display:flex;flex-direction:column;gap:11px">
        <!-- Nama -->
        <div>
          <label for="nama" style="font-size:12px;font-weight:700;display:block;margin-bottom:5px">Nama <span style="color:#CE004F">*</span></label>
          <div style="display:flex;align-items:center;gap:10px;background:#fff;border:2px solid #B9CCF0;border-radius:14px;padding:10px 12px">
            <span style="width:30px;height:30px;border-radius:99px;background:#1759CA;display:grid;place-items:center;flex-shrink:0">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><circle cx="12" cy="8" r="4"/><path d="M4 20c2-4 14-4 16 0"/></svg>
            </span>
            <input id="nama" type="text" placeholder="Masukkan nama lengkap" autocomplete="name" style="border:0;outline:0;width:100%;font-size:13px;color:#111;background:transparent">
          </div>
        </div>

        <!-- Nomor WhatsApp -->
        <div>
          <label for="whatsapp" style="font-size:12px;font-weight:700;display:block;margin-bottom:5px">Nomor WhatsApp <span style="color:#CE004F">*</span></label>
          <div style="display:flex;align-items:center;gap:10px;background:#fff;border:2px solid #B9CCF0;border-radius:14px;padding:10px 12px">
            <span style="width:30px;height:30px;border-radius:99px;background:#1759CA;display:grid;place-items:center;flex-shrink:0">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 3.1 5.2 2 2 0 0 1 5.1 3h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L9 11a16 16 0 0 0 4 4l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7A2 2 0 0 1 22 16.9z"/></svg>
            </span>
            <input id="whatsapp" type="tel" placeholder="Contoh: 081234567890" autocomplete="tel" style="border:0;outline:0;width:100%;font-size:13px;color:#111;background:transparent">
          </div>
        </div>

        <!-- Email -->
        <div>
          <label for="email" style="font-size:12px;font-weight:700;display:block;margin-bottom:5px">Email <span style="color:#CE004F">*</span></label>
          <div style="display:flex;align-items:center;gap:10px;background:#fff;border:2px solid #B9CCF0;border-radius:14px;padding:10px 12px">
            <span style="width:30px;height:30px;border-radius:99px;background:#1759CA;display:grid;place-items:center;flex-shrink:0">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            </span>
            <input id="email" type="email" placeholder="Contoh: kamu@email.com" autocomplete="email" style="border:0;outline:0;width:100%;font-size:13px;color:#111;background:transparent">
          </div>
        </div>

        <!-- Jumlah Orang (Otomatis sinkron dengan extras_people & kuota paket di DB) -->
        <div>
          <label for="jumlah-orang" style="font-size:12px;font-weight:700;display:block;margin-bottom:5px">
            Jumlah Orang <span id="extra-person-note" style="font-weight:500;color:#666;font-size:11px">(Kalau lebih dari 1 orang, tambah Rp15.000 per orang ya)*</span>
          </label>
          <div style="display:flex;align-items:center;gap:10px;background:#fff;border:2px solid #B9CCF0;border-radius:14px;padding:10px 12px">
            <span style="width:30px;height:30px;border-radius:99px;background:#1759CA;display:grid;place-items:center;flex-shrink:0">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><circle cx="9" cy="8" r="3.5"/><path d="M2 20c1.5-3.5 12.5-3.5 14 0"/><circle cx="17" cy="9" r="2.5"/><path d="M15 20c1-2.5 6-2.5 7 0"/></svg>
            </span>
            <select id="jumlah-orang" onchange="recalculateTotal()" style="border:0;outline:0;width:100%;font-size:13px;font-weight:600;color:#111;background:transparent;cursor:pointer">
              @for($i = 1; $i <= 8; $i++)
                <option value="{{ $i }}">{{ $i }} Orang</option>
              @endfor
            </select>
          </div>
        </div>

        <!-- Kode Voucher -->
        <div>
          <label for="voucher-code" style="font-size:12px;font-weight:700;display:block;margin-bottom:5px">Kode Voucher</label>
          <div style="display:flex;gap:8px">
            <div style="flex:1;display:flex;align-items:center;gap:10px;background:#fff;border:2px solid #B9CCF0;border-radius:14px;padding:10px 12px">
              <span style="width:30px;height:30px;border-radius:99px;background:#1759CA;display:grid;place-items:center;flex-shrink:0">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v1a2 2 0 0 0 0 4v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1a2 2 0 0 0 0-4V9z"/></svg>
              </span>
              <input id="voucher-code" type="text" placeholder="Masukkan kode (opsional)" style="border:0;outline:0;width:100%;font-size:13px;color:#111;background:transparent">
            </div>
            <button type="button" onclick="checkVoucher()" style="background:#1759CA;color:#fff;border:0;border-radius:14px;padding:0 18px;font-size:13px;font-weight:700;cursor:pointer">Cek</button>
          </div>
          <div id="voucher-message" style="font-size:11px;font-weight:600;margin-top:5px;display:none"></div>
        </div>

        <!-- DP QRIS CARD (Otomatis sinkron dengan downpayment di DB) -->
        <div style="background:#fff;border-radius:16px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:10px;border:1.5px solid #D7E0F2">
          <div>
            <div style="font-size:13px;font-weight:700;color:#111">Down Payment (DP)</div>
            <div id="dp-card-desc" style="font-size:11px;color:#666;margin-top:2px">Pembayaran DP via QRIS.</div>
          </div>
          <button type="button" onclick="showQRISModal(event)" style="background:#FEDD03;color:#111;border:0;border-radius:12px;padding:10px 14px;font-size:12px;font-weight:800;cursor:pointer;white-space:nowrap">Klik disini</button>
        </div>

        <!-- CHAT ADMIN -->
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:4px 2px">
          <span style="font-size:11px;color:#444;line-height:1.4">Kalau ada kendala, tenang~ tinggal chat admin kami aja.</span>
          <button type="button" onclick="chatAdmin()" style="border:2px solid #1759CA;background:#fff;color:#1759CA;border-radius:12px;padding:9px 14px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap">Chat Kami</button>
        </div>

        <!-- AGREEMENT CHECKBOX (Otomatis sinkron dengan downpayment di DB) -->
        <label style="display:flex;align-items:flex-start;gap:10px;font-size:12px;color:#222;line-height:1.45;cursor:pointer;padding-top:2px">
          <input id="agreement" type="checkbox" style="width:18px;height:18px;margin-top:1px;accent-color:#1759CA;flex-shrink:0;cursor:pointer">
          <span>Saya setuju, kalau batalin di hari-H, DP sebesar <strong id="agreement-dp-text">Rp30.000</strong> hangus <span style="color:#CE004F">*</span></span>
        </label>

        <!-- CONTENT CONSENT CHECKBOX (Konfirmasi izin penggunaan foto untuk konten) -->
        <label style="display:flex;align-items:flex-start;gap:10px;font-size:12px;color:#222;line-height:1.45;cursor:pointer;padding-top:2px">
          <input id="content-consent" type="checkbox" style="width:18px;height:18px;margin-top:1px;accent-color:#1759CA;flex-shrink:0;cursor:pointer">
          <span>Saya mengizinkan hasil foto digunakan untuk kebutuhan konten/sosial media <strong>Snap Fun Studio</strong></span>
        </label>
      </div>
    </div>

    <!-- FIXED BOTTOM PRICE BREAKDOWN & SUBMIT -->
    <div class="bottom-bar-fixed">
      <div style="display:flex;justify-content:space-between;font-size:12px;color:#555;margin-bottom:4px">
        <span id="breakdown-base-label">Paket (1 orang)</span>
        <span id="breakdown-base-price">Rp0</span>
      </div>
      <div id="breakdown-extra-row" style="display:flex;justify-content:space-between;font-size:12px;color:#555;margin-bottom:4px">
        <span id="breakdown-extra-label">Tambahan orang</span>
        <span id="breakdown-extra-price">Rp0</span>
      </div>
      <div id="breakdown-voucher-row" style="display:none;justify-content:space-between;font-size:12px;color:#15803D;font-weight:600;margin-bottom:6px">
        <span id="breakdown-voucher-label">Diskon Voucher</span>
        <span id="breakdown-voucher-price">-Rp0</span>
      </div>
      <div style="border-top:1px dashed #D7E0F2;margin:8px 0;padding-top:8px;display:flex;justify-content:space-between;align-items:baseline">
        <div>
          <span style="font-size:13px;font-weight:700;color:#111;display:block">Total sementara</span>
          <span id="breakdown-dp-note" style="font-size:11px;color:#1759CA;font-weight:600">DP dibayar sekarang: Rp0</span>
        </div>
        <span id="total-price" style="font-size:20px;font-weight:800;color:#CE004F">Rp0</span>
      </div>
      <button type="button" id="submit-booking-btn" onclick="submitBooking()" style="width:100%;background:#1759CA;color:#fff;border:0;border-radius:16px;padding:15px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 6px 16px rgba(23,89,202,.25)">
        Booking Sekarang
      </button>
    </div>
  </section>

  <!-- =====================================================================
       SCREEN 5: BERHASIL (Booking kamu udah masuk!)
       ===================================================================== -->
  <section id="screen-5" class="screen" style="padding:24px;justify-content:center;align-items:center;text-align:center">
    <img class="float" src="{{ asset('img/Pinpin-01.png') }}" alt="Pinpin Mascot" style="width:200px;height:200px;object-fit:contain;margin:0 auto 12px">

    <h1 class="rise" style="animation-delay:.1s;font-size:24px;font-weight:800;color:#1759CA;letter-spacing:-.02em;margin:0 0 8px">Booking kamu udah masuk!</h1>
    <p class="rise" style="animation-delay:.2s;font-size:13px;line-height:1.55;color:#444;max-width:310px;margin:0 auto 18px">
      Makasih udah booking di Snap Fun Studio. Jangan lupa konfirmasi lewat WhatsApp ya, dan kirim bukti pembayarannya!
    </p>

    <!-- RINGKASAN BOOKING & DP -->
    <div class="rise" style="animation-delay:.25s;width:100%;background:#fff;border:1.5px solid #D7E0F2;border-radius:18px;padding:14px 16px;margin-bottom:20px;text-align:left;font-size:12px;color:#333;box-shadow:0 6px 18px rgba(23,89,202,.06)">
      <div style="display:flex;justify-content:space-between;margin-bottom:6px">
        <span style="color:#666">Paket</span>
        <strong id="success-pkg" style="color:#111">-</strong>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px">
        <span style="color:#666">Background</span>
        <strong id="success-bg" style="color:#111">-</strong>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px">
        <span style="color:#666">Jadwal</span>
        <strong id="success-schedule" style="color:#111">-</strong>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px">
        <span style="color:#666">Total Harga</span>
        <strong id="success-total" style="color:#CE004F">-</strong>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px">
        <span style="color:#666">Izin Konten</span>
        <strong id="success-content-consent" style="color:#111">-</strong>
      </div>
      <div style="display:flex;justify-content:space-between;padding-top:6px;border-top:1px dashed #D7E0F2">
        <span style="color:#666">Nominal DP</span>
        <strong id="success-dp" style="color:#1759CA">-</strong>
      </div>
    </div>

    <button type="button" onclick="confirmBookingViaWA()" class="rise" style="animation-delay:.3s;width:100%;background:#1759CA;color:#fff;border:0;border-radius:16px;padding:15px 20px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 8px 20px rgba(23,89,202,.28);display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:10px">
      Konfirmasi via WhatsApp
    </button>

    <button type="button" onclick="showQRISModal(event)" class="rise" style="animation-delay:.35s;width:100%;background:#fff;color:#1759CA;border:2px solid #D7E0F2;border-radius:16px;padding:12px 20px;font-size:13px;font-weight:700;cursor:pointer">
      Lihat QRIS Pembayaran DP
    </button>
  </section>

  <!-- =====================================================================
       MODAL 4B: QRIS DP
       ===================================================================== -->
  <div id="qris-modal" class="fade" onclick="if(event.target===this)closeQRISModal()" style="position:fixed;inset:0;background:rgba(16,32,63,.55);display:none;place-items:center;padding:24px;z-index:100">
    <div class="pop" style="background:#fff;border-radius:22px;padding:20px;width:100%;max-width:340px;text-align:center;position:relative;box-shadow:0 20px 40px rgba(0,0,0,.25)">
      <button type="button" onclick="closeQRISModal()" aria-label="Tutup" style="position:absolute;top:12px;right:12px;width:32px;height:32px;border-radius:99px;border:0;background:#F3F6FD;cursor:pointer;font-size:18px;line-height:1;color:#333">×</button>
      <div style="font-size:15px;font-weight:700;margin:4px 0 4px">Scan QRIS untuk pembayaran DP</div>
      <div id="qris-dp-amount" style="font-size:12px;font-weight:700;color:#1759CA;margin-bottom:12px">Nominal DP: Rp30.000</div>

      <div style="width:230px;height:230px;margin:0 auto 14px;border-radius:14px;border:2px solid #D7E0F2;padding:12px;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden">
        <img id="qris-image" src="{{ asset('img/qrcode.png') }}" alt="QRIS Snap Fun Studio" style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;display:block">
      </div>

      <button type="button" onclick="downloadQRIS()" style="width:100%;background:#1759CA;color:#fff;border:0;border-radius:14px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.3" stroke-linecap="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
        Unduh QRIS
      </button>
    </div>
  </div>

  <!-- =====================================================================
       MODAL DETAIL PROMO (Terhubung dengan Promo Beranda Website Utama)
       ===================================================================== -->
  <div id="promo-modal" class="fade" onclick="if(event.target===this)closePromoModal()" style="position:fixed;inset:0;background:rgba(16,32,63,.55);display:none;place-items:center;padding:24px;z-index:100">
    <div class="pop" style="background:#fff;border-radius:22px;padding:18px;width:100%;max-width:350px;position:relative;box-shadow:0 20px 40px rgba(0,0,0,.25)">
      <button type="button" onclick="closePromoModal()" aria-label="Tutup" style="position:absolute;top:12px;right:12px;width:32px;height:32px;border-radius:99px;border:0;background:rgba(255,255,255,.9);cursor:pointer;font-size:18px;line-height:1;color:#111;z-index:2;box-shadow:0 2px 8px rgba(0,0,0,.15)">×</button>
      <div style="border-radius:14px;overflow:hidden;margin-bottom:12px;background:#0F3F91;max-height:240px">
        <img id="promo-modal-img" src="" alt="Promo Snap Fun" style="width:100%;height:100%;max-height:240px;object-fit:cover;display:block">
      </div>
      <span id="promo-modal-badge" style="display:inline-block;background:#FEDD03;color:#111;font-size:10px;font-weight:800;padding:3px 9px;border-radius:99px;margin-bottom:6px;letter-spacing:.04em">PROMO</span>
      <div id="promo-modal-title" style="font-size:16px;font-weight:800;color:#111;margin-bottom:6px">-</div>
      <p id="promo-modal-desc" style="font-size:12px;line-height:1.55;color:#444;margin:0 0 14px">-</p>
      <button type="button" onclick="closePromoModal()" style="width:100%;background:#1759CA;color:#fff;border:0;border-radius:14px;padding:12px;font-size:13px;font-weight:700;cursor:pointer">Pilih Paket Sekarang</button>
    </div>
  </div>

</div>

<script>
  // =========================================================================
  // DATA INITIALIZATION (Sinkron langsung dengan Database Admin)
  // =========================================================================
  const INITIAL_PACKAGES = @json($initialPackages ?? []);
  const STUDIO_WA_NUMBER = @json($waNumber ?? '6285117607254');

  let packages = Array.isArray(INITIAL_PACKAGES) ? INITIAL_PACKAGES : [];
  let selectedPackage = null;
  let selectedBackground = null;
  let selectedBackgroundName = '';
  let selectedDate = '';
  let selectedTime = '';

  let basePrice = 0;
  let currentVoucher = null;
  let closedDays = [];
  let operationalHoursMap = {};
  let blockedCache = null;

  const MONTHS_ID = ['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'];
  const MONTHS_SHORT_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const DAYS_SHORT_ID = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

  const todayDate = new Date();
  todayDate.setHours(0, 0, 0, 0);
  let activeYear = todayDate.getFullYear();
  let activeMonth = todayDate.getMonth(); // 0..11

  function formatRupiah(num) {
    return 'Rp' + Number(num || 0).toLocaleString('id-ID');
  }

  function showToast(msg, type = 'error') {
    const el = document.getElementById('toast');
    if (!el) return;
    el.textContent = msg;
    el.className = 'toast-msg show ' + type;
    clearTimeout(window.__toastTimer);
    window.__toastTimer = setTimeout(() => {
      el.classList.remove('show');
    }, 3200);
  }

  // =========================================================================
  // NAVIGATION (Tanpa efek slider saat berpindah halaman)
  // =========================================================================
  function goToScreen(screenId) {
    document.querySelectorAll('.screen').forEach(sec => {
      sec.classList.remove('active');
    });
    const target = document.getElementById(screenId);
    if (!target) return;
    target.classList.add('active');
    window.scrollTo(0, 0);
  }

  // =========================================================================
  // PROMO BANNER AUTO-SLIDE (Terhubung dengan SitePromo Website Utama)
  // =========================================================================
  let currentPromoIdx = 0;
  function showPromoSlide(idx) {
    const slides = document.querySelectorAll('.promo-slide');
    const dots = document.querySelectorAll('.promo-dot');
    if (!slides.length) return;
    currentPromoIdx = (idx + slides.length) % slides.length;
    slides.forEach((s, i) => s.classList.toggle('active', i === currentPromoIdx));
    dots.forEach((d, i) => {
      d.style.width = i === currentPromoIdx ? '18px' : '6px';
      d.style.background = i === currentPromoIdx ? '#1759CA' : '#C8D3EA';
    });
  }

  function openPromoModal(img, title, desc, badge) {
    document.getElementById('promo-modal-img').src = img;
    document.getElementById('promo-modal-title').textContent = title;
    document.getElementById('promo-modal-desc').textContent = desc;
    document.getElementById('promo-modal-badge').textContent = (badge || 'PROMO').toUpperCase();
    document.getElementById('promo-modal').style.display = 'grid';
  }

  function closePromoModal() {
    document.getElementById('promo-modal').style.display = 'none';
  }

  // =========================================================================
  // SCREEN 1: RENDER PACKAGES & OPEN PACKAGE DETAIL (SCREEN 1B)
  // =========================================================================
  function renderPackageList() {
    const container = document.getElementById('package-list');
    if (!container) return;
    container.innerHTML = '';

    if (!packages.length) {
      container.innerHTML = '<div style="background:#fff;border-radius:18px;padding:20px;text-align:center;color:#666;font-size:13px">Belum ada paket yang tersedia.</div>';
      return;
    }

    packages.forEach((pkg, idx) => {
      const delay = (0.18 + idx * 0.08).toFixed(2);
      const lines = (pkg.description || '').split(/\r?\n/).map(s => s.trim()).filter(Boolean);
      const shortDesc = lines[0] || `Sesi foto ${pkg.duration_minutes || 15} menit • Include ${pkg.included_people || 1} orang`;
      const imgUrl = pkg.image_url || '{{ asset("img/group(3).png") }}';

      const card = document.createElement('div');
      card.className = 'rise press';
      card.style.cssText = `animation-delay:${delay}s;background:#fff;border-radius:22px;padding:10px;box-shadow:0 6px 18px rgba(23,89,202,.08);cursor:pointer`;
      card.onclick = () => openPackageDetail(pkg.id);

      card.innerHTML = `
        <div style="height:112px;border-radius:16px;background:#D7E0F2 url('${imgUrl}') center/cover no-repeat;margin-bottom:10px"></div>
        <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:8px;padding:2px 6px 4px">
          <div style="min-width:0">
            <div style="font-size:18px;font-weight:700;color:#111;line-height:1.15">${escapeHtml(pkg.title)}</div>
            <div style="font-size:11px;color:#666;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeHtml(shortDesc)}</div>
          </div>
          <div style="text-align:right;flex-shrink:0">
            <div style="font-size:17px;font-weight:700;color:#CE004F">${formatRupiah(pkg.price)}</div>
            <div style="font-size:10px;color:#777">/sesi</div>
          </div>
        </div>
      `;
      container.appendChild(card);
    });
  }

  function escapeHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function openPackageDetail(pkgId) {
    const pkg = packages.find(p => Number(p.id) === Number(pkgId));
    if (!pkg) return;

    selectedPackage = pkg;
    basePrice = Number(pkg.price) || 0;

    // Populate Screen 1B (Detail Paket)
    document.getElementById('detail-pkg-img').src = pkg.image_url || '{{ asset("img/group(3).png") }}';
    document.getElementById('detail-pkg-title').textContent = pkg.title;
    document.getElementById('detail-pkg-price').textContent = formatRupiah(pkg.price);

    const includedPeople = Number(pkg.included_people) || (Number(pkg.id) === 2 ? 2 : 1);
    const maxPeople = Number(pkg.max_people) || 8;
    const durationMin = Number(pkg.duration_minutes) || 15;
    const extraFee = Number(pkg.extras_people) || 0;
    const extraDurationFee = Number(pkg.extras_duration) || 0;
    const dpAmount = Number(pkg.downpayment) || 0;
    const bgCount = Number(pkg.backgrounds_count) || (Array.isArray(pkg.backgrounds) ? pkg.backgrounds.length : 0);

    // 6 Atribut Paket yang tersinkron langsung dengan kolom Database Admin:
    // 1. Untuk berapa orang
    // 2. Durasi
    // 3. Jika tambah orang tambah berapa
    // 4. Jika tambah durasi per 3 menit tambah berapa
    // 5. Harga DP
    // 6. Berapa pilihan background
    const attrs = [
      {
        icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><circle cx="9" cy="8" r="3.5"/><path d="M2 20c1.5-4 12.5-4 14 0"/></svg>',
        label: maxPeople > includedPeople ? `Untuk ${includedPeople} orang (Max ${maxPeople})` : `Untuk ${includedPeople} orang`
      },
      {
        icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
        label: `Durasi ${durationMin} menit`
      }
    ];

    if (extraFee > 0) {
      attrs.push({
        icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>',
        label: `Tambah orang +${formatRupiah(extraFee)}/orang`
      });
    }

    if (extraDurationFee > 0) {
      attrs.push({
        icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 1.5M9 2h6"/></svg>',
        label: `Tambah durasi +${formatRupiah(extraDurationFee)}/3 menit`
      });
    }

    attrs.push({
      icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/></svg>',
      label: `DP ${formatRupiah(dpAmount)}`
    });

    attrs.push({
      icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1759CA" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
      label: `${bgCount} pilihan background`
    });

    const attrContainer = document.getElementById('detail-pkg-attributes');
    attrContainer.innerHTML = attrs.map((a, i) => `
      <span class="pop" style="animation-delay:${(0.12 + i * 0.05).toFixed(2)}s;display:inline-flex;align-items:center;gap:6px;background:#F3F6FD;border-radius:99px;padding:6px 12px;font-size:11px;font-weight:600;color:#222">
        ${a.icon}
        ${escapeHtml(a.label)}
      </span>
    `).join('');

    // Deskripsi Paket langsung dalam bentuk poin (bullet list) dari kolom Deskripsi di Admin DB
    const rawLines = (pkg.description || '')
      .split(/\r?\n/)
      .map(s => s.replace(/^[\-\•\*]\s*/, '').trim())
      .filter(Boolean);

    const points = rawLines.length > 0
      ? rawLines
      : [
          `Berlaku untuk ${includedPeople} orang dengan durasi sesi ${durationMin} menit.`,
          extraFee > 0 ? `Bisa tambah orang (${formatRupiah(extraFee)}/orang, maksimal ${maxPeople} orang).` : `Kapasitas maksimal ${maxPeople} orang.`,
          `Tersedia ${bgCount} pilihan background.`,
          `Pembayaran DP sebesar ${formatRupiah(dpAmount)} via QRIS.`
        ];

    const pointsEl = document.getElementById('detail-pkg-points');
    pointsEl.innerHTML = points.map(p => `<li>${escapeHtml(p)}</li>`).join('');

    goToScreen('screen-1b');

    // Re-trigger animasi masuk pada foto & section bawah Detail Paket (geser ke atas sedikit + fade in)
    const heroEl = document.getElementById('detail-pkg-hero');
    const sheetEl = document.getElementById('detail-pkg-sheet');
    [heroEl, sheetEl].forEach(el => {
      if (!el) return;
      el.style.animation = 'none';
      void el.offsetWidth;
      el.style.animation = '';
    });
  }

  // =========================================================================
  // SCREEN 2: PILIH BACKGROUND
  // =========================================================================
  function confirmPackageAndChooseBg() {
    if (!selectedPackage) {
      showToast('Silakan pilih paket terlebih dahulu.');
      return;
    }

    document.getElementById('step2-pkg-name').textContent = selectedPackage.title;
    document.getElementById('step2-pkg-price').textContent = formatRupiah(selectedPackage.price);
    document.getElementById('step3-pkg-price').textContent = formatRupiah(selectedPackage.price);

    selectedBackground = null;
    selectedBackgroundName = '';

    goToScreen('screen-2');
    loadBackgroundsForPackage(selectedPackage.id);
  }

  let currentBgItems = [];
  let showAllDatesGrid = false;

  async function loadBackgroundsForPackage(packageId) {
    const container = document.getElementById('background-options');
    container.innerHTML = '<div style="grid-column:span 2;text-align:center;padding:24px;font-size:13px;color:#555">Memuat pilihan background...</div>';

    let bgItems = [];
    try {
      const res = await fetch(`/api/backgrounds?package_id=${encodeURIComponent(packageId)}`);
      const json = await res.json();
      if (json.success && Array.isArray(json.data) && json.data.length > 0) {
        bgItems = json.data;
      }
    } catch (e) {
      console.warn('Fallback ke data background lokal:', e);
    }

    if (!bgItems.length && selectedPackage && Array.isArray(selectedPackage.backgrounds)) {
      bgItems = selectedPackage.backgrounds;
    }

    if (!bgItems.length) {
      container.innerHTML = '<div style="grid-column:span 2;background:#fff;border-radius:16px;padding:20px;text-align:center;color:#666;font-size:13px">Belum ada background yang terhubung pada paket ini.</div>';
      return;
    }

    // Pastikan setiap item background memiliki ID unik agar tidak terpilih semua
    const seenIds = new Set();
    currentBgItems = bgItems.map((bg, idx) => {
      let uid = bg.id != null ? Number(bg.id) : (idx + 1);
      if (seenIds.has(uid)) {
        uid = idx + 1000;
      }
      seenIds.add(uid);
      return {
        ...bg,
        _uid: uid,
        id: bg.id != null ? bg.id : uid,
      };
    });

    // Hanya pilih 1 background pertama secara default
    selectedBackground = currentBgItems[0].id;
    selectedBackgroundKey = currentBgItems[0]._uid;
    selectedBackgroundName = currentBgItems[0].name;

    renderBackgroundCards(currentBgItems);
  }

  let selectedBackgroundKey = null;

  function renderBackgroundCards(bgItems) {
    const container = document.getElementById('background-options');
    container.innerHTML = '';

    bgItems.forEach((bg, idx) => {
      const isSelected = selectedBackgroundKey === bg._uid;
      const delay = (0.06 + idx * 0.10).toFixed(2);
      const imgUrl = bg.image_url || '{{ asset("img/group(3).png") }}';

      const card = document.createElement('div');
      card.className = 'bg-pop-enter press bg-option-card';
      card.dataset.bgUid = String(bg._uid);
      card.style.cssText = isSelected
        ? `animation-delay:${delay}s;background:#fff;border-radius:18px;padding:6px;border:3px solid #1759CA;box-shadow:0 6px 16px rgba(23,89,202,.18);cursor:pointer`
        : `animation-delay:${delay}s;background:#fff;border-radius:18px;padding:6px;border:3px solid transparent;box-shadow:0 4px 12px rgba(23,89,202,.08);cursor:pointer`;

      card.onclick = () => {
        selectedBackground = bg.id;
        selectedBackgroundKey = bg._uid;
        selectedBackgroundName = bg.name;
        updateBackgroundSelectionUI();
      };

      card.innerHTML = `
        <div style="position:relative;height:104px;border-radius:12px;background:#D7E0F2 url('${imgUrl}') center/cover no-repeat">
          <span class="bg-check-badge" style="position:absolute;top:8px;right:8px;width:24px;height:24px;border-radius:99px;background:#1759CA;display:${isSelected ? 'grid' : 'none'};place-items:center;box-shadow:0 2px 6px rgba(0,0,0,.2)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
          </span>
        </div>
        <div style="font-size:13px;font-weight:700;padding:8px 4px 4px;color:#111">${escapeHtml(bg.name)}</div>
      `;
      container.appendChild(card);
    });
  }

  function updateBackgroundSelectionUI() {
    document.querySelectorAll('#background-options .bg-option-card').forEach(card => {
      const isSelected = Number(card.dataset.bgUid) === Number(selectedBackgroundKey);
      card.style.border = isSelected ? '3px solid #1759CA' : '3px solid transparent';
      card.style.boxShadow = isSelected
        ? '0 6px 16px rgba(23,89,202,.18)'
        : '0 4px 12px rgba(23,89,202,.08)';
      const badge = card.querySelector('.bg-check-badge');
      if (badge) {
        badge.style.display = isSelected ? 'grid' : 'none';
      }
    });
  }

  function proceedToDateTime() {
    if (!selectedBackground) {
      showToast('Silakan pilih background terlebih dahulu.');
      return;
    }

    goToScreen('screen-3');
    initDateTimeScreen();
  }

  // =========================================================================
  // SCREEN 3: PILIH TANGGAL & WAKTU
  // =========================================================================
  function formatYMD(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
  }

  function scrollDateStrip(direction) {
    const container = document.getElementById('date-pills-container');
    if (!container) return;
    container.scrollBy({ left: direction * 210, behavior: 'smooth' });
  }

  function toggleAllDatesView() {
    showAllDatesGrid = !showAllDatesGrid;
    const container = document.getElementById('date-pills-container');
    const leftBtn = document.getElementById('date-scroll-left');
    const rightBtn = document.getElementById('date-scroll-right');
    const toggleText = document.getElementById('toggle-all-dates-text');
    const toggleBtn = document.getElementById('toggle-all-dates-btn');

    if (showAllDatesGrid) {
      container.style.display = 'grid';
      container.style.gridTemplateColumns = 'repeat(5, 1fr)';
      container.style.overflowX = 'visible';
      if (leftBtn) leftBtn.style.display = 'none';
      if (rightBtn) rightBtn.style.display = 'none';
      if (toggleText) toggleText.textContent = 'Mode Geser';
      if (toggleBtn) {
        toggleBtn.style.background = '#1759CA';
        toggleBtn.style.color = '#fff';
        toggleBtn.querySelector('svg')?.setAttribute('stroke', '#fff');
      }
    } else {
      container.style.display = 'flex';
      container.style.gridTemplateColumns = '';
      container.style.overflowX = 'auto';
      if (leftBtn) leftBtn.style.display = 'grid';
      if (rightBtn) rightBtn.style.display = 'grid';
      if (toggleText) toggleText.textContent = 'Semua Tanggal';
      if (toggleBtn) {
        toggleBtn.style.background = '#fff';
        toggleBtn.style.color = '#1759CA';
        toggleBtn.querySelector('svg')?.setAttribute('stroke', '#1759CA');
      }
    }
    renderDatePills();
  }

  function initDateTimeScreen() {
    const nativeInput = document.getElementById('booking-date');
    if (nativeInput) {
      nativeInput.min = formatYMD(todayDate);
    }

    // Default to today (or first open day) if no date selected yet
    if (!selectedDate) {
      let d = new Date(todayDate);
      for (let i = 0; i < 14; i++) {
        if (!closedDays.includes(d.getDay())) {
          selectedDate = formatYMD(d);
          activeYear = d.getFullYear();
          activeMonth = d.getMonth();
          break;
        }
        d.setDate(d.getDate() + 1);
      }
      if (!selectedDate) {
        selectedDate = formatYMD(todayDate);
      }
    }

    if (nativeInput) {
      nativeInput.value = selectedDate;
    }

    renderDatePills();
    loadTimeSlotsForSelectedDate();
  }

  function changeMonth(delta) {
    let newMonth = activeMonth + delta;
    let newYear = activeYear;
    if (newMonth < 0) {
      newMonth = 11;
      newYear--;
    } else if (newMonth > 11) {
      newMonth = 0;
      newYear++;
    }

    // Do not navigate before current month
    if (newYear < todayDate.getFullYear() || (newYear === todayDate.getFullYear() && newMonth < todayDate.getMonth())) {
      return;
    }

    activeYear = newYear;
    activeMonth = newMonth;
    renderDatePills();
  }

  function onNativeDateInputChange(val) {
    if (!val) return;
    const parts = val.split('-').map(Number);
    const picked = new Date(parts[0], parts[1] - 1, parts[2]);
    picked.setHours(0, 0, 0, 0);

    if (picked < todayDate) {
      showToast('Tidak bisa memilih tanggal yang sudah lewat.');
      return;
    }
    if (closedDays.includes(picked.getDay())) {
      showToast('Maaf, studio tutup pada hari tersebut.');
      return;
    }

    const needRebuildMonth = (activeYear !== picked.getFullYear() || activeMonth !== picked.getMonth());
    activeYear = picked.getFullYear();
    activeMonth = picked.getMonth();
    if (needRebuildMonth) {
      selectedDate = val;
      selectedTime = '';
      renderDatePills();
      updateStep3Summary();
      loadTimeSlotsForSelectedDate();
    } else {
      selectDate(val);
    }
  }

  function renderDatePills() {
    const label = document.getElementById('month-year-label');
    if (label) {
      label.textContent = `${MONTHS_ID[activeMonth]} ${activeYear}`;
    }

    const prevBtn = document.getElementById('prev-month-btn');
    if (prevBtn) {
      const isCurrentMonth = activeYear === todayDate.getFullYear() && activeMonth === todayDate.getMonth();
      prevBtn.style.opacity = isCurrentMonth ? '.35' : '1';
      prevBtn.style.pointerEvents = isCurrentMonth ? 'none' : 'auto';
    }

    const container = document.getElementById('date-pills-container');
    if (!container) return;
    container.innerHTML = '';

    const daysInMonth = new Date(activeYear, activeMonth + 1, 0).getDate();
    const startDay = (activeYear === todayDate.getFullYear() && activeMonth === todayDate.getMonth())
      ? todayDate.getDate()
      : 1;

    let selectedBtnEl = null;

    for (let dayNum = startDay; dayNum <= daysInMonth; dayNum++) {
      const dateObj = new Date(activeYear, activeMonth, dayNum);
      const ymd = formatYMD(dateObj);
      const dayOfWeek = dateObj.getDay();
      const dayShort = DAYS_SHORT_ID[dayOfWeek];
      const isClosed = closedDays.includes(dayOfWeek);
      const isSelected = selectedDate === ymd;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'date-pill-btn';
      btn.dataset.ymd = ymd;
      btn.dataset.closed = isClosed ? '1' : '0';

      if (isClosed) {
        btn.disabled = true;
        btn.style.cssText = `min-width:58px;height:76px;border-radius:20px;border:2px solid #E3E8F2;background:#E3E8F2;color:#888;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;cursor:not-allowed;flex-shrink:0;opacity:.6`;
      } else if (isSelected) {
        btn.style.cssText = `min-width:58px;height:76px;border-radius:20px;border:2px solid #1759CA;background:#1759CA;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;box-shadow:0 8px 18px rgba(23,89,202,.25);cursor:pointer;flex-shrink:0`;
        selectedBtnEl = btn;
      } else {
        btn.style.cssText = `min-width:58px;height:76px;border-radius:20px;border:2px solid #D7E0F2;background:#fff;color:#111;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;cursor:pointer;flex-shrink:0`;
      }

      btn.innerHTML = `
        <span style="font-size:19px;font-weight:700;line-height:1">${dayNum}</span>
        <span style="font-size:11px;font-weight:600;opacity:.9">${dayShort}</span>
      `;

      if (!isClosed) {
        btn.onclick = () => selectDate(ymd);
      }

      container.appendChild(btn);
    }

    if (selectedBtnEl && !showAllDatesGrid) {
      setTimeout(() => {
        selectedBtnEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
      }, 40);
    }
  }

  function updateDatePillStyles() {
    document.querySelectorAll('#date-pills-container .date-pill-btn').forEach(btn => {
      if (btn.dataset.closed === '1') return;
      const isSelected = btn.dataset.ymd === selectedDate;
      if (isSelected) {
        btn.style.background = '#1759CA';
        btn.style.color = '#fff';
        btn.style.borderColor = '#1759CA';
        btn.style.boxShadow = '0 8px 18px rgba(23,89,202,.25)';
      } else {
        btn.style.background = '#fff';
        btn.style.color = '#111';
        btn.style.borderColor = '#D7E0F2';
        btn.style.boxShadow = 'none';
      }
    });
  }

  function selectDate(ymd) {
    selectedDate = ymd;
    selectedTime = '';
    const nativeInput = document.getElementById('booking-date');
    if (nativeInput) nativeInput.value = ymd;

    updateStep3Summary();
    updateDatePillStyles();
    loadTimeSlotsForSelectedDate();
  }

  function normalizeTime(str) {
    return String(str || '').replace('.', ':').trim().slice(0, 5);
  }

  function timeToMinutes(t) {
    const [h, m] = normalizeTime(t).split(':').map(Number);
    return (h || 0) * 60 + (m || 0);
  }

  async function loadTimeSlotsForSelectedDate() {
    const slotContainer = document.getElementById('time-slots');
    if (!slotContainer || !selectedDate || !selectedPackage) return;

    slotContainer.innerHTML = '<div style="grid-column:span 3;text-align:center;padding:20px;font-size:13px;color:#555">Memuat jam tersedia...</div>';

    try {
      const [bookedRes, blockedRes, slotsRes] = await Promise.all([
        fetch(`/booked-times/${selectedDate}?package_id=${selectedPackage.id}`).then(r => r.json()).catch(() => ({ booked_times: [] })),
        blockedCache ? Promise.resolve(blockedCache) : fetch('/blocked-dates').then(r => r.json()).then(d => (blockedCache = d)).catch(() => []),
        fetch(`/api/time-slots?package_id=${selectedPackage.id}&date=${selectedDate}`).then(r => r.json()).catch(() => ({ slots: [] }))
      ]);

      const bookedTimes = (bookedRes.booked_times || []).map(normalizeTime);
      let allSlots = (slotsRes.slots || []).map(normalizeTime);

      // Fallback slot generator if operational_hours table has no slots configured for that day
      if (!allSlots.length) {
        const dur = Number(selectedPackage.duration_minutes) || 15;
        const step = dur + 5;
        for (let mins = 10 * 60; mins + dur <= 21 * 60; mins += step) {
          const hh = String(Math.floor(mins / 60)).padStart(2, '0');
          const mm = String(mins % 60).padStart(2, '0');
          allSlots.push(`${hh}:${mm}`);
        }
      }

      const blockedForDay = Array.isArray(blockedRes)
        ? blockedRes.filter(b => b.date === selectedDate)
        : [];
      const isFullDayBlocked = blockedForDay.some(b => !b.start_time || !b.end_time);

      slotContainer.innerHTML = '';

      if (isFullDayBlocked) {
        slotContainer.innerHTML = '<div style="grid-column:span 3;background:#fff;border-radius:16px;padding:18px;text-align:center;color:#CE004F;font-size:13px;font-weight:600">Maaf, tanggal ini tidak tersedia untuk booking.</div>';
        return;
      }

      const now = new Date();
      const todayYMD = formatYMD(now);
      const currentMinutes = now.getHours() * 60 + now.getMinutes();

      allSlots.forEach((slot) => {
        const slotMin = timeToMinutes(slot);
        const isBooked = bookedTimes.includes(slot);
        const isBlockedTime = blockedForDay.some(b => {
          if (!b.start_time || !b.end_time) return true;
          const startMin = timeToMinutes(b.start_time);
          const endMin = timeToMinutes(b.end_time);
          return slotMin >= startMin && slotMin < endMin;
        });
        const isPastToday = selectedDate === todayYMD && slotMin <= currentMinutes;
        const isUnavailable = isBooked || isBlockedTime || isPastToday;
        const isSelected = selectedTime === slot && !isUnavailable;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'time-slot-btn';
        btn.dataset.slot = slot;
        btn.dataset.unavailable = isUnavailable ? '1' : '0';
        btn.textContent = slot;

        if (isUnavailable) {
          btn.disabled = true;
          btn.style.cssText = `min-height:48px;border-radius:14px;border:0;background:#E3E8F2;font-size:14px;font-weight:700;color:#555;text-decoration:line-through;cursor:not-allowed`;
        } else if (isSelected) {
          btn.style.cssText = `min-height:48px;border-radius:14px;border:2px solid #1759CA;background:#1759CA;font-size:14px;font-weight:700;color:#fff;box-shadow:0 6px 14px rgba(23,89,202,.25);cursor:pointer`;
        } else {
          btn.style.cssText = `min-height:48px;border-radius:14px;border:2px solid #D7E0F2;background:#fff;font-size:14px;font-weight:700;color:#111;cursor:pointer`;
        }

        if (!isUnavailable) {
          btn.onclick = () => selectTimeSlot(slot);
        }

        slotContainer.appendChild(btn);
      });
    } catch (err) {
      console.error('Gagal memuat slot waktu:', err);
      slotContainer.innerHTML = '<div style="grid-column:span 3;text-align:center;padding:18px;color:#CE004F;font-size:13px">Gagal memuat slot waktu. Silakan coba lagi.</div>';
    }
  }

  function selectTimeSlot(slot) {
    selectedTime = slot;
    updateStep3Summary();
    document.querySelectorAll('#time-slots .time-slot-btn').forEach(btn => {
      if (btn.dataset.unavailable === '1') return;
      const isSelected = btn.dataset.slot === selectedTime;
      if (isSelected) {
        btn.style.background = '#1759CA';
        btn.style.borderColor = '#1759CA';
        btn.style.color = '#fff';
        btn.style.boxShadow = '0 6px 14px rgba(23,89,202,.25)';
      } else {
        btn.style.background = '#fff';
        btn.style.borderColor = '#D7E0F2';
        btn.style.color = '#111';
        btn.style.boxShadow = 'none';
      }
    });
  }

  function formatHumanDate(ymd) {
    if (!ymd) return '';
    const [y, m, d] = ymd.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    return `${DAYS_SHORT_ID[dt.getDay()]} ${d} ${MONTHS_SHORT_ID[m - 1]}`;
  }

  function updateStep3Summary() {
    const el = document.getElementById('step3-schedule-summary');
    if (!el) return;
    if (selectedDate && selectedTime) {
      el.textContent = `${formatHumanDate(selectedDate)}, ${selectedTime}`;
    } else if (selectedDate) {
      el.textContent = `${formatHumanDate(selectedDate)} • Pilih jam`;
    } else {
      el.textContent = 'Pilih tanggal & jam';
    }
  }

  function proceedToConfirmation() {
    if (!selectedDate) {
      showToast('Silakan pilih tanggal sesi foto terlebih dahulu.');
      return;
    }
    if (!selectedTime) {
      showToast('Silakan pilih jam sesi foto yang tersedia.');
      return;
    }

    populateConfirmationScreen();
    goToScreen('screen-4');
  }

  // =========================================================================
  // SCREEN 4: KONFIRMASI BOOKING & PERHITUNGAN HARGA DARI DB
  // =========================================================================
  function populateConfirmationScreen() {
    if (!selectedPackage) return;

    const includedPeople = Number(selectedPackage.included_people) || (Number(selectedPackage.id) === 2 ? 2 : 1);
    const maxPeople = Number(selectedPackage.max_people) || 8;
    const extraFee = Number(selectedPackage.extras_people) || 0;
    const dpAmount = Number(selectedPackage.downpayment) || 0;

    // Update opsi dropdown Jumlah Orang sesuai max_people paket
    const selectEl = document.getElementById('jumlah-orang');
    if (selectEl) {
      const prevVal = parseInt(selectEl.value || '1', 10) || 1;
      selectEl.innerHTML = '';
      for (let i = 1; i <= maxPeople; i++) {
        const opt = document.createElement('option');
        opt.value = String(i);
        opt.textContent = `${i} Orang`;
        selectEl.appendChild(opt);
      }
      selectEl.value = String(Math.min(prevVal, maxPeople));
    }

    // Summary header pill
    document.getElementById('confirm-summary-title').textContent =
      `${selectedPackage.title} • ${selectedBackgroundName || 'Background'}`;
    document.getElementById('confirm-summary-sub').textContent =
      `${formatHumanDate(selectedDate)} ${selectedDate.split('-')[0]} pukul ${selectedTime} WIB`;

    // Extra person note from DB
    const extraNoteEl = document.getElementById('extra-person-note');
    if (extraFee > 0) {
      extraNoteEl.textContent = `(Kalau lebih dari ${includedPeople} orang, tambah ${formatRupiah(extraFee)} per orang ya)*`;
    } else {
      extraNoteEl.textContent = `(Termasuk hingga ${includedPeople} orang)*`;
    }

    // DP Card & Agreement Checkbox from DB
    document.getElementById('dp-card-desc').textContent =
      `Pembayaran DP sebesar ${formatRupiah(dpAmount)} via QRIS.`;
    document.getElementById('agreement-dp-text').textContent = formatRupiah(dpAmount);
    document.getElementById('qris-dp-amount').textContent = `Nominal DP: ${formatRupiah(dpAmount)}`;
    document.getElementById('breakdown-dp-note').textContent = `DP dibayar sekarang: ${formatRupiah(dpAmount)}`;

    recalculateTotal();
  }

  function computePriceBreakdown() {
    if (!selectedPackage) {
      return { base: 0, peopleCount: 1, includedPeople: 1, extraPeople: 0, extraPerPerson: 0, extraTotal: 0, discount: 0, finalTotal: 0 };
    }

    const base = Number(selectedPackage.price) || 0;
    const peopleCount = parseInt(document.getElementById('jumlah-orang')?.value || '1', 10) || 1;
    const includedPeople = Number(selectedPackage.included_people) || (Number(selectedPackage.id) === 2 ? 2 : 1);
    const extraPerPerson = Number(selectedPackage.extras_people) || 0;
    const extraPeople = Math.max(0, peopleCount - includedPeople);
    const extraTotal = extraPeople * extraPerPerson;
    const subTotal = base + extraTotal;

    let discount = 0;
    if (currentVoucher && Number(currentVoucher.discount_percent) > 0) {
      discount = Math.round(subTotal * (Number(currentVoucher.discount_percent) / 100));
    }

    const finalTotal = Math.max(0, subTotal - discount);
    return { base, peopleCount, includedPeople, extraPeople, extraPerPerson, extraTotal, discount, finalTotal };
  }

  function recalculateTotal() {
    const info = computePriceBreakdown();
    if (!selectedPackage) return;

    document.getElementById('breakdown-base-label').textContent =
      `${selectedPackage.title} (${info.peopleCount} orang)`;
    document.getElementById('breakdown-base-price').textContent = formatRupiah(info.base);

    const extraRow = document.getElementById('breakdown-extra-row');
    const extraLabel = document.getElementById('breakdown-extra-label');
    const extraPrice = document.getElementById('breakdown-extra-price');
    if (info.extraPeople > 0 && info.extraPerPerson > 0) {
      extraRow.style.display = 'flex';
      extraLabel.textContent = `Tambahan orang (${info.extraPeople} × ${formatRupiah(info.extraPerPerson)})`;
      extraPrice.textContent = `+${formatRupiah(info.extraTotal)}`;
    } else {
      extraRow.style.display = 'flex';
      extraLabel.textContent = `Tambahan orang`;
      extraPrice.textContent = `Rp0`;
    }

    const voucherRow = document.getElementById('breakdown-voucher-row');
    if (info.discount > 0 && currentVoucher) {
      voucherRow.style.display = 'flex';
      document.getElementById('breakdown-voucher-label').textContent =
        `Diskon Voucher (${currentVoucher.discount_percent}%)`;
      document.getElementById('breakdown-voucher-price').textContent =
        `-${formatRupiah(info.discount)}`;
    } else {
      voucherRow.style.display = 'none';
    }

    document.getElementById('total-price').textContent = formatRupiah(info.finalTotal);
  }

  async function checkVoucher() {
    const codeInput = document.getElementById('voucher-code');
    const msgEl = document.getElementById('voucher-message');
    const code = (codeInput?.value || '').trim();

    if (!code) {
      currentVoucher = null;
      msgEl.style.display = 'none';
      recalculateTotal();
      showToast('Masukkan kode voucher terlebih dahulu.');
      return;
    }

    try {
      const res = await fetch(`/get-voucher?code=${encodeURIComponent(code)}`);
      const data = await res.json();

      msgEl.style.display = 'block';
      if (!data.valid) {
        currentVoucher = null;
        msgEl.style.color = '#CE004F';
        msgEl.textContent = data.message || 'Kode voucher tidak ditemukan atau sudah tidak berlaku.';
        recalculateTotal();
        return;
      }

      currentVoucher = data;
      msgEl.style.color = '#15803D';
      msgEl.textContent = `Voucher berhasil diterapkan! Diskon ${data.discount_percent}%`;
      recalculateTotal();
      showToast(`Voucher diskon ${data.discount_percent}% berhasil dipasang!`, 'success');
    } catch (e) {
      msgEl.style.display = 'block';
      msgEl.style.color = '#CE004F';
      msgEl.textContent = 'Gagal memeriksa kode voucher.';
    }
  }

  // =========================================================================
  // MODAL 4B: QRIS DP
  // =========================================================================
  function showQRISModal(event) {
    if (event) event.preventDefault();
    const modal = document.getElementById('qris-modal');
    if (modal) modal.style.display = 'grid';
  }

  function closeQRISModal() {
    const modal = document.getElementById('qris-modal');
    if (modal) modal.style.display = 'none';
  }

  function downloadQRIS() {
    const img = document.getElementById('qris-image');
    if (!img) return;
    const link = document.createElement('a');
    link.href = img.src;
    link.download = 'QRIS-DP-SnapFunStudio.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  function chatAdmin() {
    const pkgTitle = selectedPackage?.title || '-';
    const jadwal = (selectedDate && selectedTime) ? `${selectedDate} jam ${selectedTime}` : 'belum dipilih';
    const text = `Halo Admin Snap Fun Studio! Saya butuh bantuan saat booking:\n• Paket: ${pkgTitle}\n• Jadwal: ${jadwal}`;
    window.open(`https://wa.me/${STUDIO_WA_NUMBER}?text=${encodeURIComponent(text)}`, '_blank');
  }

  // =========================================================================
  // SUBMIT BOOKING & SCREEN 5 (BERHASIL)
  // =========================================================================
  async function submitBooking() {
    const name = (document.getElementById('nama')?.value || '').trim();
    const whatsapp = (document.getElementById('whatsapp')?.value || '').trim();
    const email = (document.getElementById('email')?.value || '').trim();
    const agreed = document.getElementById('agreement')?.checked;
    const contentConsent = Boolean(document.getElementById('content-consent')?.checked);

    if (!name) {
      showToast('Mohon isi Nama kamu terlebih dahulu.');
      document.getElementById('nama')?.focus();
      return;
    }
    if (!whatsapp || !/^[0-9+\s\-]{9,18}$/.test(whatsapp)) {
      showToast('Mohon isi Nomor WhatsApp yang aktif (contoh: 08123456789).');
      document.getElementById('whatsapp')?.focus();
      return;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showToast('Mohon isi alamat Email yang valid.');
      document.getElementById('email')?.focus();
      return;
    }
    if (!agreed) {
      showToast('Harap centang persetujuan ketentuan DP terlebih dahulu.');
      return;
    }

    const info = computePriceBreakdown();
    const submitBtn = document.getElementById('submit-booking-btn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Memproses Booking...';
    }

    try {
      const res = await fetch('/api/submit-booking', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          name,
          whatsapp,
          email,
          people_count: info.peopleCount,
          package_id: selectedPackage.id,
          background_id: selectedBackground,
          date: selectedDate,
          time: selectedTime,
          price: info.finalTotal,
          voucher_id: currentVoucher ? currentVoucher.id : null,
          confirmation: contentConsent
        })
      });

      const data = await res.json();
      if (!res.ok) {
        showToast(data.message || 'Gagal menyimpan booking. Silakan periksa kembali data kamu.');
        return;
      }

      // Populate Screen 5 (Berhasil)
      const dpAmount = Number(selectedPackage.downpayment) || 0;
      document.getElementById('success-pkg').textContent = `${selectedPackage.title} (${info.peopleCount} orang)`;
      document.getElementById('success-bg').textContent = selectedBackgroundName || '-';
      document.getElementById('success-schedule').textContent = `${formatHumanDate(selectedDate)} ${selectedDate.split('-')[0]}, ${selectedTime}`;
      document.getElementById('success-total').textContent = formatRupiah(info.finalTotal);
      document.getElementById('success-content-consent').textContent = contentConsent ? 'Boleh untuk konten' : 'Tidak untuk konten';
      document.getElementById('success-dp').textContent = formatRupiah(dpAmount);

      goToScreen('screen-5');
    } catch (err) {
      console.error('Submit error:', err);
      showToast('Terjadi kesalahan jaringan. Silakan coba lagi.');
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Booking Sekarang';
      }
    }
  }

  function confirmBookingViaWA() {
    const name = (document.getElementById('nama')?.value || '').trim();
    const whatsapp = (document.getElementById('whatsapp')?.value || '').trim();
    const email = (document.getElementById('email')?.value || '').trim();
    const contentConsent = Boolean(document.getElementById('content-consent')?.checked);
    const info = computePriceBreakdown();
    const dpAmount = Number(selectedPackage?.downpayment) || 0;

    const msg =
      `Halo Admin Snap Fun Studio! Saya ingin konfirmasi booking saya:\n\n` +
      `• Nama: ${name}\n` +
      `• Nomor WA: ${whatsapp}\n` +
      `• Email: ${email}\n` +
      `• Paket: ${selectedPackage?.title || '-'}\n` +
      `• Background: ${selectedBackgroundName || '-'}\n` +
      `• Tanggal: ${selectedDate}\n` +
      `• Waktu: ${selectedTime} WIB\n` +
      `• Jumlah Orang: ${info.peopleCount} orang\n` +
      `• Total Harga: ${formatRupiah(info.finalTotal)}\n` +
      `• Nominal DP: ${formatRupiah(dpAmount)}\n` +
      `• Izin Konten: ${contentConsent ? 'Boleh digunakan untuk konten' : 'Tidak digunakan untuk konten'}\n\n` +
      `Berikut saya lampirkan bukti pembayaran DP-nya. Terima kasih!`;

    window.open(`https://wa.me/${STUDIO_WA_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
  }

  // =========================================================================
  // BOOTSTRAP ON DOM READY
  // =========================================================================
  document.addEventListener('DOMContentLoaded', async () => {
    // 1. Render initial packages immediately
    renderPackageList();

    // 2. Refresh packages from /packages API so any live DB update is reflected
    try {
      const res = await fetch('/packages');
      const freshPackages = await res.json();
      if (Array.isArray(freshPackages) && freshPackages.length > 0) {
        packages = freshPackages;
        renderPackageList();
      }
    } catch (e) {
      console.warn('Menggunakan data paket awal:', e);
    }

    // 3. Load operational hours (closed days) from database
    try {
      const opRes = await fetch('/operational-hours');
      const opData = await opRes.json();
      if (Array.isArray(opData)) {
        closedDays = opData.filter(d => Number(d.is_open) === 0).map(d => Number(d.day_of_week));
        opData.forEach(d => {
          operationalHoursMap[d.day_of_week] = d;
        });
      }
    } catch (e) {
      console.warn('Gagal mengambil jam operasional:', e);
    }

    // 4. Auto-slide Promo Banner if multiple promos are active
    const promoSlides = document.querySelectorAll('.promo-slide');
    if (promoSlides.length > 1) {
      setInterval(() => {
        showPromoSlide(currentPromoIdx + 1);
      }, 4500);
    }
  });
</script>
</body>
</html>
