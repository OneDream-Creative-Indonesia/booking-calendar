<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking Studio Foto - Snap Fun</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
    <!-- Favicon dasar -->
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="shortcut icon" href="/img/favicon.ico">
    <!-- Favicon untuk Android -->
    <link rel="icon" type="image/png" sizes="192x192" href="/img/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/img/android-chrome-512x512.png">

    <!-- Favicon untuk iOS -->
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
    <style>
        /* Variabel Warna & Pengaturan Dasar */
        :root {
            --primary-blue: #1759CA;
            --primary-pink: #CE004F;
            --primary-yellow: #FEDD03;
            --text-dark: #282828;
            --text-light: #fff;
            --bg-light: #fff;
            --bg-disabled: #f1f3f5;
            --border-light: #e0e0e0;
            --border-radius-md: 16px;
            --border-radius-lg: 24px;
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .voucher-row {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .voucher-input-wrapper {
            flex: 1;
        }

        .voucher-check-btn {
            background-color: #1f1f22;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            height: 100%;
            white-space: nowrap;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
        }

        /* Kontainer Utama */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* Navigasi / Header */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 2rem;
            height: 70px;
        }

        .navbar-logo-svg {
            transform: scale(0.65);
            transform-origin: right center;
        }
        .modal {
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: none;
            justify-content: center;
            align-items: center;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: #fff;
            padding: 24px;
            border-radius: 10px;
            max-width: 90%;
            width: 400px;
            text-align: center;
            position: relative;
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-content img {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            margin-bottom: 16px;
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-content .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-content button {
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-weight: bold;
        }

        .modal-content button.download {
            background-color: #f0f0f0;
        }

        .modal-content button.whatsapp {
            background-color: #25D366;
            color: white;
        }
        .close {
        position: absolute;
        top: 10px; right: 15px;
        font-size: 24px;
        cursor: pointer;
        }
        .navbar .back-button {
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; background-color: var(--primary-pink);
            color: white; border-radius: 50%; text-decoration: none;
            font-size: 1.5rem; font-weight: bold; cursor: pointer;
            flex-shrink: 0;
        }
        .fc-day-selected {
            background-color: var(--primary-blue) !important; /* putih */
            border-radius: 8px;
        }

        .fc-day-selected .fc-daygrid-day-number {
            color: var(--bg-light) !important; /* angka jadi biru */
            font-weight: 800;
        }
        .fc-day-selected:hover {
            background-color: var(--primary-blue) !important;
        }

        .fc-day-selected:hover .fc-daygrid-day-number {
            color: var(--bg-light) !important;
        }


        /* Konten Halaman yang Dapat Diganti */
        .page { display: none; flex-direction: column; align-items: center; animation: fadeIn 0.5s ease-in-out; }
        .page.active { display: flex; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Judul Section */
        .section-header { text-align: center; margin-bottom: 3rem; }
        .section-header h1 { font-size: 2.5rem; font-weight: 800; margin: 0 0 0.5rem 0; }
        .section-header p { font-size: 1.25rem; font-weight: 500; margin: 0; color: #555; }
        .time-slot.booked {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* --- Halaman 1: Pemilihan Paket --- */
        .packages-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; color: var(--text-light); width: 100%; }
        .package-card { border-radius: var(--border-radius-lg); padding: 2rem; cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; }
        .package-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15); }
        .package-card .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255, 255, 255, 0.3); padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .package-card .header h2 { margin: 0; font-size: 1.5rem; font-weight: 800; }
        .package-card .header .price { font-size: 1.5rem; font-weight: 800; }
        .package-card .details-list { list-style-type: none; padding: 0; margin: 0; flex-grow: 1; }
        .package-card .details-list li { margin-bottom: 1rem; display: flex; align-items: flex-start; }
        .package-card .details-list .icon { width: 20px; height: 20px; border-radius: 50%; background-color: var(--bg-light); margin-right: 1rem; flex-shrink: 0; }
        .package-card.card-blue { background-color: var(--primary-blue); }
        .package-card.card-pink { background-color: var(--primary-pink); }
        .package-card.card-yellow { background-color: var(--primary-yellow); color: var(--text-dark); }
        .package-card.card-yellow .icon { background-color: var(--primary-blue); }

        /* --- Halaman 2: Pilih Background --- */
        .background-selection-layout { display: flex; gap: 3rem; width: 100%; align-items: flex-start; }
        .package-summary { flex: 1; max-width: 350px; }
        .package-summary h2 { font-size: 2rem; margin-top: 0; }
        .package-summary ul { list-style-type: none; padding-left: 0; }
        .package-summary ul li { display: flex; align-items: start; margin-bottom: 1rem; font-weight: 500; }
        .package-summary ul .icon { width: 18px; height: 18px; aspect-ratio: 1/1; border-radius: 50%; background-color: var(--primary-blue); margin-right: 1rem; }
        .character-art-desktop { margin-top: 2rem; width: 347px; height: 330.13px; flex-shrink: 0; position: relative; }
        .character-art-desktop svg { position: absolute; }
        .character-art-mobile {display:none;}
        .background-grid-container { flex: 2; background-color: white; padding: 2rem; border-radius: var(--border-radius-lg); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); text-align: center; }
        .background-grid-container h2 { margin-top: 0; font-size: 1.8rem; }
        .background-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .background-item { cursor: pointer; }
        .background-item .preview { width: 100%; aspect-ratio: 1 / 1; background-color: var(--primary-blue); border-radius: var(--border-radius-md); margin-bottom: 0.5rem; border: 4px solid transparent; transition: border-color 0.2s; }
        .background-item.selected .preview { border-color: var(--primary-pink); }
        .background-item p { margin: 0; font-weight: 700; }

        /* --- Halaman 3: Pilih Jadwal --- */
        .schedule-layout { display: grid; grid-template-columns: 1.2fr 1fr; gap: 2rem; width: 100%; max-width: 1100px; }
        .calendar-container { padding: 1.5rem; border-radius: var(--border-radius-lg); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); }
        .time-slots-container { display: flex; flex-direction: column; }
        .time-slots-header { text-align: center; margin-bottom: 1.5rem; }
        .time-slots-header h3 { margin: 0; font-size: 1.5rem; font-weight: 700; }
        .time-slots-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: auto; }
        .time-slot { padding: 1rem; text-align: center; border: 1px solid var(--border-light); border-radius: var(--border-radius-lg); cursor: pointer; font-weight: 700; transition: background-color 0.2s, color 0.2s; }
        .time-slot.selected { background-color: var(--primary-blue); color: white; border-color: var(--primary-blue); }
        .time-slot.disabled { background-color: var(--bg-disabled); color: #aaa; cursor: not-allowed; border-color: var(--border-light); }
        .time-slots-container .btn-primary { margin-top: 1.5rem; }

        /* Override FullCalendar Styles */
        .fc { --fc-border-color: transparent; --fc-today-bg-color: rgba(254, 221, 3, 0.2); --fc-button-text-color: var(--text-dark); font-family: 'Montserrat', sans-serif;}
        .fc .fc-toolbar-title { font-size: 1.5rem; font-weight: 800; color: var(--text-dark); }
        .fc .fc-button { background: none !important; border: none !important; box-shadow: none !important; }
        .fc .fc-daygrid-day-number { font-weight: 700; }
        .fc .fc-day.fc-day-today .fc-daygrid-day-number { color: var(--text-dark); }
        .fc-day-selected .fc-daygrid-day-number, .fc-day-selected:hover .fc-daygrid-day-number { color: white; }
        .fc-day-disabled { background-color: var(--bg-disabled) !important; color: #ccc !important; cursor: not-allowed; }
        .fc-day-disabled .fc-daygrid-day-number { color: #aaa !important; }
        .fc .fc-highlight { background: var(--primary-blue); border-radius: 5px; }

        /* --- Halaman 4: Form Booking --- */
        .form-container { width: 100%; max-width: 800px; background-color: white; padding: 2rem; border-radius: var(--border-radius-lg); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 0.5rem; font-weight: 700; font-size: 0.9rem; }
        .form-group label .red-star { color: var(--primary-pink); }
        .form-group .input-wrapper { position: relative; }
        .form-group .input-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; opacity: 0.5; }
        .form-group input, .form-group select { width: 100%; padding: 1rem 1rem 1rem 3rem; border-radius: var(--border-radius-md); border: 1px solid var(--border-light); font-size: 1rem; font-family: 'Montserrat', sans-serif; box-sizing: border-box; }
        .form-group select { appearance: none; padding-right: 3rem; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%231759CA'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5em; }
        .voucher-code-container{grid-column: span 2;}
        .dp-section { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light); }
        .dp-section h3 { margin: 0 0 1rem 0; font-size: 1.1rem; }
        .dp-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;}
        .dp-info p { margin: 0; }
        .dp-info .price { font-weight: 800; font-size: 1.2rem; }
        .dp-info .btn-secondary { background: none; border: 2px solid var(--primary-blue); color: var(--primary-blue); padding: 0.5rem 1rem; }
        .agreement-box { display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.9rem; color: #666; }
        .agreement-box input[type="checkbox"] { margin-top: 4px; width: 18px; height: 18px; flex-shrink: 0; }
        .agreement-box strong { color: var(--text-dark); }
        .agreement-box .red-star { color: var(--primary-pink); }
        .form-container .btn-primary { margin-top: 2rem; }

        /* --- Halaman 5: Konfirmasi Sukses --- */
        .success-container { display: flex; flex-direction: column; align-items: center; text-align: center; max-width: 600px; }
        .success-icon { width: 100px; height: 100px; background-color: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; position: relative; }
        .success-icon .checkmark { width: 40px; height: 40px; stroke: white; stroke-width: 5; stroke-linecap: round; stroke-linejoin: round; }
        .success-icon::before, .success-icon::after { content: ''; position: absolute; background-color: var(--primary-yellow); border-radius: 8px; opacity: 0.8; z-index: -1; }
        .success-icon::before { width: 50px; height: 50px; transform: rotate(45deg); top: 10px; right: -20px; }
        .success-icon::after { width: 40px; height: 40px; transform: rotate(15deg); bottom: 5px; left: -15px; }
        .success-container h1 { font-size: 2.2rem; }
        .success-container p { font-size: 1.1rem; line-height: 1.6; color: #555; }
        .success-container .btn-primary { margin-top: 2rem; }

        /* Komponen Umum */
        .btn { display: inline-block; padding: 1rem 2rem; font-family: 'Montserrat', sans-serif; font-size: 1rem; font-weight: 700; border-radius: var(--border-radius-lg); border: none; cursor: pointer; text-decoration: none; transition: background-color 0.3s, transform 0.2s; }
        .btn:hover { transform: translateY(-3px); }
        .btn-primary { background-color: var(--primary-blue); color: var(--text-light); width: 100%; text-align: center; box-sizing: border-box; font-size: 1.2rem; }

        /* Media Query (Responsif) */
        @media (max-width: 992px) {
            .background-selection-layout { flex-direction: column; align-items: center; }
            .schedule-layout { grid-template-columns: 1fr; }
            .package-summary { max-width: 100%; text-align: center; display: flex; flex-direction: column; align-items: center; }
            .packages-grid { grid-template-columns: 1fr; }
            .character-art-desktop { display: none; }
            .character-art-mobile svg { position: absolute; }
            .character-art-mobile { display: block; margin-top: 2rem;bottom:80px;left:10px;flex-shrink: 0; position: relative; }
            .background-grid-container {z-index: 2;}
            .schedule-layout { gap: 1.5rem;}
        }

        @media (max-width: 768px) {
            .main-container { padding: 1rem; }
            .navbar { margin-bottom: 1.5rem; height: auto; }
            .navbar-logo-svg { transform: scale(0.6); transform-origin: right center; }
            .section-header { margin-bottom: 2rem; }
            .section-header h1 { font-size: 1.8rem; }
            .section-header p { font-size: 1rem; }
            .background-grid { grid-template-columns: 1fr 1fr; gap: 1rem; }
            .form-grid { grid-template-columns: 1fr; }
            .dp-info { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
            .time-slots-grid { grid-template-columns: 1fr 1fr; }
            .success-container h1 { font-size: 1.5rem; }
            .success-container p { font-size: 1rem; }
            .voucher-code-container{grid-column: 1;}
        }
    </style>
</head>
<body>

    <main class="main-container">
        <nav class="navbar">
            <div id="backButtonContainer"></div>
            <div class="navbar-logo-svg">
                 <div style="width: 153.84px; height: 105px; position: relative;">
                    <svg width="154" height="105" viewBox="0 0 154 105" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 0; top: 0;"><path d="M132.585 10.3348C128.575 10.3348 125.722 11.2509 123.431 11.9896C121.847 12.5001 120.595 12.9003 119.308 12.9003C118.022 12.9003 116.564 12.4839 114.434 11.5061L109.13 9.07215V17.8488C108.386 16.8817 107.537 15.9978 106.575 15.2053C102.657 11.9735 96.9683 10.3348 89.671 10.3348C85.4688 10.3348 81.3351 10.8613 78.3323 11.7801L75.6512 12.5995V19.0792C74.9658 17.6124 74.0931 16.3121 73.0307 15.1892C69.9858 11.9681 65.57 10.3375 59.9072 10.3375C55.3359 10.3375 52.3938 11.4228 50.0317 12.2932C48.2627 12.946 46.9841 13.4161 45.2441 13.4161C43.9893 13.4161 42.4708 13.2066 39.8898 11.6861L38.2369 10.7136V3.44135L35.9828 2.43661C32.4528 0.865038 27.7629 0 22.7671 0C10.1603 0 2.32785 6.33465 2.32785 16.5297C2.32785 20.9436 3.70927 24.4387 7.10219 28.6027L9.60931 31.6679C11.0514 33.4598 11.6973 34.3436 11.9029 34.8084C12.2904 35.6868 11.6867 36.4095 10.1471 35.9985C6.70938 35.0824 0 26.6818 0 26.6818V51.7196L2.30412 52.7055C5.46768 54.0595 9.41685 54.8842 13.5822 55.0777C11.3941 57.9038 11.0988 61.6407 11.0988 64.6146V103.719H33.4098V90.5711H44.7775C45.695 93.9479 47.2293 96.7875 49.3831 99.0495C53.1399 102.999 58.5495 105 65.4645 105C73.2891 105 79.5898 102.244 83.4125 97.5693V103.719H126.679V72.4509C126.679 69.3051 126.226 66.5354 125.33 64.1525L130.808 64.2277V54.5377C137.414 54.3281 143.032 52.2515 147.103 48.4985C151.511 44.4366 153.841 38.7091 153.841 31.9312C153.841 19.012 145.3 10.3348 132.585 10.3348Z" fill="#1759CA"/></svg>
                    <svg width="34" height="38" viewBox="0 0 34 38" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 76.26px; top: 14.17px;"><path d="M0.262801 27.6158C0.262801 22.5492 3.15745 17.547 15.4927 15.688L18.6404 15.2393C17.6966 13.4448 15.4294 12.5448 12.4715 12.5448C8.31933 12.5448 5.23486 14.4038 3.4079 17.0984V1.44977C5.92557 0.678762 9.70075 0.165649 13.6657 0.165649C26.5045 0.165649 33.1111 5.48751 33.1111 16.3273V37.1688C30.2797 35.7585 28.139 34.9257 25.6213 34.9257C21.0289 34.9257 18.071 38.0043 11.8388 38.0043C4.47561 38.0043 0.260162 33.7705 0.260162 27.6158H0.262801ZM19.2679 24.9213V21.9716L17.8838 22.2295C14.5489 22.8715 13.2255 24.153 13.2255 26.012C13.2255 27.871 14.4224 28.7683 16.0569 28.7683C17.6914 28.7683 19.2652 27.8066 19.2652 24.9213H19.2679Z" fill="white"/></svg>
                    <svg width="39" height="47" viewBox="0 0 39 47" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 112.89px; top: 14.17px;"><path d="M20.5846 0.168335C14.3551 0.168335 11.4604 2.7339 7.30561 2.7339C5.35475 2.7339 3.40389 2.15631 0.88623 1.00113V37.7276C7.61406 38.8022 11.7188 42.1173 15.0458 46.2464V36.7229H17.69C30.2124 36.7229 38.0791 29.4775 38.0791 17.9338C38.0791 7.16117 31.2827 0.168335 20.5846 0.168335ZM16.936 25.8186H15.0484V16.647C15.0484 13.6328 16.182 11.7093 19.0134 11.7093C21.8448 11.7093 23.5452 14.017 23.5452 18.2508C23.5452 23.9596 20.7771 25.8186 16.936 25.8186Z" fill="white"/></svg>
                    <svg width="32" height="49" viewBox="0 0 32 49" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 3.76px; top: 3.83px;"><path d="M0.761993 46.1729V31.486C2.20932 34.1779 4.85353 35.7172 7.93536 35.7172C11.0172 35.7172 12.7809 34.4331 12.7809 32.0609C12.7809 30.6506 12.0875 29.4309 9.50923 26.226L6.99156 23.1473C4.22345 19.7489 3.08984 17.0544 3.08984 13.5271C3.08984 5.57521 9.19551 0.828247 19.7671 0.828247C24.2356 0.828247 28.451 1.59657 31.4722 2.94517V17.632C30.0882 15.0664 27.067 13.4008 23.8586 13.4008C21.0878 13.4008 19.4533 14.7468 19.4533 17.0571C19.4533 18.4675 20.21 19.8779 22.725 22.8921L25.2427 25.9708C28.0108 29.3691 29.1444 32.0636 29.1444 35.3975C29.1444 43.3494 22.725 48.2871 12.4672 48.2871C8.12517 48.2871 3.9071 47.5188 0.761993 46.1702V46.1729Z" fill="white"/></svg>
                    <svg width="36" height="37" viewBox="0 0 36 37" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 38.01px; top: 14.17px;"><path d="M7.24149 3.24701C12.464 3.24701 14.6679 0.168335 21.9046 0.168335C31.2186 0.168335 35.7504 5.17051 35.7504 14.5973V36.7229H21.2771V17.1628C21.2771 14.2131 19.8298 13.0579 17.879 13.0579C15.9281 13.0579 14.4808 14.2131 14.4808 17.1628V36.7229H0.00749207V1.00113C2.83888 2.66942 4.97955 3.24432 7.24413 3.24432L7.24149 3.24701Z" fill="white"/></svg>
                    <svg width="36" height="42" viewBox="0 0 36 42" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 87.18px; top: 58.02px;"><path d="M7.41109 3.10053C12.6336 3.10053 14.8375 0.0218506 22.0742 0.0218506C31.3882 0.0218506 35.92 5.02403 35.92 14.4508V41.8848H21.4467V17.0163C21.4467 14.0666 19.9994 12.9114 18.0486 12.9114C16.0977 12.9114 14.6504 14.0666 14.6504 17.0163V41.8848H0.177094V0.854653C3.00848 2.52294 5.14915 3.09784 7.41373 3.09784L7.41109 3.10053Z" fill="white"/></svg>
                    <svg width="31" height="45" viewBox="0 0 31 45" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 14.86px; top: 54.99px;"><path d="M9.41823 -0.0057373H30.3109V15.5139C28.2968 13.2062 25.4654 12.18 18.3553 12.18H15.6504V19.5543H30.3135V31.7401H15.6504V44.8876H0.860809V9.61445C0.860809 2.81772 2.87494 -0.0057373 9.41823 -0.0057373Z" fill="white"/></svg>
                    <svg width="37" height="28" viewBox="0 0 37 28" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 47.53px; top: 74.09px;"><path d="M0.529785 8.31552V0.0922852H15.1691C15.1691 12.732 15.1296 2.41607 15.1296 7.80241C15.1296 12.1625 16.5137 14.6018 18.9681 14.6018C21.0455 14.6018 22.2397 13.3822 22.2397 11.2035V0.0949742H36.7156V11.5903C36.7156 21.0815 29.6029 27.1744 18.4645 27.1744C6.69609 27.1744 0.529785 20.6974 0.529785 8.32089V8.31552Z" fill="white"/></svg>
                    <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 52.46px; top: 58.36px;"><path d="M6.22528 12.0976C9.40661 12.0976 11.9856 9.46955 11.9856 6.22769C11.9856 2.98583 9.40661 0.357788 6.22528 0.357788C3.04395 0.357788 0.464966 2.98583 0.464966 6.22769C0.464966 9.46955 3.04395 12.0976 6.22528 12.0976Z" fill="#FEDD03"/></svg>
                    <svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 68.79px; top: 58.04px;"><path d="M8.76108 12.4119C8.38145 12.4119 7.99918 12.3098 7.6591 12.0949L2.18615 8.6455C1.3109 8.09478 0.78891 7.19213 0.78891 6.23307C0.78891 5.274 1.30826 4.37135 2.18087 3.82063L7.65647 0.357793C8.58708 -0.23054 9.83932 0.0193019 10.4509 0.92195C11.0599 1.82191 10.7989 3.03082 9.86832 3.62184L5.74252 6.23038L9.86568 8.82818C10.7989 9.41651 11.0626 10.6254 10.4536 11.5281C10.066 12.1003 9.42015 12.4119 8.76108 12.4119Z" fill="#FEDD03"/></svg>
                </div>
            </div>
        </nav>

        <div id="page-content">

            <section id="page-pilih-paket" class="page">
                <div class="section-header">
                    <h1>Mau pilih yang mana?</h1>
                    <p>Pilih paket sesuai yang kamu mau!</p>
                </div>
                  <div id="package-container" class="package-grid"></div>
            </section>

            <section id="page-pilih-background" class="page">
                 <div class="background-selection-layout">
                    <div class="package-summary">
                        <h2 id="summary-title">Snap Photobox</h2>
                        <ul id="package-detail-list"></ul>
                        <div class="character-art-desktop">
                            <div style="left: 214.75px; top: 160.46px; position: absolute"><svg width="129" height="164" viewBox="0 0 129 164" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M116.699 163.507C111.405 162.696 113.595 153.123 114.953 149.978C108.23 163.563 99.1513 164.346 102.672 146.596C95.3533 161.201 87.4247 158.168 91.1118 142.137C96.7117 71.5437 82.5733 37.5675 7.3489 39.4264C5.00636 26.9456 2.85787 14.4229 0.750977 1.88628C104.654 -10.6923 134.4 61.914 127.58 151.725C126.402 157.204 121.981 164.332 116.699 163.521V163.507Z" fill="#1759CA"/></svg></div>
                            <div style="left: 5.26px; top: 76.29px; position: absolute"><svg width="129" height="164" viewBox="0 0 129 164" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.063 0.366212C17.358 1.17683 15.1679 10.7505 13.8095 13.8952C20.5322 0.310307 29.6113 -0.472362 26.0905 17.2774C33.4092 2.67228 41.3378 5.70512 37.6507 21.7358C32.0508 92.3296 46.1892 126.306 121.414 124.447C123.756 136.928 125.905 149.45 128.012 161.987C24.1084 174.566 -5.63765 101.959 1.18204 12.1482C2.36024 6.66948 6.78194 -0.458381 12.063 0.352238V0.366212Z" fill="#1759CA"/></svg></div>
                            <div style="left: 211.22px; top: 221.41px; position: absolute"><svg width="63" height="110" viewBox="0 0 63 110" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M55.7025 109.132H0.216309V0.411011H41.2037V81.2773L60.0826 96.5952C65.2112 100.76 62.2865 109.118 55.7025 109.118V109.132Z" fill="#1759CA"/></svg></div>
                            <div style="left: 115.61px; top: 221.41px; position: absolute"><svg width="64" height="110" viewBox="0 0 64 110" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.60198 109.132H63.0466V0.411011H22.0869V81.2773L3.22186 96.5952C-1.90676 100.76 1.01793 109.118 7.60198 109.118V109.132Z" fill="#1759CA"/></svg></div>
                            <div style="left: 211.22px; top: 274.73px; position: absolute"><svg width="63" height="57" viewBox="0 0 63 57" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M55.7025 56.1318H0.216309V0.730103H41.2037V28.2772L60.0826 43.5951C65.2112 47.76 62.2865 56.1178 55.7025 56.1178V56.1318Z" fill="#0C4FAF"/></svg></div>
                            <div style="left: 115.61px; top: 274.73px; position: absolute"><svg width="64" height="57" viewBox="0 0 64 57" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M63.0466 0.730103V56.1318H7.60198C1.01793 56.1318 -1.90676 47.774 3.22186 43.6091L22.0869 28.2912V0.744071H63.0466V0.730103Z" fill="#0C4FAF"/></svg></div>
                            <div style="left: 58.20px; top: 35.83px; position: absolute"><svg width="269" height="205" viewBox="0 0 269 205" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M224.49 106.956C241.983 122.065 255.719 141.492 263.995 163.546C271.341 183.141 257.161 204.119 236.383 204.119H34.9668C14.1889 204.119 0.0089912 183.141 7.3554 163.546C15.6305 141.505 29.3669 122.065 46.8597 106.956C16.0879 92.3372 4.6802 60.4016 0.591158 35.3003C-2.37513 17.0754 11.8464 0.611396 30.1708 0.835016L135.682 2.09288H238.961C258.464 2.09288 272.893 20.8629 267.501 39.7727C259.975 66.2017 244.755 89.4721 224.49 106.97V106.956Z" fill="#1759CA"/><path d="M224.49 106.956C241.983 122.065 255.719 141.492 263.995 163.546C271.341 183.141 257.161 204.119 236.383 204.119H34.9668C14.1889 204.119 0.0089912 183.141 7.3554 163.546C15.6305 141.505 29.3669 122.065 46.8597 106.956C16.0879 92.3372 4.6802 60.4016 0.591158 35.3003C-2.37513 17.0754 11.8464 0.611396 30.1708 0.835016L135.682 2.09288H238.961C258.464 2.09288 272.893 20.8629 267.501 39.7727C259.975 66.2017 244.755 89.4721 224.49 106.97V106.956Z" fill="#1759CA"/></svg></div>
                            <div style="left: 197.84px; top: 111.81px; position: absolute"><svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.7508 34.9115C8.40839 34.9115 0.84021 27.2805 0.84021 17.8605C0.84021 8.44057 8.40839 0.80957 17.7508 0.80957C27.0932 0.80957 34.6614 8.44057 34.6614 17.8605C34.6614 27.2805 27.0932 34.9115 17.7508 34.9115Z" fill="white"/></svg></div>
                            <div style="left: 200.32px; top: 121.87px; position: absolute"><svg width="30" height="19" viewBox="0 0 30 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.0004 18.4684C6.90545 18.4684 0.321411 11.8297 0.321411 3.66756C0.321411 2.13017 1.56891 0.872314 3.09364 0.872314C4.61837 0.872314 5.86587 2.13017 5.86587 3.66756C5.86587 8.74092 9.9549 12.8779 15.0004 12.8779C20.0458 12.8779 24.1349 8.74092 24.1349 3.66756C24.1349 2.13017 25.3824 0.872314 26.9071 0.872314C28.4318 0.872314 29.6793 2.13017 29.6793 3.66756C29.6793 11.8297 23.0953 18.4684 15.0004 18.4684Z" fill="#231F20"/></svg></div>
                            <div style="left: 153.17px; top: 111.81px; position: absolute"><svg width="34" height="35" viewBox="0 0 34 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.0761 34.9115C7.73371 34.9115 0.165527 27.2805 0.165527 17.8605C0.165527 8.44057 7.73371 0.80957 17.0761 0.80957C26.4185 0.80957 33.9867 8.44057 33.9867 17.8605C33.9867 27.2805 26.4185 34.9115 17.0761 34.9115Z" fill="white"/></svg></div>
                            <div style="left: 154.98px; top: 121.87px; position: absolute"><svg width="31" height="19" viewBox="0 0 31 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.6605 18.4684C7.56562 18.4684 0.981567 11.8297 0.981567 3.66756C0.981567 2.13017 2.22907 0.872314 3.7538 0.872314C5.27852 0.872314 6.52603 2.13017 6.52603 3.66756C6.52603 8.74092 10.6289 12.8779 15.6605 12.8779C20.6921 12.8779 24.795 8.74092 24.795 3.66756C24.795 2.13017 26.0425 0.872314 27.5673 0.872314C29.092 0.872314 30.3395 2.13017 30.3395 3.66756C30.3395 11.8297 23.7554 18.4684 15.6605 18.4684Z" fill="#231F20"/></svg></div>
                            <div style="left: 231.50px; top: 137.48px; position: absolute"><svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.4059 34.5857C8.06344 34.5857 0.495239 26.9547 0.495239 17.5347C0.495239 8.11477 8.06344 0.483765 17.4059 0.483765C26.7483 0.483765 34.3165 8.11477 34.3165 17.5347C34.3165 26.9547 26.7483 34.5857 17.4059 34.5857Z" fill="#0C4FAF"/></svg></div>
                            <div style="left: 119.46px; top: 137.48px; position: absolute"><svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.3662 34.5857C8.02375 34.5857 0.455566 26.9547 0.455566 17.5347C0.455566 8.11477 8.02375 0.483765 17.3662 0.483765C26.7086 0.483765 34.2768 8.11477 34.2768 17.5347C34.2768 26.9547 26.7086 34.5857 17.3662 34.5857Z" fill="#0C4FAF"/></svg></div>
                            <div style="left: 167.22px; top: 141.68px; position: absolute"><svg width="51" height="57" viewBox="0 0 51 57" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M50.6203 28.6711C50.6203 30.0128 50.5372 31.3266 50.3708 32.6124C48.6521 46.2112 38.1314 56.6654 25.4208 56.6654C20.708 56.6654 16.3001 55.2259 12.5299 52.7241C5.15577 47.8464 0.221191 38.9017 0.221191 28.6711C0.221191 13.2134 11.5042 0.676758 25.4208 0.676758C39.3374 0.676758 50.6203 13.2134 50.6203 28.6711Z" fill="#231F20"/></svg></div>
                            <div style="left: 179.53px; top: 169.67px; position: absolute"><svg width="39" height="29" viewBox="0 0 39 29" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M38.3707 4.61243C36.6519 18.2113 26.1313 28.6655 13.4207 28.6655C8.70787 28.6655 4.30002 27.2259 0.529785 24.7242C2.24857 11.1253 12.7553 0.671143 25.4799 0.671143C30.1926 0.671143 34.6005 2.11069 38.3707 4.61243Z" fill="#CE004F"/></svg></div>
                            <div style="left: 0px; top: 0px; position: absolute"><svg width="57" height="53" viewBox="0 0 57 53" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M56.706 0V41.9705C56.706 48.0083 51.8546 52.8999 45.8665 52.8999C39.8785 52.8999 35.0271 48.0083 35.0271 41.9705C35.0271 35.9328 39.8785 31.0411 45.8665 31.0411C47.017 31.0411 48.1259 31.2228 49.1655 31.5583V19.1754L21.6788 26.3451V41.9845C21.6788 48.0222 16.8274 52.9139 10.8394 52.9139C4.8514 52.9139 0 48.0222 0 41.9845C0 35.9468 4.8514 31.0551 10.8394 31.0551C11.9899 31.0551 13.0988 31.2368 14.1384 31.5722V12.6764L56.706 0Z" fill="white"/></svg></div>
                            <div style="left: 303.28px; top: 118.74px; position: absolute"><svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M44 0.755923V33.0969C44 37.7509 40.2575 41.5245 35.6418 41.5245C31.026 41.5245 27.2835 37.7509 27.2835 33.0969C27.2835 28.4428 31.026 24.6692 35.6418 24.6692C36.5289 24.6692 37.3883 24.809 38.1784 25.0745V15.5288L16.9985 21.0494V33.0969C16.9985 37.7509 13.256 41.5245 8.64025 41.5245C4.02449 41.5245 0.281982 37.7509 0.281982 33.0969C0.281982 28.4428 4.02449 24.6692 8.64025 24.6692C9.52737 24.6692 10.3868 24.809 11.1768 25.0745V10.5113L43.9862 0.741943L44 0.755923Z" fill="white"/></svg></div>
                        </div>
                        <div class="character-art-mobile">
                            <div style="left: 107.375px; top: 80.23px; position: absolute">
                                <svg width="64.5" height="82" viewBox="0 0 129 164" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M116.699 163.507C111.405 162.696 113.595 153.123 114.953 149.978C108.23 163.563 99.1513 164.346 102.672 146.596C95.3533 161.201 87.4247 158.168 91.1118 142.137C96.7117 71.5437 82.5733 37.5675 7.3489 39.4264C5.00636 26.9456 2.85787 14.4229 0.750977 1.88628C104.654 -10.6923 134.4 61.914 127.58 151.725C126.402 157.204 121.981 164.332 116.699 163.521V163.507Z" fill="#1759CA"/>
                                </svg>
                            </div>
                            <div style="left: 2.63px; top: 38.145px; position: absolute">
                                <svg width="64.5" height="82" viewBox="0 0 129 164" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.063 0.366212C17.358 1.17683 15.1679 10.7505 13.8095 13.8952C20.5322 0.310307 29.6113 -0.472362 26.0905 17.2774C33.4092 2.67228 41.3378 5.70512 37.6507 21.7358C32.0508 92.3296 46.1892 126.306 121.414 124.447C123.756 136.928 125.905 149.45 128.012 161.987C24.1084 174.566 -5.63765 101.959 1.18204 12.1482C2.36024 6.66948 6.78194 -0.458381 12.063 0.352238V0.366212Z" fill="#1759CA"/>
                                </svg>
                            </div>
                            <div style="left: 105.61px; top: 110.705px; position: absolute">
                                <svg width="31.5" height="55" viewBox="0 0 63 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M55.7025 109.132H0.216309V0.411011H41.2037V81.2773L60.0826 96.5952C65.2112 100.76 62.2865 109.118 55.7025 109.118V109.132Z" fill="#1759CA"/>
                                </svg>
                            </div>
                            <div style="left: 57.805px; top: 110.705px; position: absolute">
                                <svg width="32" height="55" viewBox="0 0 64 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.60198 109.132H63.0466V0.411011H22.0869V81.2773L3.22186 96.5952C-1.90676 100.76 1.01793 109.118 7.60198 109.118V109.132Z" fill="#1759CA"/>
                                </svg>
                            </div>
                            <div style="left: 105.61px; top: 137.365px; position: absolute">
                                <svg width="31.5" height="28.5" viewBox="0 0 63 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M55.7025 56.1318H0.216309V0.730103H41.2037V28.2772L60.0826 43.5951C65.2112 47.76 62.2865 56.1178 55.7025 56.1178V56.1318Z" fill="#0C4FAF"/>
                                </svg>
                            </div>
                            <div style="left: 57.805px; top: 137.365px; position: absolute">
                                <svg width="32" height="28.5" viewBox="0 0 64 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M63.0466 0.730103V56.1318H7.60198C1.01793 56.1318 -1.90676 47.774 3.22186 43.6091L22.0869 28.2912V0.744071H63.0466V0.730103Z" fill="#0C4FAF"/>
                                </svg>
                            </div>
                            <div style="left: 29.1px; top: 17.915px; position: absolute">
                                <svg width="134.5" height="102.5" viewBox="0 0 269 205" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M224.49 106.956C241.983 122.065 255.719 141.492 263.995 163.546C271.341 183.141 257.161 204.119 236.383 204.119H34.9668C14.1889 204.119 0.0089912 183.141 7.3554 163.546C15.6305 141.505 29.3669 122.065 46.8597 106.956C16.0879 92.3372 4.6802 60.4016 0.591158 35.3003C-2.37513 17.0754 11.8464 0.611396 30.1708 0.835016L135.682 2.09288H238.961C258.464 2.09288 272.893 20.8629 267.501 39.7727C259.975 66.2017 244.755 89.4721 224.49 106.97V106.956Z" fill="#1759CA"/><path d="M224.49 106.956C241.983 122.065 255.719 141.492 263.995 163.546C271.341 183.141 257.161 204.119 236.383 204.119H34.9668C14.1889 204.119 0.0089912 183.141 7.3554 163.546C15.6305 141.505 29.3669 122.065 46.8597 106.956C16.0879 92.3372 4.6802 60.4016 0.591158 35.3003C-2.37513 17.0754 11.8464 0.611396 30.1708 0.835016L135.682 2.09288H238.961C258.464 2.09288 272.893 20.8629 267.501 39.7727C259.975 66.2017 244.755 89.4721 224.49 106.97V106.956Z" fill="#1759CA"/>
                                </svg>
                            </div>
                            <div style="left: 98.92px; top: 55.905px; position: absolute">
                                <svg width="17.5" height="17.5" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.7508 34.9115C8.40839 34.9115 0.84021 27.2805 0.84021 17.8605C0.84021 8.44057 8.40839 0.80957 17.7508 0.80957C27.0932 0.80957 34.6614 8.44057 34.6614 17.8605C34.6614 27.2805 27.0932 34.9115 17.7508 34.9115Z" fill="white"/>
                                </svg>
                            </div>
                            <div style="left: 100.16px; top: 60.935px; position: absolute">
                                <svg width="15" height="9.5" viewBox="0 0 30 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.0004 18.4684C6.90545 18.4684 0.321411 11.8297 0.321411 3.66756C0.321411 2.13017 1.56891 0.872314 3.09364 0.872314C4.61837 0.872314 5.86587 2.13017 5.86587 3.66756C5.86587 8.74092 9.9549 12.8779 15.0004 12.8779C20.0458 12.8779 24.1349 8.74092 24.1349 3.66756C24.1349 2.13017 25.3824 0.872314 26.9071 0.872314C28.4318 0.872314 29.6793 2.13017 29.6793 3.66756C29.6793 11.8297 23.0953 18.4684 15.0004 18.4684Z" fill="#231F20"/>
                                </svg>
                            </div>
                            <div style="left: 76.585px; top: 55.905px; position: absolute">
                                <svg width="17" height="17.5" viewBox="0 0 34 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.0761 34.9115C7.73371 34.9115 0.165527 27.2805 0.165527 17.8605C0.165527 8.44057 7.73371 0.80957 17.0761 0.80957C26.4185 0.80957 33.9867 8.44057 33.9867 17.8605C33.9867 27.2805 26.4185 34.9115 17.0761 34.9115Z" fill="white"/>
                                </svg>
                            </div>
                            <div style="left: 77.49px; top: 60.935px; position: absolute">
                                <svg width="15.5" height="9.5" viewBox="0 0 31 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.6605 18.4684C7.56562 18.4684 0.981567 11.8297 0.981567 3.66756C0.981567 2.13017 2.22907 0.872314 3.7538 0.872314C5.27852 0.872314 6.52603 2.13017 6.52603 3.66756C6.52603 8.74092 10.6289 12.8779 15.6605 12.8779C20.6921 12.8779 24.795 8.74092 24.795 3.66756C24.795 2.13017 26.0425 0.872314 27.5673 0.872314C29.092 0.872314 30.3395 2.13017 30.3395 3.66756C30.3395 11.8297 23.7554 18.4684 15.6605 18.4684Z" fill="#231F20"/>
                                </svg>
                            </div>
                            <div style="left: 115.75px; top: 68.74px; position: absolute">
                                <svg width="17.5" height="17.5" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.4059 34.5857C8.06344 34.5857 0.495239 26.9547 0.495239 17.5347C0.495239 8.11477 8.06344 0.483765 17.4059 0.483765C26.7483 0.483765 34.3165 8.11477 34.3165 17.5347C34.3165 26.9547 26.7483 34.5857 17.4059 34.5857Z" fill="#0C4FAF"/>
                                </svg>
                            </div>
                            <div style="left: 59.73px; top: 68.74px; position: absolute">
                                <svg width="17.5" height="17.5" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.3662 34.5857C8.02375 34.5857 0.455566 26.9547 0.455566 17.5347C0.455566 8.11477 8.02375 0.483765 17.3662 0.483765C26.7086 0.483765 34.2768 8.11477 34.2768 17.5347C34.2768 26.9547 26.7086 34.5857 17.3662 34.5857Z" fill="#0C4FAF"/>
                                </svg>
                            </div>
                            <div style="left: 83.61px; top: 70.84px; position: absolute">
                                <svg width="25.5" height="28.5" viewBox="0 0 51 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M50.6203 28.6711C50.6203 30.0128 50.5372 31.3266 50.3708 32.6124C48.6521 46.2112 38.1314 56.6654 25.4208 56.6654C20.708 56.6654 16.3001 55.2259 12.5299 52.7241C5.15577 47.8464 0.221191 38.9017 0.221191 28.6711C0.221191 13.2134 11.5042 0.676758 25.4208 0.676758C39.3374 0.676758 50.6203 13.2134 50.6203 28.6711Z" fill="#231F20"/>
                                </svg>
                            </div>
                            <div style="left: 89.765px; top: 84.835px; position: absolute">
                                <svg width="19.5" height="14.5" viewBox="0 0 39 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M38.3707 4.61243C36.6519 18.2113 26.1313 28.6655 13.4207 28.6655C8.70787 28.6655 4.30002 27.2259 0.529785 24.7242C2.24857 11.1253 12.7553 0.671143 25.4799 0.671143C30.1926 0.671143 34.6005 2.11069 38.3707 4.61243Z" fill="#CE004F"/>
                                </svg>
                            </div>
                            <div style="left: 151.64px; top: 59.37px; position: absolute">
                                <svg width="22" height="21" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M44 0.755923V33.0969C44 37.7509 40.2575 41.5245 35.6418 41.5245C31.026 41.5245 27.2835 37.7509 27.2835 33.0969C27.2835 28.4428 31.026 24.6692 35.6418 24.6692C36.5289 24.6692 37.3883 24.809 38.1784 25.0745V15.5288L16.9985 21.0494V33.0969C16.9985 37.7509 13.256 41.5245 8.64025 41.5245C4.02449 41.5245 0.281982 37.7509 0.281982 33.0969C0.281982 28.4428 4.02449 24.6692 8.64025 24.6692C9.52737 24.6692 10.3868 24.809 11.1768 25.0745V10.5113L43.9862 0.741943L44 0.755923Z" fill="white"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="background-grid-container">
                        <h2>Pilih Background</h2>
                        <div class="background-grid" id="background-grid"></div>
                        <button class="btn btn-primary" onclick="goToPage('page-pilih-jadwal')">Lanjut</button>
                    </div>
                </div>
            </section>

            <section id="page-pilih-jadwal" class="page">
                <div class="section-header">
                    <h1>Pilih Tanggal & Waktu</h1>
                    <p>Pilih slot yang tersedia untuk sesi fotomu.</p>
                </div>
                <div class="schedule-layout">
                    <div class="calendar-container">
                        <div id="calendar"></div>
                    </div>
                    <div class="time-slots-container">
                        <div class="time-slots-header">
                            <h3 id="time-slots-date-header">Pilih Waktu Dulu</h3>
                        </div>
                        <div class="time-slots-grid" id="time-slots-grid">
                            <p style="grid-column: 1 / -1; text-align: center; color: #888;">Jadwal akan muncul di sini setelah kamu memilih tanggal.</p>
                        </div>
                        <button class="btn btn-primary" onclick="goToPage('page-form',bookingData)">Lanjut</button>
                    </div>
                </div>
            </section>

            <!-- Modal QRIS -->
          <div id="qrisModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h3>Scan QRIS untuk pembayaran DP</h3>
                    <img id="qrisImage" src="/img/qrcode.png" alt="QRIS" />

                    <div class="button-group">
                        <button class="download" onclick="downloadQRIS()">📥 Unduh QRIS</button>
                    </div>
                </div>
            </div>


            <section id="page-form" class="page">
                <div class="form-container">
                    <div class="section-header" style="margin-bottom: 2rem; text-align: left;">
                        <h1>Konfirmasi Booking Kamu</h1>
                        <p>Biar sesi foto kamu nggak diambil orang, isi dulu data di bawah ini ya!</p>
                    </div>
            <div class="form-grid">
                <!-- Nama -->
                <div class="form-group">
                    <label for="nama">Nama<span class="red-star">*</span></label>
                    <div class="input-wrapper">
                        <img src="https://api.iconify.design/solar:user-circle-bold-duotone.svg" class="input-icon" alt="user icon">
                        <input type="text" id="nama" placeholder="ketik nama kamu">
                    </div>
                </div>

                <!-- WhatsApp -->
                <div class="form-group">
                    <label for="whatsapp">Nomor WhatsApp<span class="red-star">*</span></label>
                    <div class="input-wrapper">
                        <img src="https://api.iconify.design/solar:phone-bold-duotone.svg" class="input-icon" alt="phone icon">
                        <input type="text" id="whatsapp" placeholder="ketik nomor wa kamu">
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email<span class="red-star">*</span></label>
                    <div class="input-wrapper">
                        <img src="https://api.iconify.design/solar:letter-bold-duotone.svg" class="input-icon" alt="email icon">
                        <input type="email" id="email" placeholder="ketik email kamu">
                    </div>
                </div>

                <!-- Jumlah Orang -->
                <div class="form-group">
                    <label for="jumlah-orang"><span class="red-star">*</span></label>
                    <div class="input-wrapper">
                        <img src="https://api.iconify.design/solar:users-group-rounded-bold-duotone.svg" class="input-icon" alt="group icon">
                        <select id="jumlah-orang">
                            <option value="">pilih jumlah orangnya</option>
                            <option value="1">1 Orang</option>
                            <option value="2">2 Orang</option>
                            <option value="3">3 Orang</option>
                            <option value="4">4 Orang</option>
                            <option value="5">5 Orang</option>
                            <option value="6">6 Orang</option>
                        </select>
                    </div>
                </div>

                <!-- Kode Voucher -->
                <div class="form-group voucher-code-container">
                    <label for="voucher-code">Kode Voucher</label>
                    <div class="voucher-row">
                        <div class="input-wrapper voucher-input-wrapper">
                            <img src="https://api.iconify.design/solar:ticket-sale-bold-duotone.svg" class="input-icon" alt="voucher icon">
                            <input type="text" id="voucher-code" placeholder="masukkan kode voucher">
                        </div>
                        <button type="button" class="voucher-check-btn" onclick="checkVoucher()">Cek</button>
                    </div>
                    <p id="voucher-message" style="margin-top: 6px; font-size: 0.9em;"></p>
                </div>

            </div>

                    <div class="dp-section">
                        <h3>Down Payment (DP)</h3>
                        <div class="dp-info"><p>Pembayaran DP via QRIS. <a href="#" class="btn-link" onclick="showQRISModal(event)">Klik disini</a></p><span id="dp-price" class="price"></span></div>
                        <div class="dp-info"><p>Kalau ada kendala, tenang~ tinggal chat admin kami aja.</p><button class="btn btn-secondary" onclick="chatAdmin()">💬 Chat Kami</button></div>
                    </div>
                    <div class="agreement-box"><input type="checkbox" id="agreement"><label for="agreement">Saya setuju, kalau batalin di hari-H, <strong id="confirmedDp" class="red-star"></strong><span class="red-star">*</span></label></div>
                    <button class="btn btn-primary" onclick="submitBooking()">Booking Sekarang</button>
                </div>
            </section>

            <section id="page-sukses" class="page">
                <div class="success-container">
                    <div class="success-icon"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg></div>
                    <h1>Booking kamu udah masuk!</h1>
                    <h4 id="totalPriceQRIS" style="margin-top: 1rem; text-align: center;"></h4>
                    <h4 id="dpPriceQRIS" style="margin-top: 0.5rem; text-align: center; color: #555;"></h4>
                    <p>Jangan lupa <strong>kirim bukti transfer DP ke WhatsApp admin</strong> untuk konfirmasi, ya! Tanpa konfirmasi, jadwal belum bisa kami kunci.
                    Kamu juga bisa tambah cetak foto saat di lokasi, tinggal konfirmasi langsung ke tim kami di sana.</p>
                    <button class="whatsapp btn btn-primary" onclick="confirmBookingViaWA()">💬 Konfirmasi via WhatsApp</button>
                </div>
            </section>
        </div>
    </main>

<script>
    function showQRISModal(event) {
        event.preventDefault();
        document.getElementById('qrisModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('qrisModal').style.display = 'none';
    }

    function downloadQRIS() {
        const link = document.createElement('a');
        link.href = document.getElementById('qrisImage').src;
        link.download = 'QRIS-SnapPhotobox.jpg';
        link.click();
    }

    function confirmBookingViaWA() {
        const nomor = '+6285117607254';
        const pesan = encodeURIComponent(`Hallo kak, aku udah booking nih, ini bukti dp aku ya…`);
        window.open(`https://wa.me/${nomor}?text=${pesan}`, '_blank');
    }
       function chatAdmin() {
        const nomor = '+6285117607254';
        const pesan = encodeURIComponent("Halo kak, saya mau tanya tentang booking SnapFun Photostudio.");
        window.open(`https://wa.me/${nomor}?text=${pesan}`, '_blank');
    }
</script>


<script>
    // --- GLOBAL VARIABLES ---
    const pages = document.querySelectorAll('.page');
    const backButtonContainer = document.getElementById('backButtonContainer');
    const timeSlotsGrid = document.getElementById('time-slots-grid');
    const timeSlotsDateHeader = document.getElementById('time-slots-date-header');
    let pageHistory = ['page-pilih-paket'];
    let calendar;
    let isCalendarRendered = false;
    let todayCloseTime;
    let bookedTimes = [];
    let blockedDates = [];
    // Objek untuk menyimpan data booking sementara
    let bookingData = {
        package: null,
        background: null,
        date: null,
        time: null
    };

    // Mapping hari dalam seminggu
    const dayMap = { 0: 'Minggu', 1: 'Senin', 2: 'Selasa', 3: 'Rabu', 4: 'Kamis', 5: 'Jumat', 6: 'Sabtu' };

    let closedDays = []; // Hari libur studio (diambil dari backend)

    // --- NAVIGATION FUNCTIONS ---
    function updateNav() {
        if (pageHistory.length > 1) {
            backButtonContainer.innerHTML = `<a href="#" class="back-button" onclick="goBack(event)">&lt;</a>`;
        } else {
            backButtonContainer.innerHTML = '';
        }
    }

    // Fungsi untuk navigasi ke halaman tertentu
    function goToPage(pageId, data = null) {
         if (pageId === 'page-pilih-jadwal') {
                if (!bookingData.background_id) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Background',
                        text: 'Harap pilih background terlebih dahulu sebelum melanjutkan.'
                    });
                    return; // jangan lanjut
                }
            }

            if (pageId === 'page-form') {
                if (!bookingData.time) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Waktu',
                        text: 'Harap pilih waktu terlebih dahulu sebelum melanjutkan.'
                    });
                    return; // jangan lanjut
                }
            }
        pages.forEach(page => page.classList.remove('active'));
        const targetPage = document.getElementById(pageId);
        if (targetPage) targetPage.classList.add('active');

        if (pageId === 'page-pilih-paket') {
            pageHistory = ['page-pilih-paket'];
        } else if (pageHistory[pageHistory.length - 1] !== pageId) {
            pageHistory.push(pageId); // hanya push kalau bukan duplikat
        }

        if (pageId === 'page-pilih-jadwal' && !isCalendarRendered) {
            setTimeout(() => {
                calendar.render();
                isCalendarRendered = true;
            }, 0);
        }
        if (pageId === 'page-form') {
            let dp = data.downPayment ?? 0;;
            let extraPerPersonShort = (data.extras_people / 1000).toLocaleString('id-ID') + " rb";

            document.querySelector('label[for="jumlah-orang"]').innerHTML =
            `Jumlah Orang (Tambah ${extraPerPersonShort}/org jika lebih dari 1)<span class="red-star">*</span>`;
            document.getElementById('dp-price').textContent = `Rp ${dp.toLocaleString()}`;
            document.getElementById('confirmedDp').textContent = `DP sebesar Rp ${dp.toLocaleString()} hangus`;
        }
        if (pageId === 'page-sukses') {
            let dp = data.downPayment ?? 0;
            document.getElementById('totalPriceQRIS').textContent = `Total harga sementara: Rp ${data.price.toLocaleString()}`;
            document.getElementById('dpPriceQRIS').textContent = `Total DP: Rp ${dp.toLocaleString()}`;
        }
        updateNav();
        window.scrollTo(0, 0);
    }


    // Fungsi untuk kembali ke halaman sebelumnya
    function goBack(event) {
        event.preventDefault();

        if (pageHistory.length > 1) {
            pageHistory.pop(); // keluar dari current
            const prevPageId = pageHistory[pageHistory.length - 1];
            pages.forEach(page => page.classList.remove('active'));
            document.getElementById(prevPageId).classList.add('active');
            updateNav();
        }
    }

  async function checkVoucher() {
    const code = document.getElementById('voucher-code').value.trim();
    const peopleCount = parseInt(document.getElementById('jumlah-orang').value);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!bookingData.price) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Paket Dulu',
            text: 'Harap pilih paket sebelum memasukkan kode voucher.'
        });
        return;
    }

    try {
        const res = await fetch('/get-voucher');
        const vouchers = await res.json();

        const matched = vouchers.find(v => v.code_voucher === code && v.is_active);

        if (!matched) {
            document.getElementById('voucher-message').textContent = 'Kode voucher tidak ditemukan atau tidak aktif.';
            document.getElementById('voucher-message').style.color = 'red';
            bookingData.voucher_id = null;
            bookingData.voucher_discount = 0;
            return;
        }

        const now = new Date();
        const nowUtc = new Date(now.getTime() + now.getTimezoneOffset() * 60000);
        const start = new Date(matched.start_date + 'Z'); // pastikan ada 'Z' untuk UTC
        const end = new Date(matched.end_date + 'Z');

        // 🔴 Cek usage limit dulu
        if (matched.usage_limit !== null && matched.used_count >= matched.usage_limit) {
            document.getElementById('voucher-message').textContent = 'Maaf, batas penggunaan voucher sudah terpenuhi.';
            document.getElementById('voucher-message').style.color = 'red';
            bookingData.voucher_id = null;
            bookingData.voucher_discount = 0;
            return;
        }

        // ⏳ Cek masa berlaku
        if (now < start || now > end) {
            document.getElementById('voucher-message').textContent = 'Voucher sudah tidak berlaku.';
            document.getElementById('voucher-message').style.color = 'red';
            bookingData.voucher_id = null;
            bookingData.voucher_discount = 0;
            return;
        }

        let finalPrice = Number(bookingData.price);
        let extraPerPerson = Number(bookingData.extras_people) || 0;
        if (peopleCount > 1) {
            finalPrice += (peopleCount - 1) * extraPerPerson;
        }

        const percent = parseFloat(matched.discount_percent);
        const discountAmount = Math.round(finalPrice * (percent / 100));

        bookingData.voucher_discount = discountAmount;
        bookingData.voucher_id = matched.id;

        document.getElementById('voucher-message').textContent = '🎉 Selamat! Voucher berhasil digunakan';
        document.getElementById('voucher-message').style.color = 'green';

    } catch (err) {
        document.getElementById('voucher-message').textContent = 'Terjadi kesalahan. Coba lagi.';
        document.getElementById('voucher-message').style.color = 'red';
        bookingData.voucher_discount = 0;
        bookingData.voucher_id = null;
        console.error(err);
    }
}



    // --- BOOKING FLOW ---

    // Fungsi pilih paket
    async function selectPackage(packageName, packageId, packagePrice,packageDescription, packageDownPayment, packageExtrasPeople) {
        bookingData.package = packageName;
        bookingData.packageDescription = packageDescription;
        bookingData.package_id = packageId;
        bookingData.price = packagePrice;
        bookingData.downPayment = packageDownPayment;
        bookingData.extras_people = packageExtrasPeople;
        document.getElementById('summary-title').textContent = packageName;
        const ul = document.getElementById('package-detail-list');
        ul.innerHTML = '';

        // Tambah deskripsi (pisah berdasarkan baris kalau multi-line)
        if (packageDescription) {
            const lines = packageDescription.split('\n');
            lines.forEach(line => {
                const li = document.createElement('li');
                li.innerHTML = `<span class="icon"></span>${line}`;
                ul.appendChild(li);
            });
        }
        loadBackgrounds();
        goToPage('page-pilih-background');
    }

    function loadBackgrounds() {
        fetch(`/api/backgrounds?package_id=${bookingData.package_id}`)
            .then(res => res.json())
            .then(response => {
                const container = document.getElementById('background-grid');
                container.innerHTML = '';
                const data = response.data;

                if (!Array.isArray(data)) {
                    console.warn('Data background bukan array:', data);
                    return;
                }

                data.forEach(bg => {
                    const div = document.createElement('div');
                    div.classList.add('background-item');
                    div.onclick = () => selectBackground(div, bg.id, bg.name);
                    div.innerHTML = `
                        <div class="preview" style="background-image: url('${bg.image_url}'); background-size: cover; background-position: center;"></div>
                        <p>${bg.name}</p>
                    `;
                    container.appendChild(div);
                });
            })

            .catch(err => console.error('Gagal load background:', err));
    }
    //ambil data blocked dates
    async function loadBlockedDates() {
            try {
                const res = await fetch('/blocked-dates');
                const data = await res.json();
                blockedDates = Array.isArray(data) ? data : [];
            } catch (err) {
                console.error('Gagal load blocked dates:', err);
            }
        }

    // Fungsi pilih background foto
    function selectBackground(element, id, name) {
        document.querySelectorAll('.background-item').forEach(item => item.classList.remove('selected'));
        element.classList.add('selected');
        bookingData.background_id = id;
    }


    // Fungsi pilih jam booking
    function selectTime(element, time) {
        document.querySelectorAll('.time-slot').forEach(item => item.classList.remove('selected'));
        element.classList.add('selected');
        bookingData.time = time;
    }

    // Update tampilan slot waktu berdasarkan tanggal yang dipilih
    async function updateSlotsForDate(date, selectedPackageId) {
        const dateStr = date.toISOString().split('T')[0];
        timeSlotsGrid.innerHTML = '';
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const isToday = date.toDateString() === today.toDateString();
        const now = new Date();

        const dayName = dayMap[date.getDay()];
        const isClosed = closedDays.includes(dayName);

        if (date < today) {
            timeSlotsGrid.innerHTML = `<p style="grid-column: 1 / -1; text-align: center; color: #888;">Tidak bisa booking untuk tanggal yang sudah lewat.</p>`;
            return;
        }

        if (isClosed) {
            const message = `Studio tutup pada hari ${dayName}.`;
            timeSlotsGrid.innerHTML = `<p style="grid-column: 1 / -1; text-align: center; color: #888;">${message}</p>`;
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: message });
            return;
        }
        const blockedTimeRanges = blockedDates
            .filter(b => b.date === dateStr)
            .filter(b => b.package_ids.includes(String(selectedPackageId)) || b.package_ids.includes(parseInt(selectedPackageId)))
            .map(b => ({ start: b.start_time, end: b.end_time }));

        // Fetch slot dari backend
        try {
            const res = await fetch(`/api/time-slots?date=${dateStr}`);
            const data = await res.json();
            const slots = data.slots;

            if (!slots.length) {
                timeSlotsGrid.innerHTML = `<p style="grid-column: 1 / -1; text-align: center; color: #888;">Tidak ada slot tersedia pada hari ini.</p>`;
                return;
            }

            for (const slot of slots) {
                const slotEl = document.createElement('div');
                slotEl.classList.add('time-slot');
                slotEl.textContent = slot;

                const [hour, minute] = slot.split(':').map(Number);
                const slotTime = new Date(date);
                slotTime.setHours(hour, minute, 0, 0);

                const isBooked = bookedTimes.includes(slot + ':00');
                const isPastTime = isToday && slotTime < now;

                // Cek apakah slot ini masuk dalam blockedTimeRanges
                const isBlocked = blockedTimeRanges.some(range => {
                    return slot >= range.start.slice(0,5) && slot < range.end.slice(0,5);
                });

                if (isBooked || isPastTime || isBlocked) {
                    slotEl.classList.add('booked');
                    slotEl.style.opacity = '0.5';
                    slotEl.style.pointerEvents = 'none';
                    slotEl.title = isBlocked
                        ? 'Waktu diblokir'
                        : isBooked
                            ? 'Sudah dibooking'
                            : 'Waktu telah lewat';
                } else {
                    slotEl.onclick = () => selectTime(slotEl, slot);
                }

                timeSlotsGrid.appendChild(slotEl);
            }
        } catch (error) {
            console.error('Gagal ambil slot dari backend:', error);
            timeSlotsGrid.innerHTML = `<p style="grid-column: 1 / -1; text-align: center; color: #888;">Gagal memuat slot waktu.</p>`;
        }
    }


    // --- DATA FETCHING ---

    // Ambil hari tutup dari backend
    async function loadClosedDays() {
        try {
            const res = await fetch('/operational-hours');
            const data = await res.json();
            closedDays = data.closed_days || [];
        } catch (error) {
            console.error('Gagal load hari tutup:', error);
        }
    }

    // Ambil jam tutup hari ini
    async function loadTodayCloseTime() {
        try {
            const res = await fetch('/jam-tutup');
            const data = await res.json();
            todayCloseTime = data.close_time;
        } catch (err) {
            console.error('Gagal ambil jam tutup hari ini:', err);
        }
    }

    // Ambil data slot yang sudah dibooking
    async function loadBookedTimes(dateStr) {
        try {
            const res = await fetch(`/booked-times/${dateStr}?package_id=${bookingData.package_id}`);

            const data = await res.json();
            bookedTimes = data.booked_times || [];
        } catch (err) {
            console.error('Gagal ambil booking yang sudah ada:', err);
            bookedTimes = [];
        }
    }

    // Ambil daftar paket dari backend
  function loadPackages() {
    fetch('/packages')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('package-container');
            container.innerHTML = '';
            container.className = 'packages-grid';

            data.forEach(pkg => {
                const div = document.createElement('div');
                div.classList.add('package-card');

                if (pkg.id == 1) div.classList.add('card-blue');
                if (pkg.id == 2) div.classList.add('card-pink');
                if (pkg.id == 3) div.classList.add('card-yellow');

                div.onclick = () => selectPackage(pkg.title, pkg.id, pkg.price, pkg.description, pkg.downpayment, pkg.extras_people);

                const icons = [
                    `<!-- Icon jam --> <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<ellipse cx="9.67691" cy="8.99019" rx="9.60969" ry="8.99019" fill="white"/>
<mask id="path-2-inside-1_61_93" fill="white">
<path d="M13.4632 4.47437H12.9923V5.36329C12.9923 6.45466 12.4394 7.50725 11.3934 8.40725C11.0139 8.73374 10.6362 8.98373 10.3459 9.15561C10.6362 9.32749 11.0139 9.57748 11.3934 9.90396C12.4394 10.804 12.9923 11.8566 12.9923 12.9479V13.8368H13.4632C13.6466 13.8368 13.7953 13.976 13.7953 14.1476C13.7953 14.3191 13.6466 14.4583 13.4632 14.4583C13.244 14.4583 6.22628 14.4583 5.89058 14.4583C5.70717 14.4583 5.55847 14.3191 5.55847 14.1476C5.55847 13.976 5.70717 13.8368 5.89058 13.8368H6.36152V12.9479C6.36152 11.7638 7.00894 10.7226 7.96046 9.90396C8.3399 9.57748 8.7176 9.32749 9.00796 9.15561C8.7176 8.98373 8.3399 8.73374 7.96046 8.40725C6.91442 7.50725 6.36152 6.45466 6.36152 5.36329V4.47437H5.89058C5.70717 4.47437 5.55847 4.33526 5.55847 4.16367C5.55847 3.99208 5.70717 3.85297 5.89058 3.85297H13.4632C13.6466 3.85297 13.7953 3.99208 13.7953 4.16367C13.7953 4.33526 13.6466 4.47437 13.4632 4.47437ZM12.3281 12.9479C12.3281 12.5854 12.2563 12.2315 12.1143 11.8875H7.23951C7.09748 12.2315 7.02574 12.5855 7.02574 12.9479V13.8368H12.3281V12.9479ZM7.58034 11.2661H11.7735C11.28 10.5504 10.4714 9.91844 9.67692 9.50685C8.88167 9.91886 8.07351 10.5508 7.58034 11.2661ZM9.6769 8.80437C11.0523 8.09174 12.3281 6.85979 12.3281 5.36329V4.9144L11.5234 5.71658C11.0351 6.20338 10.3732 6.47146 9.65965 6.47148C9.65961 6.47148 9.65967 6.47148 9.65965 6.47148C8.9461 6.47148 8.28411 6.20334 7.79584 5.71658L7.02574 4.94883V5.36331C7.02572 6.85277 8.29321 8.08748 9.6769 8.80437ZM7.4607 4.47437L8.28017 5.29133C8.64157 5.65164 9.13148 5.85007 9.65963 5.85007C10.1878 5.85005 10.6777 5.65164 11.0391 5.29133L11.8586 4.47437H7.4607Z"/>
</mask>
<path d="M13.4632 4.47437H12.9923V5.36329C12.9923 6.45466 12.4394 7.50725 11.3934 8.40725C11.0139 8.73374 10.6362 8.98373 10.3459 9.15561C10.6362 9.32749 11.0139 9.57748 11.3934 9.90396C12.4394 10.804 12.9923 11.8566 12.9923 12.9479V13.8368H13.4632C13.6466 13.8368 13.7953 13.976 13.7953 14.1476C13.7953 14.3191 13.6466 14.4583 13.4632 14.4583C13.244 14.4583 6.22628 14.4583 5.89058 14.4583C5.70717 14.4583 5.55847 14.3191 5.55847 14.1476C5.55847 13.976 5.70717 13.8368 5.89058 13.8368H6.36152V12.9479C6.36152 11.7638 7.00894 10.7226 7.96046 9.90396C8.3399 9.57748 8.7176 9.32749 9.00796 9.15561C8.7176 8.98373 8.3399 8.73374 7.96046 8.40725C6.91442 7.50725 6.36152 6.45466 6.36152 5.36329V4.47437H5.89058C5.70717 4.47437 5.55847 4.33526 5.55847 4.16367C5.55847 3.99208 5.70717 3.85297 5.89058 3.85297H13.4632C13.6466 3.85297 13.7953 3.99208 13.7953 4.16367C13.7953 4.33526 13.6466 4.47437 13.4632 4.47437ZM12.3281 12.9479C12.3281 12.5854 12.2563 12.2315 12.1143 11.8875H7.23951C7.09748 12.2315 7.02574 12.5855 7.02574 12.9479V13.8368H12.3281V12.9479ZM7.58034 11.2661H11.7735C11.28 10.5504 10.4714 9.91844 9.67692 9.50685C8.88167 9.91886 8.07351 10.5508 7.58034 11.2661ZM9.6769 8.80437C11.0523 8.09174 12.3281 6.85979 12.3281 5.36329V4.9144L11.5234 5.71658C11.0351 6.20338 10.3732 6.47146 9.65965 6.47148C9.65961 6.47148 9.65967 6.47148 9.65965 6.47148C8.9461 6.47148 8.28411 6.20334 7.79584 5.71658L7.02574 4.94883V5.36331C7.02572 6.85277 8.29321 8.08748 9.6769 8.80437ZM7.4607 4.47437L8.28017 5.29133C8.64157 5.65164 9.13148 5.85007 9.65963 5.85007C10.1878 5.85005 10.6777 5.65164 11.0391 5.29133L11.8586 4.47437H7.4607Z" fill="white"/>
<path d="M12.9923 4.47437H11.9923V3.47437H12.9923V4.47437ZM11.3934 8.40725L10.7411 7.64923L10.7412 7.64921L11.3934 8.40725ZM10.3459 9.15561L9.83645 10.0161L8.38278 9.15561L9.83645 8.29508L10.3459 9.15561ZM11.3934 9.90396L10.7412 10.662L10.7411 10.662L11.3934 9.90396ZM12.9923 13.8368V14.8368H11.9923V13.8368H12.9923ZM6.36152 13.8368H7.36152V14.8368H6.36152V13.8368ZM7.96046 9.90396L8.61268 10.662L8.61267 10.662L7.96046 9.90396ZM9.00796 9.15561L9.51736 8.29508L10.971 9.15561L9.51736 10.0161L9.00796 9.15561ZM7.96046 8.40725L8.61266 7.64921L8.61268 7.64923L7.96046 8.40725ZM6.36152 4.47437V3.47437H7.36152V4.47437H6.36152ZM12.1143 11.8875V10.8875H12.7833L13.0386 11.5059L12.1143 11.8875ZM7.23951 11.8875L6.31518 11.5059L6.57049 10.8875H7.23951V11.8875ZM7.02574 13.8368V14.8368H6.02574V13.8368H7.02574ZM12.3281 13.8368H13.3281V14.8368H12.3281V13.8368ZM7.58034 11.2661V12.2661H5.67629L6.75705 10.6985L7.58034 11.2661ZM11.7735 11.2661L12.5968 10.6985L13.6775 12.2661H11.7735V11.2661ZM9.67692 9.50685L9.2169 8.61894L9.67692 8.38061L10.1369 8.61894L9.67692 9.50685ZM9.6769 8.80437L10.1369 9.69227L9.6769 9.93062L9.21687 9.69228L9.6769 8.80437ZM12.3281 4.9144L11.6221 4.20621L13.3281 2.50543V4.9144H12.3281ZM11.5234 5.71658L10.8174 5.00839L10.8174 5.00838L11.5234 5.71658ZM7.79584 5.71658L7.08982 6.42477L7.08982 6.42476L7.79584 5.71658ZM7.02574 4.94883H6.02574V2.53984L7.73176 4.24064L7.02574 4.94883ZM7.02574 5.36331H8.02574V5.36332L7.02574 5.36331ZM7.4607 4.47437L6.75468 5.18256L5.04123 3.47437H7.4607V4.47437ZM8.28017 5.29133L8.98619 4.58314L8.9862 4.58315L8.28017 5.29133ZM9.65963 5.85007L9.65967 6.85007H9.65963V5.85007ZM11.0391 5.29133L10.3331 4.58313L10.3331 4.58313L11.0391 5.29133ZM11.8586 4.47437V3.47437H14.2781L12.5646 5.18257L11.8586 4.47437ZM13.4632 4.47437V5.47437H12.9923V4.47437V3.47437H13.4632V4.47437ZM12.9923 4.47437H13.9923V5.36329H12.9923H11.9923V4.47437H12.9923ZM12.9923 5.36329H13.9923C13.9923 6.81982 13.2495 8.12948 12.0456 9.16529L11.3934 8.40725L10.7412 7.64921C11.6293 6.88502 11.9923 6.0895 11.9923 5.36329H12.9923ZM11.3934 8.40725L12.0456 9.16528C11.6108 9.5394 11.1821 9.82266 10.8553 10.0161L10.3459 9.15561L9.83645 8.29508C10.0903 8.1448 10.4171 7.92807 10.7411 7.64923L11.3934 8.40725ZM10.3459 9.15561L10.8553 8.29508C11.1821 8.48856 11.6108 8.77182 12.0456 9.14594L11.3934 9.90396L10.7411 10.662C10.4171 10.3831 10.0903 10.1664 9.83645 10.0161L10.3459 9.15561ZM11.3934 9.90396L12.0456 9.14592C13.2495 10.1817 13.9923 11.4914 13.9923 12.9479H12.9923H11.9923C11.9923 12.2217 11.6293 11.4262 10.7412 10.662L11.3934 9.90396ZM12.9923 12.9479H13.9923V13.8368H12.9923H11.9923V12.9479H12.9923ZM12.9923 13.8368V12.8368H13.4632V13.8368V14.8368H12.9923V13.8368ZM13.4632 13.8368V12.8368C14.1351 12.8368 14.7953 13.362 14.7953 14.1476H13.7953H12.7953C12.7953 14.59 13.1582 14.8368 13.4632 14.8368V13.8368ZM13.7953 14.1476H14.7953C14.7953 14.9331 14.1351 15.4583 13.4632 15.4583V14.4583V13.4583C13.1582 13.4583 12.7953 13.7051 12.7953 14.1476H13.7953ZM13.4632 14.4583V15.4583C13.4628 15.4583 13.4624 15.4583 13.4619 15.4583C13.4614 15.4583 13.4609 15.4583 13.4604 15.4583C13.4598 15.4583 13.4593 15.4583 13.4587 15.4583C13.4581 15.4583 13.4575 15.4583 13.4569 15.4583C13.4562 15.4583 13.4556 15.4583 13.4549 15.4583C13.4542 15.4583 13.4535 15.4583 13.4527 15.4583C13.452 15.4583 13.4512 15.4583 13.4505 15.4583C13.4497 15.4583 13.4489 15.4583 13.448 15.4583C13.4472 15.4583 13.4463 15.4583 13.4454 15.4583C13.4446 15.4583 13.4436 15.4583 13.4427 15.4583C13.4418 15.4583 13.4408 15.4583 13.4398 15.4583C13.4388 15.4583 13.4378 15.4583 13.4368 15.4583C13.4358 15.4583 13.4347 15.4583 13.4336 15.4583C13.4325 15.4583 13.4314 15.4583 13.4303 15.4583C13.4291 15.4583 13.428 15.4583 13.4268 15.4583C13.4256 15.4583 13.4244 15.4583 13.4232 15.4583C13.4219 15.4583 13.4207 15.4583 13.4194 15.4583C13.4181 15.4583 13.4168 15.4583 13.4155 15.4583C13.4142 15.4583 13.4128 15.4583 13.4114 15.4583C13.41 15.4583 13.4086 15.4583 13.4072 15.4583C13.4058 15.4583 13.4043 15.4583 13.4029 15.4583C13.4014 15.4583 13.3999 15.4583 13.3984 15.4583C13.3969 15.4583 13.3953 15.4583 13.3937 15.4583C13.3922 15.4583 13.3906 15.4583 13.389 15.4583C13.3874 15.4583 13.3857 15.4583 13.3841 15.4583C13.3824 15.4583 13.3807 15.4583 13.379 15.4583C13.3773 15.4583 13.3756 15.4583 13.3738 15.4583C13.372 15.4583 13.3703 15.4583 13.3685 15.4583C13.3667 15.4583 13.3648 15.4583 13.363 15.4583C13.3611 15.4583 13.3593 15.4583 13.3574 15.4583C13.3555 15.4583 13.3536 15.4583 13.3516 15.4583C13.3497 15.4583 13.3477 15.4583 13.3457 15.4583C13.3438 15.4583 13.3418 15.4583 13.3397 15.4583C13.3377 15.4583 13.3356 15.4583 13.3336 15.4583C13.3315 15.4583 13.3294 15.4583 13.3273 15.4583C13.3251 15.4583 13.323 15.4583 13.3208 15.4583C13.3187 15.4583 13.3165 15.4583 13.3143 15.4583C13.3121 15.4583 13.3098 15.4583 13.3076 15.4583C13.3053 15.4583 13.303 15.4583 13.3007 15.4583C13.2985 15.4583 13.2961 15.4583 13.2938 15.4583C13.2914 15.4583 13.2891 15.4583 13.2867 15.4583C13.2843 15.4583 13.2819 15.4583 13.2795 15.4583C13.277 15.4583 13.2746 15.4583 13.2721 15.4583C13.2696 15.4583 13.2672 15.4583 13.2646 15.4583C13.2621 15.4583 13.2596 15.4583 13.257 15.4583C13.2545 15.4583 13.2519 15.4583 13.2493 15.4583C13.2467 15.4583 13.2441 15.4583 13.2414 15.4583C13.2388 15.4583 13.2361 15.4583 13.2334 15.4583C13.2307 15.4583 13.228 15.4583 13.2253 15.4583C13.2226 15.4583 13.2198 15.4583 13.217 15.4583C13.2143 15.4583 13.2115 15.4583 13.2087 15.4583C13.2059 15.4583 13.203 15.4583 13.2002 15.4583C13.1973 15.4583 13.1944 15.4583 13.1915 15.4583C13.1887 15.4583 13.1857 15.4583 13.1828 15.4583C13.1799 15.4583 13.1769 15.4583 13.1739 15.4583C13.171 15.4583 13.168 15.4583 13.1649 15.4583C13.1619 15.4583 13.1589 15.4583 13.1558 15.4583C13.1528 15.4583 13.1497 15.4583 13.1466 15.4583C13.1435 15.4583 13.1404 15.4583 13.1372 15.4583C13.1341 15.4583 13.1309 15.4583 13.1278 15.4583C13.1246 15.4583 13.1214 15.4583 13.1182 15.4583C13.1149 15.4583 13.1117 15.4583 13.1084 15.4583C13.1052 15.4583 13.1019 15.4583 13.0986 15.4583C13.0953 15.4583 13.092 15.4583 13.0887 15.4583C13.0853 15.4583 13.082 15.4583 13.0786 15.4583C13.0752 15.4583 13.0718 15.4583 13.0684 15.4583C13.065 15.4583 13.0616 15.4583 13.0581 15.4583C13.0547 15.4583 13.0512 15.4583 13.0477 15.4583C13.0442 15.4583 13.0407 15.4583 13.0372 15.4583C13.0336 15.4583 13.0301 15.4583 13.0265 15.4583C13.023 15.4583 13.0194 15.4583 13.0158 15.4583C13.0122 15.4583 13.0085 15.4583 13.0049 15.4583C13.0013 15.4583 12.9976 15.4583 12.9939 15.4583C12.9902 15.4583 12.9865 15.4583 12.9828 15.4583C12.9791 15.4583 12.9754 15.4583 12.9716 15.4583C12.9679 15.4583 12.9641 15.4583 12.9603 15.4583C12.9565 15.4583 12.9527 15.4583 12.9489 15.4583C12.945 15.4583 12.9412 15.4583 12.9373 15.4583C12.9335 15.4583 12.9296 15.4583 12.9257 15.4583C12.9218 15.4583 12.9179 15.4583 12.9139 15.4583C12.91 15.4583 12.9061 15.4583 12.9021 15.4583C12.8981 15.4583 12.8941 15.4583 12.8901 15.4583C12.8861 15.4583 12.8821 15.4583 12.8781 15.4583C12.874 15.4583 12.87 15.4583 12.8659 15.4583C12.8618 15.4583 12.8577 15.4583 12.8536 15.4583C12.8495 15.4583 12.8454 15.4583 12.8412 15.4583C12.8371 15.4583 12.8329 15.4583 12.8287 15.4583C12.8245 15.4583 12.8203 15.4583 12.8161 15.4583C12.8119 15.4583 12.8077 15.4583 12.8034 15.4583C12.7992 15.4583 12.7949 15.4583 12.7906 15.4583C12.7863 15.4583 12.782 15.4583 12.7777 15.4583C12.7734 15.4583 12.7691 15.4583 12.7647 15.4583C12.7604 15.4583 12.756 15.4583 12.7516 15.4583C12.7472 15.4583 12.7428 15.4583 12.7384 15.4583C12.734 15.4583 12.7296 15.4583 12.7251 15.4583C12.7207 15.4583 12.7162 15.4583 12.7117 15.4583C12.7073 15.4583 12.7028 15.4583 12.6982 15.4583C12.6937 15.4583 12.6892 15.4583 12.6847 15.4583C12.6801 15.4583 12.6755 15.4583 12.671 15.4583C12.6664 15.4583 12.6618 15.4583 12.6572 15.4583C12.6526 15.4583 12.6479 15.4583 12.6433 15.4583C12.6387 15.4583 12.634 15.4583 12.6293 15.4583C12.6247 15.4583 12.62 15.4583 12.6153 15.4583C12.6106 15.4583 12.6058 15.4583 12.6011 15.4583C12.5964 15.4583 12.5916 15.4583 12.5868 15.4583C12.5821 15.4583 12.5773 15.4583 12.5725 15.4583C12.5677 15.4583 12.5629 15.4583 12.5581 15.4583C12.5532 15.4583 12.5484 15.4583 12.5435 15.4583C12.5387 15.4583 12.5338 15.4583 12.5289 15.4583C12.524 15.4583 12.5191 15.4583 12.5142 15.4583C12.5093 15.4583 12.5043 15.4583 12.4994 15.4583C12.4944 15.4583 12.4895 15.4583 12.4845 15.4583C12.4795 15.4583 12.4745 15.4583 12.4695 15.4583C12.4645 15.4583 12.4595 15.4583 12.4545 15.4583C12.4494 15.4583 12.4444 15.4583 12.4393 15.4583C12.4342 15.4583 12.4292 15.4583 12.4241 15.4583C12.419 15.4583 12.4139 15.4583 12.4088 15.4583C12.4036 15.4583 12.3985 15.4583 12.3933 15.4583C12.3882 15.4583 12.383 15.4583 12.3779 15.4583C12.3727 15.4583 12.3675 15.4583 12.3623 15.4583C12.3571 15.4583 12.3518 15.4583 12.3466 15.4583C12.3414 15.4583 12.3361 15.4583 12.3309 15.4583C12.3256 15.4583 12.3203 15.4583 12.315 15.4583C12.3098 15.4583 12.3045 15.4583 12.2991 15.4583C12.2938 15.4583 12.2885 15.4583 12.2832 15.4583C12.2778 15.4583 12.2725 15.4583 12.2671 15.4583C12.2617 15.4583 12.2563 15.4583 12.2509 15.4583C12.2455 15.4583 12.2401 15.4583 12.2347 15.4583C12.2293 15.4583 12.2239 15.4583 12.2184 15.4583C12.213 15.4583 12.2075 15.4583 12.202 15.4583C12.1966 15.4583 12.1911 15.4583 12.1856 15.4583C12.1801 15.4583 12.1746 15.4583 12.169 15.4583C12.1635 15.4583 12.158 15.4583 12.1524 15.4583C12.1469 15.4583 12.1413 15.4583 12.1357 15.4583C12.1302 15.4583 12.1246 15.4583 12.119 15.4583C12.1134 15.4583 12.1078 15.4583 12.1021 15.4583C12.0965 15.4583 12.0909 15.4583 12.0852 15.4583C12.0796 15.4583 12.0739 15.4583 12.0682 15.4583C12.0626 15.4583 12.0569 15.4583 12.0512 15.4583C12.0455 15.4583 12.0398 15.4583 12.0341 15.4583C12.0283 15.4583 12.0226 15.4583 12.0169 15.4583C12.0111 15.4583 12.0054 15.4583 11.9996 15.4583C11.9938 15.4583 11.988 15.4583 11.9822 15.4583C11.9765 15.4583 11.9707 15.4583 11.9648 15.4583C11.959 15.4583 11.9532 15.4583 11.9474 15.4583C11.9415 15.4583 11.9357 15.4583 11.9298 15.4583C11.924 15.4583 11.9181 15.4583 11.9122 15.4583C11.9063 15.4583 11.9004 15.4583 11.8945 15.4583C11.8886 15.4583 11.8827 15.4583 11.8768 15.4583C11.8708 15.4583 11.8649 15.4583 11.859 15.4583C11.853 15.4583 11.847 15.4583 11.8411 15.4583C11.8351 15.4583 11.8291 15.4583 11.8231 15.4583C11.8171 15.4583 11.8111 15.4583 11.8051 15.4583C11.7991 15.4583 11.7931 15.4583 11.7871 15.4583C11.781 15.4583 11.775 15.4583 11.7689 15.4583C11.7629 15.4583 11.7568 15.4583 11.7507 15.4583C11.7447 15.4583 11.7386 15.4583 11.7325 15.4583C11.7264 15.4583 11.7203 15.4583 11.7142 15.4583C11.708 15.4583 11.7019 15.4583 11.6958 15.4583C11.6896 15.4583 11.6835 15.4583 11.6773 15.4583C11.6712 15.4583 11.665 15.4583 11.6588 15.4583C11.6527 15.4583 11.6465 15.4583 11.6403 15.4583C11.6341 15.4583 11.6279 15.4583 11.6217 15.4583C11.6155 15.4583 11.6092 15.4583 11.603 15.4583C11.5968 15.4583 11.5905 15.4583 11.5843 15.4583C11.578 15.4583 11.5718 15.4583 11.5655 15.4583C11.5592 15.4583 11.5529 15.4583 11.5466 15.4583C11.5404 15.4583 11.5341 15.4583 11.5278 15.4583C11.5214 15.4583 11.5151 15.4583 11.5088 15.4583C11.5025 15.4583 11.4961 15.4583 11.4898 15.4583C11.4835 15.4583 11.4771 15.4583 11.4707 15.4583C11.4644 15.4583 11.458 15.4583 11.4516 15.4583C11.4453 15.4583 11.4389 15.4583 11.4325 15.4583C11.4261 15.4583 11.4197 15.4583 11.4133 15.4583C11.4069 15.4583 11.4004 15.4583 11.394 15.4583C11.3876 15.4583 11.3811 15.4583 11.3747 15.4583C11.3682 15.4583 11.3618 15.4583 11.3553 15.4583C11.3489 15.4583 11.3424 15.4583 11.3359 15.4583C11.3294 15.4583 11.323 15.4583 11.3165 15.4583C11.31 15.4583 11.3035 15.4583 11.2969 15.4583C11.2904 15.4583 11.2839 15.4583 11.2774 15.4583C11.2709 15.4583 11.2643 15.4583 11.2578 15.4583C11.2513 15.4583 11.2447 15.4583 11.2381 15.4583C11.2316 15.4583 11.225 15.4583 11.2185 15.4583C11.2119 15.4583 11.2053 15.4583 11.1987 15.4583C11.1921 15.4583 11.1855 15.4583 11.1789 15.4583C11.1723 15.4583 11.1657 15.4583 11.1591 15.4583C11.1525 15.4583 11.1459 15.4583 11.1392 15.4583C11.1326 15.4583 11.126 15.4583 11.1193 15.4583C11.1127 15.4583 11.106 15.4583 11.0994 15.4583C11.0927 15.4583 11.0861 15.4583 11.0794 15.4583C11.0727 15.4583 11.066 15.4583 11.0594 15.4583C11.0527 15.4583 11.046 15.4583 11.0393 15.4583C11.0326 15.4583 11.0259 15.4583 11.0192 15.4583C11.0125 15.4583 11.0057 15.4583 10.999 15.4583C10.9923 15.4583 10.9856 15.4583 10.9788 15.4583C10.9721 15.4583 10.9654 15.4583 10.9586 15.4583C10.9519 15.4583 10.9451 15.4583 10.9383 15.4583C10.9316 15.4583 10.9248 15.4583 10.918 15.4583C10.9113 15.4583 10.9045 15.4583 10.8977 15.4583C10.8909 15.4583 10.8841 15.4583 10.8773 15.4583C10.8705 15.4583 10.8637 15.4583 10.8569 15.4583C10.8501 15.4583 10.8433 15.4583 10.8365 15.4583C10.8297 15.4583 10.8229 15.4583 10.816 15.4583C10.8092 15.4583 10.8024 15.4583 10.7955 15.4583C10.7887 15.4583 10.7818 15.4583 10.775 15.4583C10.7681 15.4583 10.7613 15.4583 10.7544 15.4583C10.7476 15.4583 10.7407 15.4583 10.7338 15.4583C10.7269 15.4583 10.7201 15.4583 10.7132 15.4583C10.7063 15.4583 10.6994 15.4583 10.6925 15.4583C10.6856 15.4583 10.6787 15.4583 10.6718 15.4583C10.6649 15.4583 10.658 15.4583 10.6511 15.4583C10.6442 15.4583 10.6373 15.4583 10.6304 15.4583C10.6235 15.4583 10.6165 15.4583 10.6096 15.4583C10.6027 15.4583 10.5957 15.4583 10.5888 15.4583C10.5819 15.4583 10.5749 15.4583 10.568 15.4583C10.561 15.4583 10.5541 15.4583 10.5471 15.4583C10.5402 15.4583 10.5332 15.4583 10.5263 15.4583C10.5193 15.4583 10.5123 15.4583 10.5054 15.4583C10.4984 15.4583 10.4914 15.4583 10.4844 15.4583C10.4775 15.4583 10.4705 15.4583 10.4635 15.4583C10.4565 15.4583 10.4495 15.4583 10.4425 15.4583C10.4355 15.4583 10.4285 15.4583 10.4215 15.4583C10.4145 15.4583 10.4075 15.4583 10.4005 15.4583C10.3935 15.4583 10.3865 15.4583 10.3795 15.4583C10.3725 15.4583 10.3655 15.4583 10.3584 15.4583C10.3514 15.4583 10.3444 15.4583 10.3374 15.4583C10.3303 15.4583 10.3233 15.4583 10.3163 15.4583C10.3092 15.4583 10.3022 15.4583 10.2952 15.4583C10.2881 15.4583 10.2811 15.4583 10.274 15.4583C10.267 15.4583 10.26 15.4583 10.2529 15.4583C10.2459 15.4583 10.2388 15.4583 10.2317 15.4583C10.2247 15.4583 10.2176 15.4583 10.2106 15.4583C10.2035 15.4583 10.1964 15.4583 10.1894 15.4583C10.1823 15.4583 10.1752 15.4583 10.1682 15.4583C10.1611 15.4583 10.154 15.4583 10.147 15.4583C10.1399 15.4583 10.1328 15.4583 10.1257 15.4583C10.1186 15.4583 10.1116 15.4583 10.1045 15.4583C10.0974 15.4583 10.0903 15.4583 10.0832 15.4583C10.0761 15.4583 10.069 15.4583 10.062 15.4583C10.0549 15.4583 10.0478 15.4583 10.0407 15.4583C10.0336 15.4583 10.0265 15.4583 10.0194 15.4583C10.0123 15.4583 10.0052 15.4583 9.99808 15.4583C9.99098 15.4583 9.98388 15.4583 9.97677 15.4583C9.96967 15.4583 9.96256 15.4583 9.95546 15.4583C9.94835 15.4583 9.94124 15.4583 9.93413 15.4583C9.92702 15.4583 9.91991 15.4583 9.9128 15.4583C9.90569 15.4583 9.89857 15.4583 9.89146 15.4583C9.88434 15.4583 9.87723 15.4583 9.87011 15.4583C9.863 15.4583 9.85588 15.4583 9.84876 15.4583C9.84164 15.4583 9.83453 15.4583 9.82741 15.4583C9.82029 15.4583 9.81317 15.4583 9.80605 15.4583C9.79892 15.4583 9.7918 15.4583 9.78468 15.4583C9.77756 15.4583 9.77044 15.4583 9.76331 15.4583C9.75619 15.4583 9.74907 15.4583 9.74194 15.4583C9.73482 15.4583 9.7277 15.4583 9.72057 15.4583C9.71345 15.4583 9.70632 15.4583 9.6992 15.4583C9.69207 15.4583 9.68495 15.4583 9.67783 15.4583C9.6707 15.4583 9.66358 15.4583 9.65645 15.4583C9.64933 15.4583 9.6422 15.4583 9.63508 15.4583C9.62795 15.4583 9.62083 15.4583 9.61371 15.4583C9.60658 15.4583 9.59946 15.4583 9.59233 15.4583C9.58521 15.4583 9.57809 15.4583 9.57097 15.4583C9.56384 15.4583 9.55672 15.4583 9.5496 15.4583C9.54248 15.4583 9.53536 15.4583 9.52824 15.4583C9.52112 15.4583 9.514 15.4583 9.50688 15.4583C9.49976 15.4583 9.49264 15.4583 9.48553 15.4583C9.47841 15.4583 9.47129 15.4583 9.46418 15.4583C9.45706 15.4583 9.44995 15.4583 9.44284 15.4583C9.43572 15.4583 9.42861 15.4583 9.4215 15.4583C9.41439 15.4583 9.40728 15.4583 9.40017 15.4583C9.39306 15.4583 9.38596 15.4583 9.37885 15.4583C9.37175 15.4583 9.36464 15.4583 9.35754 15.4583C9.35044 15.4583 9.34334 15.4583 9.33624 15.4583C9.32914 15.4583 9.32204 15.4583 9.31494 15.4583C9.30785 15.4583 9.30075 15.4583 9.29366 15.4583C9.28657 15.4583 9.27947 15.4583 9.27238 15.4583C9.2653 15.4583 9.25821 15.4583 9.25112 15.4583C9.24404 15.4583 9.23695 15.4583 9.22987 15.4583C9.22279 15.4583 9.21571 15.4583 9.20864 15.4583C9.20156 15.4583 9.19448 15.4583 9.18741 15.4583C9.18034 15.4583 9.17327 15.4583 9.1662 15.4583C9.15913 15.4583 9.15207 15.4583 9.145 15.4583C9.13794 15.4583 9.13088 15.4583 9.12382 15.4583C9.11677 15.4583 9.10971 15.4583 9.10266 15.4583C9.0956 15.4583 9.08855 15.4583 9.08151 15.4583C9.07446 15.4583 9.06742 15.4583 9.06037 15.4583C9.05333 15.4583 9.04629 15.4583 9.03926 15.4583C9.03222 15.4583 9.02519 15.4583 9.01816 15.4583C9.01113 15.4583 9.00411 15.4583 8.99708 15.4583C8.99006 15.4583 8.98304 15.4583 8.97602 15.4583C8.96901 15.4583 8.96199 15.4583 8.95498 15.4583C8.94797 15.4583 8.94097 15.4583 8.93396 15.4583C8.92696 15.4583 8.91996 15.4583 8.91296 15.4583C8.90597 15.4583 8.89898 15.4583 8.89199 15.4583C8.885 15.4583 8.87801 15.4583 8.87103 15.4583C8.86405 15.4583 8.85707 15.4583 8.8501 15.4583C8.84313 15.4583 8.83616 15.4583 8.82919 15.4583C8.82222 15.4583 8.81526 15.4583 8.80831 15.4583C8.80135 15.4583 8.79439 15.4583 8.78745 15.4583C8.7805 15.4583 8.77355 15.4583 8.76661 15.4583C8.75967 15.4583 8.75273 15.4583 8.7458 15.4583C8.73887 15.4583 8.73194 15.4583 8.72502 15.4583C8.71809 15.4583 8.71118 15.4583 8.70426 15.4583C8.69735 15.4583 8.69044 15.4583 8.68353 15.4583C8.67663 15.4583 8.66973 15.4583 8.66283 15.4583C8.65594 15.4583 8.64905 15.4583 8.64216 15.4583C8.63528 15.4583 8.6284 15.4583 8.62152 15.4583C8.61464 15.4583 8.60777 15.4583 8.60091 15.4583C8.59404 15.4583 8.58718 15.4583 8.58033 15.4583C8.57347 15.4583 8.56662 15.4583 8.55978 15.4583C8.55293 15.4583 8.54609 15.4583 8.53926 15.4583C8.53242 15.4583 8.52559 15.4583 8.51877 15.4583C8.51195 15.4583 8.50513 15.4583 8.49832 15.4583C8.49151 15.4583 8.4847 15.4583 8.4779 15.4583C8.4711 15.4583 8.4643 15.4583 8.45751 15.4583C8.45072 15.4583 8.44394 15.4583 8.43716 15.4583C8.43039 15.4583 8.42361 15.4583 8.41685 15.4583C8.41008 15.4583 8.40332 15.4583 8.39657 15.4583C8.38982 15.4583 8.38307 15.4583 8.37633 15.4583C8.36959 15.4583 8.36285 15.4583 8.35612 15.4583C8.34939 15.4583 8.34267 15.4583 8.33596 15.4583C8.32924 15.4583 8.32253 15.4583 8.31583 15.4583C8.30912 15.4583 8.30243 15.4583 8.29574 15.4583C8.28905 15.4583 8.28236 15.4583 8.27569 15.4583C8.26901 15.4583 8.26234 15.4583 8.25568 15.4583C8.24901 15.4583 8.24236 15.4583 8.23571 15.4583C8.22906 15.4583 8.22242 15.4583 8.21578 15.4583C8.20915 15.4583 8.20252 15.4583 8.1959 15.4583C8.18927 15.4583 8.18266 15.4583 8.17605 15.4583C8.16945 15.4583 8.16285 15.4583 8.15625 15.4583C8.14966 15.4583 8.14308 15.4583 8.1365 15.4583C8.12992 15.4583 8.12335 15.4583 8.11679 15.4583C8.11022 15.4583 8.10367 15.4583 8.09712 15.4583C8.09057 15.4583 8.08403 15.4583 8.0775 15.4583C8.07097 15.4583 8.06444 15.4583 8.05793 15.4583C8.05141 15.4583 8.0449 15.4583 8.0384 15.4583C8.0319 15.4583 8.0254 15.4583 8.01892 15.4583C8.01243 15.4583 8.00596 15.4583 7.99949 15.4583C7.99302 15.4583 7.98656 15.4583 7.9801 15.4583C7.97365 15.4583 7.96721 15.4583 7.96077 15.4583C7.95433 15.4583 7.94791 15.4583 7.94149 15.4583C7.93507 15.4583 7.92866 15.4583 7.92225 15.4583C7.91585 15.4583 7.90946 15.4583 7.90307 15.4583C7.89669 15.4583 7.89031 15.4583 7.88394 15.4583C7.87757 15.4583 7.87121 15.4583 7.86486 15.4583C7.85851 15.4583 7.85217 15.4583 7.84584 15.4583C7.8395 15.4583 7.83318 15.4583 7.82687 15.4583C7.82055 15.4583 7.81424 15.4583 7.80795 15.4583C7.80165 15.4583 7.79536 15.4583 7.78908 15.4583C7.78281 15.4583 7.77654 15.4583 7.77028 15.4583C7.76402 15.4583 7.75777 15.4583 7.75153 15.4583C7.74528 15.4583 7.73905 15.4583 7.73283 15.4583C7.72661 15.4583 7.7204 15.4583 7.71419 15.4583C7.70799 15.4583 7.7018 15.4583 7.69561 15.4583C7.68943 15.4583 7.68325 15.4583 7.67709 15.4583C7.67093 15.4583 7.66477 15.4583 7.65863 15.4583C7.65248 15.4583 7.64635 15.4583 7.64022 15.4583C7.6341 15.4583 7.62798 15.4583 7.62188 15.4583C7.61578 15.4583 7.60968 15.4583 7.6036 15.4583C7.59751 15.4583 7.59144 15.4583 7.58538 15.4583C7.57931 15.4583 7.57326 15.4583 7.56722 15.4583C7.56117 15.4583 7.55514 15.4583 7.54912 15.4583C7.5431 15.4583 7.53709 15.4583 7.53109 15.4583C7.52508 15.4583 7.51909 15.4583 7.51312 15.4583C7.50714 15.4583 7.50117 15.4583 7.49521 15.4583C7.48925 15.4583 7.4833 15.4583 7.47737 15.4583C7.47143 15.4583 7.46551 15.4583 7.45959 15.4583C7.45368 15.4583 7.44778 15.4583 7.44188 15.4583C7.43599 15.4583 7.43011 15.4583 7.42424 15.4583C7.41837 15.4583 7.41251 15.4583 7.40667 15.4583C7.40082 15.4583 7.39498 15.4583 7.38916 15.4583C7.38334 15.4583 7.37752 15.4583 7.37172 15.4583C7.36592 15.4583 7.36013 15.4583 7.35435 15.4583C7.34858 15.4583 7.34281 15.4583 7.33705 15.4583C7.3313 15.4583 7.32556 15.4583 7.31982 15.4583C7.31409 15.4583 7.30837 15.4583 7.30267 15.4583C7.29696 15.4583 7.29126 15.4583 7.28558 15.4583C7.2799 15.4583 7.27423 15.4583 7.26857 15.4583C7.26291 15.4583 7.25726 15.4583 7.25162 15.4583C7.24599 15.4583 7.24037 15.4583 7.23476 15.4583C7.22915 15.4583 7.22355 15.4583 7.21796 15.4583C7.21238 15.4583 7.2068 15.4583 7.20124 15.4583C7.19568 15.4583 7.19013 15.4583 7.1846 15.4583C7.17906 15.4583 7.17354 15.4583 7.16803 15.4583C7.16252 15.4583 7.15702 15.4583 7.15154 15.4583C7.14605 15.4583 7.14058 15.4583 7.13512 15.4583C7.12966 15.4583 7.12422 15.4583 7.11879 15.4583C7.11335 15.4583 7.10793 15.4583 7.10253 15.4583C7.09712 15.4583 7.09173 15.4583 7.08635 15.4583C7.08097 15.4583 7.0756 15.4583 7.07025 15.4583C7.06489 15.4583 7.05955 15.4583 7.05422 15.4583C7.0489 15.4583 7.04358 15.4583 7.03828 15.4583C7.03298 15.4583 7.0277 15.4583 7.02242 15.4583C7.01715 15.4583 7.01189 15.4583 7.00665 15.4583C7.0014 15.4583 6.99617 15.4583 6.99095 15.4583C6.98574 15.4583 6.98053 15.4583 6.97534 15.4583C6.97015 15.4583 6.96497 15.4583 6.95981 15.4583C6.95465 15.4583 6.9495 15.4583 6.94437 15.4583C6.93924 15.4583 6.93412 15.4583 6.92901 15.4583C6.9239 15.4583 6.91881 15.4583 6.91374 15.4583C6.90866 15.4583 6.9036 15.4583 6.89855 15.4583C6.8935 15.4583 6.88847 15.4583 6.88345 15.4583C6.87843 15.4583 6.87342 15.4583 6.86843 15.4583C6.86344 15.4583 6.85847 15.4583 6.85351 15.4583C6.84855 15.4583 6.8436 15.4583 6.83867 15.4583C6.83374 15.4583 6.82882 15.4583 6.82392 15.4583C6.81902 15.4583 6.81413 15.4583 6.80926 15.4583C6.80439 15.4583 6.79954 15.4583 6.7947 15.4583C6.78985 15.4583 6.78503 15.4583 6.78022 15.4583C6.77541 15.4583 6.77061 15.4583 6.76583 15.4583C6.76105 15.4583 6.75629 15.4583 6.75154 15.4583C6.74679 15.4583 6.74205 15.4583 6.73734 15.4583C6.73262 15.4583 6.72792 15.4583 6.72323 15.4583C6.71854 15.4583 6.71387 15.4583 6.70921 15.4583C6.70456 15.4583 6.69992 15.4583 6.69529 15.4583C6.69067 15.4583 6.68606 15.4583 6.68147 15.4583C6.67688 15.4583 6.6723 15.4583 6.66774 15.4583C6.66318 15.4583 6.65864 15.4583 6.65411 15.4583C6.64958 15.4583 6.64507 15.4583 6.64057 15.4583C6.63608 15.4583 6.6316 15.4583 6.62713 15.4583C6.62267 15.4583 6.61822 15.4583 6.61379 15.4583C6.60936 15.4583 6.60495 15.4583 6.60055 15.4583C6.59616 15.4583 6.59178 15.4583 6.58741 15.4583C6.58305 15.4583 6.5787 15.4583 6.57437 15.4583C6.57004 15.4583 6.56572 15.4583 6.56143 15.4583C6.55713 15.4583 6.55285 15.4583 6.54859 15.4583C6.54432 15.4583 6.54008 15.4583 6.53585 15.4583C6.53162 15.4583 6.52741 15.4583 6.52321 15.4583C6.51902 15.4583 6.51484 15.4583 6.51068 15.4583C6.50652 15.4583 6.50237 15.4583 6.49825 15.4583C6.49412 15.4583 6.49001 15.4583 6.48592 15.4583C6.48183 15.4583 6.47776 15.4583 6.4737 15.4583C6.46964 15.4583 6.46561 15.4583 6.46158 15.4583C6.45756 15.4583 6.45356 15.4583 6.44957 15.4583C6.44559 15.4583 6.44162 15.4583 6.43767 15.4583C6.43372 15.4583 6.42979 15.4583 6.42587 15.4583C6.42196 15.4583 6.41806 15.4583 6.41419 15.4583C6.41031 15.4583 6.40645 15.4583 6.40261 15.4583C6.39876 15.4583 6.39494 15.4583 6.39113 15.4583C6.38733 15.4583 6.38354 15.4583 6.37977 15.4583C6.376 15.4583 6.37225 15.4583 6.36852 15.4583C6.36479 15.4583 6.36108 15.4583 6.35738 15.4583C6.35369 15.4583 6.35001 15.4583 6.34635 15.4583C6.34269 15.4583 6.33905 15.4583 6.33543 15.4583C6.33181 15.4583 6.32821 15.4583 6.32463 15.4583C6.32105 15.4583 6.31748 15.4583 6.31394 15.4583C6.31039 15.4583 6.30687 15.4583 6.30336 15.4583C6.29985 15.4583 6.29636 15.4583 6.2929 15.4583C6.28943 15.4583 6.28598 15.4583 6.28255 15.4583C6.27912 15.4583 6.27571 15.4583 6.27231 15.4583C6.26892 15.4583 6.26555 15.4583 6.2622 15.4583C6.25884 15.4583 6.25551 15.4583 6.2522 15.4583C6.24888 15.4583 6.24559 15.4583 6.24231 15.4583C6.23904 15.4583 6.23578 15.4583 6.23255 15.4583C6.22931 15.4583 6.2261 15.4583 6.2229 15.4583C6.21971 15.4583 6.21653 15.4583 6.21338 15.4583C6.21022 15.4583 6.20709 15.4583 6.20397 15.4583C6.20085 15.4583 6.19776 15.4583 6.19468 15.4583C6.19161 15.4583 6.18855 15.4583 6.18552 15.4583C6.18248 15.4583 6.17947 15.4583 6.17647 15.4583C6.17348 15.4583 6.1705 15.4583 6.16755 15.4583C6.1646 15.4583 6.16166 15.4583 6.15875 15.4583C6.15584 15.4583 6.15295 15.4583 6.15008 15.4583C6.1472 15.4583 6.14435 15.4583 6.14152 15.4583C6.13869 15.4583 6.13589 15.4583 6.1331 15.4583C6.13031 15.4583 6.12754 15.4583 6.1248 15.4583C6.12205 15.4583 6.11932 15.4583 6.11662 15.4583C6.11392 15.4583 6.11123 15.4583 6.10857 15.4583C6.10591 15.4583 6.10327 15.4583 6.10065 15.4583C6.09803 15.4583 6.09543 15.4583 6.09285 15.4583C6.09028 15.4583 6.08772 15.4583 6.08519 15.4583C6.08265 15.4583 6.08014 15.4583 6.07765 15.4583C6.07516 15.4583 6.07269 15.4583 6.07024 15.4583C6.06779 15.4583 6.06536 15.4583 6.06296 15.4583C6.06056 15.4583 6.05817 15.4583 6.05581 15.4583C6.05345 15.4583 6.05111 15.4583 6.0488 15.4583C6.04648 15.4583 6.04418 15.4583 6.04191 15.4583C6.03964 15.4583 6.03739 15.4583 6.03516 15.4583C6.03293 15.4583 6.03072 15.4583 6.02854 15.4583C6.02636 15.4583 6.02419 15.4583 6.02205 15.4583C6.01991 15.4583 6.0178 15.4583 6.0157 15.4583C6.01361 15.4583 6.01154 15.4583 6.00949 15.4583C6.00744 15.4583 6.00541 15.4583 6.00341 15.4583C6.0014 15.4583 5.99942 15.4583 5.99746 15.4583C5.9955 15.4583 5.99357 15.4583 5.99165 15.4583C5.98974 15.4583 5.98785 15.4583 5.98598 15.4583C5.98411 15.4583 5.98227 15.4583 5.98045 15.4583C5.97863 15.4583 5.97683 15.4583 5.97505 15.4583C5.97328 15.4583 5.97153 15.4583 5.9698 15.4583C5.96807 15.4583 5.96636 15.4583 5.96468 15.4583C5.963 15.4583 5.96134 15.4583 5.95971 15.4583C5.95807 15.4583 5.95646 15.4583 5.95487 15.4583C5.95328 15.4583 5.95172 15.4583 5.95018 15.4583C5.94864 15.4583 5.94712 15.4583 5.94563 15.4583C5.94413 15.4583 5.94267 15.4583 5.94122 15.4583C5.93977 15.4583 5.93835 15.4583 5.93695 15.4583C5.93556 15.4583 5.93418 15.4583 5.93283 15.4583C5.93148 15.4583 5.93016 15.4583 5.92886 15.4583C5.92756 15.4583 5.92628 15.4583 5.92502 15.4583C5.92377 15.4583 5.92254 15.4583 5.92134 15.4583C5.92013 15.4583 5.91895 15.4583 5.9178 15.4583C5.91664 15.4583 5.91551 15.4583 5.91441 15.4583C5.9133 15.4583 5.91222 15.4583 5.91116 15.4583C5.9101 15.4583 5.90907 15.4583 5.90806 15.4583C5.90706 15.4583 5.90607 15.4583 5.90512 15.4583C5.90416 15.4583 5.90323 15.4583 5.90232 15.4583C5.90141 15.4583 5.90053 15.4583 5.89967 15.4583C5.89881 15.4583 5.89798 15.4583 5.89717 15.4583C5.89636 15.4583 5.89558 15.4583 5.89482 15.4583C5.89406 15.4583 5.89333 15.4583 5.89263 15.4583C5.89192 15.4583 5.89124 15.4583 5.89058 15.4583V14.4583V13.4583C5.89124 13.4583 5.89192 13.4583 5.89263 13.4583C5.89333 13.4583 5.89406 13.4583 5.89482 13.4583C5.89558 13.4583 5.89636 13.4583 5.89717 13.4583C5.89798 13.4583 5.89881 13.4583 5.89967 13.4583C5.90053 13.4583 5.90141 13.4583 5.90232 13.4583C5.90323 13.4583 5.90416 13.4583 5.90512 13.4583C5.90607 13.4583 5.90706 13.4583 5.90806 13.4583C5.90907 13.4583 5.9101 13.4583 5.91116 13.4583C5.91222 13.4583 5.9133 13.4583 5.91441 13.4583C5.91551 13.4583 5.91664 13.4583 5.9178 13.4583C5.91895 13.4583 5.92013 13.4583 5.92134 13.4583C5.92254 13.4583 5.92377 13.4583 5.92502 13.4583C5.92628 13.4583 5.92756 13.4583 5.92886 13.4583C5.93016 13.4583 5.93148 13.4583 5.93283 13.4583C5.93418 13.4583 5.93556 13.4583 5.93695 13.4583C5.93835 13.4583 5.93977 13.4583 5.94122 13.4583C5.94267 13.4583 5.94413 13.4583 5.94563 13.4583C5.94712 13.4583 5.94864 13.4583 5.95018 13.4583C5.95172 13.4583 5.95328 13.4583 5.95487 13.4583C5.95646 13.4583 5.95807 13.4583 5.95971 13.4583C5.96134 13.4583 5.963 13.4583 5.96468 13.4583C5.96636 13.4583 5.96807 13.4583 5.9698 13.4583C5.97153 13.4583 5.97328 13.4583 5.97505 13.4583C5.97683 13.4583 5.97863 13.4583 5.98045 13.4583C5.98227 13.4583 5.98411 13.4583 5.98598 13.4583C5.98785 13.4583 5.98974 13.4583 5.99165 13.4583C5.99357 13.4583 5.9955 13.4583 5.99746 13.4583C5.99942 13.4583 6.0014 13.4583 6.00341 13.4583C6.00541 13.4583 6.00744 13.4583 6.00949 13.4583C6.01154 13.4583 6.01361 13.4583 6.0157 13.4583C6.0178 13.4583 6.01991 13.4583 6.02205 13.4583C6.02419 13.4583 6.02636 13.4583 6.02854 13.4583C6.03072 13.4583 6.03293 13.4583 6.03516 13.4583C6.03739 13.4583 6.03964 13.4583 6.04191 13.4583C6.04418 13.4583 6.04648 13.4583 6.0488 13.4583C6.05111 13.4583 6.05345 13.4583 6.05581 13.4583C6.05817 13.4583 6.06056 13.4583 6.06296 13.4583C6.06536 13.4583 6.06779 13.4583 6.07024 13.4583C6.07269 13.4583 6.07516 13.4583 6.07765 13.4583C6.08014 13.4583 6.08265 13.4583 6.08519 13.4583C6.08772 13.4583 6.09028 13.4583 6.09285 13.4583C6.09543 13.4583 6.09803 13.4583 6.10065 13.4583C6.10327 13.4583 6.10591 13.4583 6.10857 13.4583C6.11123 13.4583 6.11392 13.4583 6.11662 13.4583C6.11932 13.4583 6.12205 13.4583 6.1248 13.4583C6.12754 13.4583 6.13031 13.4583 6.1331 13.4583C6.13589 13.4583 6.13869 13.4583 6.14152 13.4583C6.14435 13.4583 6.1472 13.4583 6.15008 13.4583C6.15295 13.4583 6.15584 13.4583 6.15875 13.4583C6.16166 13.4583 6.1646 13.4583 6.16755 13.4583C6.1705 13.4583 6.17348 13.4583 6.17647 13.4583C6.17947 13.4583 6.18248 13.4583 6.18552 13.4583C6.18855 13.4583 6.19161 13.4583 6.19468 13.4583C6.19776 13.4583 6.20085 13.4583 6.20397 13.4583C6.20709 13.4583 6.21022 13.4583 6.21338 13.4583C6.21653 13.4583 6.21971 13.4583 6.2229 13.4583C6.2261 13.4583 6.22931 13.4583 6.23255 13.4583C6.23578 13.4583 6.23904 13.4583 6.24231 13.4583C6.24559 13.4583 6.24888 13.4583 6.2522 13.4583C6.25551 13.4583 6.25884 13.4583 6.2622 13.4583C6.26555 13.4583 6.26892 13.4583 6.27231 13.4583C6.27571 13.4583 6.27912 13.4583 6.28255 13.4583C6.28598 13.4583 6.28943 13.4583 6.2929 13.4583C6.29636 13.4583 6.29985 13.4583 6.30336 13.4583C6.30687 13.4583 6.31039 13.4583 6.31394 13.4583C6.31748 13.4583 6.32105 13.4583 6.32463 13.4583C6.32821 13.4583 6.33181 13.4583 6.33543 13.4583C6.33905 13.4583 6.34269 13.4583 6.34635 13.4583C6.35001 13.4583 6.35369 13.4583 6.35738 13.4583C6.36108 13.4583 6.36479 13.4583 6.36852 13.4583C6.37225 13.4583 6.376 13.4583 6.37977 13.4583C6.38354 13.4583 6.38733 13.4583 6.39113 13.4583C6.39494 13.4583 6.39876 13.4583 6.40261 13.4583C6.40645 13.4583 6.41031 13.4583 6.41419 13.4583C6.41806 13.4583 6.42196 13.4583 6.42587 13.4583C6.42979 13.4583 6.43372 13.4583 6.43767 13.4583C6.44162 13.4583 6.44559 13.4583 6.44957 13.4583C6.45356 13.4583 6.45756 13.4583 6.46158 13.4583C6.46561 13.4583 6.46964 13.4583 6.4737 13.4583C6.47776 13.4583 6.48183 13.4583 6.48592 13.4583C6.49001 13.4583 6.49412 13.4583 6.49825 13.4583C6.50237 13.4583 6.50652 13.4583 6.51068 13.4583C6.51484 13.4583 6.51902 13.4583 6.52321 13.4583C6.52741 13.4583 6.53162 13.4583 6.53585 13.4583C6.54008 13.4583 6.54432 13.4583 6.54859 13.4583C6.55285 13.4583 6.55713 13.4583 6.56143 13.4583C6.56572 13.4583 6.57004 13.4583 6.57437 13.4583C6.5787 13.4583 6.58305 13.4583 6.58741 13.4583C6.59178 13.4583 6.59616 13.4583 6.60055 13.4583C6.60495 13.4583 6.60936 13.4583 6.61379 13.4583C6.61822 13.4583 6.62267 13.4583 6.62713 13.4583C6.6316 13.4583 6.63608 13.4583 6.64057 13.4583C6.64507 13.4583 6.64958 13.4583 6.65411 13.4583C6.65864 13.4583 6.66318 13.4583 6.66774 13.4583C6.6723 13.4583 6.67688 13.4583 6.68147 13.4583C6.68606 13.4583 6.69067 13.4583 6.69529 13.4583C6.69992 13.4583 6.70456 13.4583 6.70921 13.4583C6.71387 13.4583 6.71854 13.4583 6.72323 13.4583C6.72792 13.4583 6.73262 13.4583 6.73734 13.4583C6.74205 13.4583 6.74679 13.4583 6.75154 13.4583C6.75629 13.4583 6.76105 13.4583 6.76583 13.4583C6.77061 13.4583 6.77541 13.4583 6.78022 13.4583C6.78503 13.4583 6.78985 13.4583 6.7947 13.4583C6.79954 13.4583 6.80439 13.4583 6.80926 13.4583C6.81413 13.4583 6.81902 13.4583 6.82392 13.4583C6.82882 13.4583 6.83374 13.4583 6.83867 13.4583C6.8436 13.4583 6.84855 13.4583 6.85351 13.4583C6.85847 13.4583 6.86344 13.4583 6.86843 13.4583C6.87342 13.4583 6.87843 13.4583 6.88345 13.4583C6.88847 13.4583 6.8935 13.4583 6.89855 13.4583C6.9036 13.4583 6.90866 13.4583 6.91374 13.4583C6.91881 13.4583 6.9239 13.4583 6.92901 13.4583C6.93412 13.4583 6.93924 13.4583 6.94437 13.4583C6.9495 13.4583 6.95465 13.4583 6.95981 13.4583C6.96497 13.4583 6.97015 13.4583 6.97534 13.4583C6.98053 13.4583 6.98574 13.4583 6.99095 13.4583C6.99617 13.4583 7.0014 13.4583 7.00665 13.4583C7.01189 13.4583 7.01715 13.4583 7.02242 13.4583C7.0277 13.4583 7.03298 13.4583 7.03828 13.4583C7.04358 13.4583 7.0489 13.4583 7.05422 13.4583C7.05955 13.4583 7.06489 13.4583 7.07025 13.4583C7.0756 13.4583 7.08097 13.4583 7.08635 13.4583C7.09173 13.4583 7.09712 13.4583 7.10253 13.4583C7.10793 13.4583 7.11335 13.4583 7.11879 13.4583C7.12422 13.4583 7.12966 13.4583 7.13512 13.4583C7.14058 13.4583 7.14605 13.4583 7.15154 13.4583C7.15702 13.4583 7.16252 13.4583 7.16803 13.4583C7.17354 13.4583 7.17906 13.4583 7.1846 13.4583C7.19013 13.4583 7.19568 13.4583 7.20124 13.4583C7.2068 13.4583 7.21238 13.4583 7.21796 13.4583C7.22355 13.4583 7.22915 13.4583 7.23476 13.4583C7.24037 13.4583 7.24599 13.4583 7.25162 13.4583C7.25726 13.4583 7.26291 13.4583 7.26857 13.4583C7.27423 13.4583 7.2799 13.4583 7.28558 13.4583C7.29126 13.4583 7.29696 13.4583 7.30267 13.4583C7.30837 13.4583 7.31409 13.4583 7.31982 13.4583C7.32556 13.4583 7.3313 13.4583 7.33705 13.4583C7.34281 13.4583 7.34858 13.4583 7.35435 13.4583C7.36013 13.4583 7.36592 13.4583 7.37172 13.4583C7.37752 13.4583 7.38334 13.4583 7.38916 13.4583C7.39498 13.4583 7.40082 13.4583 7.40667 13.4583C7.41251 13.4583 7.41837 13.4583 7.42424 13.4583C7.43011 13.4583 7.43599 13.4583 7.44188 13.4583C7.44778 13.4583 7.45368 13.4583 7.45959 13.4583C7.46551 13.4583 7.47143 13.4583 7.47737 13.4583C7.4833 13.4583 7.48925 13.4583 7.49521 13.4583C7.50117 13.4583 7.50714 13.4583 7.51312 13.4583C7.51909 13.4583 7.52508 13.4583 7.53109 13.4583C7.53709 13.4583 7.5431 13.4583 7.54912 13.4583C7.55514 13.4583 7.56117 13.4583 7.56722 13.4583C7.57326 13.4583 7.57931 13.4583 7.58538 13.4583C7.59144 13.4583 7.59751 13.4583 7.6036 13.4583C7.60968 13.4583 7.61578 13.4583 7.62188 13.4583C7.62798 13.4583 7.6341 13.4583 7.64022 13.4583C7.64635 13.4583 7.65248 13.4583 7.65863 13.4583C7.66477 13.4583 7.67093 13.4583 7.67709 13.4583C7.68325 13.4583 7.68943 13.4583 7.69561 13.4583C7.7018 13.4583 7.70799 13.4583 7.71419 13.4583C7.7204 13.4583 7.72661 13.4583 7.73283 13.4583C7.73905 13.4583 7.74528 13.4583 7.75153 13.4583C7.75777 13.4583 7.76402 13.4583 7.77028 13.4583C7.77654 13.4583 7.78281 13.4583 7.78908 13.4583C7.79536 13.4583 7.80165 13.4583 7.80795 13.4583C7.81424 13.4583 7.82055 13.4583 7.82687 13.4583C7.83318 13.4583 7.8395 13.4583 7.84584 13.4583C7.85217 13.4583 7.85851 13.4583 7.86486 13.4583C7.87121 13.4583 7.87757 13.4583 7.88394 13.4583C7.89031 13.4583 7.89669 13.4583 7.90307 13.4583C7.90946 13.4583 7.91585 13.4583 7.92225 13.4583C7.92866 13.4583 7.93507 13.4583 7.94149 13.4583C7.94791 13.4583 7.95433 13.4583 7.96077 13.4583C7.96721 13.4583 7.97365 13.4583 7.9801 13.4583C7.98656 13.4583 7.99302 13.4583 7.99949 13.4583C8.00596 13.4583 8.01243 13.4583 8.01892 13.4583C8.0254 13.4583 8.0319 13.4583 8.0384 13.4583C8.0449 13.4583 8.05141 13.4583 8.05793 13.4583C8.06444 13.4583 8.07097 13.4583 8.0775 13.4583C8.08403 13.4583 8.09057 13.4583 8.09712 13.4583C8.10367 13.4583 8.11022 13.4583 8.11679 13.4583C8.12335 13.4583 8.12992 13.4583 8.1365 13.4583C8.14308 13.4583 8.14966 13.4583 8.15625 13.4583C8.16285 13.4583 8.16945 13.4583 8.17605 13.4583C8.18266 13.4583 8.18927 13.4583 8.1959 13.4583C8.20252 13.4583 8.20915 13.4583 8.21578 13.4583C8.22242 13.4583 8.22906 13.4583 8.23571 13.4583C8.24236 13.4583 8.24901 13.4583 8.25568 13.4583C8.26234 13.4583 8.26901 13.4583 8.27569 13.4583C8.28236 13.4583 8.28905 13.4583 8.29574 13.4583C8.30243 13.4583 8.30912 13.4583 8.31583 13.4583C8.32253 13.4583 8.32924 13.4583 8.33596 13.4583C8.34267 13.4583 8.34939 13.4583 8.35612 13.4583C8.36285 13.4583 8.36959 13.4583 8.37633 13.4583C8.38307 13.4583 8.38982 13.4583 8.39657 13.4583C8.40332 13.4583 8.41008 13.4583 8.41685 13.4583C8.42361 13.4583 8.43039 13.4583 8.43716 13.4583C8.44394 13.4583 8.45072 13.4583 8.45751 13.4583C8.4643 13.4583 8.4711 13.4583 8.4779 13.4583C8.4847 13.4583 8.49151 13.4583 8.49832 13.4583C8.50513 13.4583 8.51195 13.4583 8.51877 13.4583C8.52559 13.4583 8.53242 13.4583 8.53926 13.4583C8.54609 13.4583 8.55293 13.4583 8.55978 13.4583C8.56662 13.4583 8.57347 13.4583 8.58033 13.4583C8.58718 13.4583 8.59404 13.4583 8.60091 13.4583C8.60777 13.4583 8.61464 13.4583 8.62152 13.4583C8.6284 13.4583 8.63528 13.4583 8.64216 13.4583C8.64905 13.4583 8.65594 13.4583 8.66283 13.4583C8.66973 13.4583 8.67663 13.4583 8.68353 13.4583C8.69044 13.4583 8.69735 13.4583 8.70426 13.4583C8.71118 13.4583 8.71809 13.4583 8.72502 13.4583C8.73194 13.4583 8.73887 13.4583 8.7458 13.4583C8.75273 13.4583 8.75967 13.4583 8.76661 13.4583C8.77355 13.4583 8.7805 13.4583 8.78745 13.4583C8.79439 13.4583 8.80135 13.4583 8.80831 13.4583C8.81526 13.4583 8.82222 13.4583 8.82919 13.4583C8.83616 13.4583 8.84313 13.4583 8.8501 13.4583C8.85707 13.4583 8.86405 13.4583 8.87103 13.4583C8.87801 13.4583 8.885 13.4583 8.89199 13.4583C8.89898 13.4583 8.90597 13.4583 8.91296 13.4583C8.91996 13.4583 8.92696 13.4583 8.93396 13.4583C8.94097 13.4583 8.94797 13.4583 8.95498 13.4583C8.96199 13.4583 8.96901 13.4583 8.97602 13.4583C8.98304 13.4583 8.99006 13.4583 8.99708 13.4583C9.00411 13.4583 9.01113 13.4583 9.01816 13.4583C9.02519 13.4583 9.03222 13.4583 9.03926 13.4583C9.04629 13.4583 9.05333 13.4583 9.06037 13.4583C9.06742 13.4583 9.07446 13.4583 9.08151 13.4583C9.08855 13.4583 9.0956 13.4583 9.10266 13.4583C9.10971 13.4583 9.11677 13.4583 9.12382 13.4583C9.13088 13.4583 9.13794 13.4583 9.145 13.4583C9.15207 13.4583 9.15913 13.4583 9.1662 13.4583C9.17327 13.4583 9.18034 13.4583 9.18741 13.4583C9.19448 13.4583 9.20156 13.4583 9.20864 13.4583C9.21571 13.4583 9.22279 13.4583 9.22987 13.4583C9.23695 13.4583 9.24404 13.4583 9.25112 13.4583C9.25821 13.4583 9.2653 13.4583 9.27238 13.4583C9.27947 13.4583 9.28657 13.4583 9.29366 13.4583C9.30075 13.4583 9.30785 13.4583 9.31494 13.4583C9.32204 13.4583 9.32914 13.4583 9.33624 13.4583C9.34334 13.4583 9.35044 13.4583 9.35754 13.4583C9.36464 13.4583 9.37175 13.4583 9.37885 13.4583C9.38596 13.4583 9.39306 13.4583 9.40017 13.4583C9.40728 13.4583 9.41439 13.4583 9.4215 13.4583C9.42861 13.4583 9.43572 13.4583 9.44284 13.4583C9.44995 13.4583 9.45706 13.4583 9.46418 13.4583C9.47129 13.4583 9.47841 13.4583 9.48553 13.4583C9.49264 13.4583 9.49976 13.4583 9.50688 13.4583C9.514 13.4583 9.52112 13.4583 9.52824 13.4583C9.53536 13.4583 9.54248 13.4583 9.5496 13.4583C9.55672 13.4583 9.56384 13.4583 9.57097 13.4583C9.57809 13.4583 9.58521 13.4583 9.59233 13.4583C9.59946 13.4583 9.60658 13.4583 9.61371 13.4583C9.62083 13.4583 9.62795 13.4583 9.63508 13.4583C9.6422 13.4583 9.64933 13.4583 9.65645 13.4583C9.66358 13.4583 9.6707 13.4583 9.67783 13.4583C9.68495 13.4583 9.69207 13.4583 9.6992 13.4583C9.70632 13.4583 9.71345 13.4583 9.72057 13.4583C9.7277 13.4583 9.73482 13.4583 9.74194 13.4583C9.74907 13.4583 9.75619 13.4583 9.76331 13.4583C9.77044 13.4583 9.77756 13.4583 9.78468 13.4583C9.7918 13.4583 9.79892 13.4583 9.80605 13.4583C9.81317 13.4583 9.82029 13.4583 9.82741 13.4583C9.83453 13.4583 9.84164 13.4583 9.84876 13.4583C9.85588 13.4583 9.863 13.4583 9.87011 13.4583C9.87723 13.4583 9.88434 13.4583 9.89146 13.4583C9.89857 13.4583 9.90569 13.4583 9.9128 13.4583C9.91991 13.4583 9.92702 13.4583 9.93413 13.4583C9.94124 13.4583 9.94835 13.4583 9.95546 13.4583C9.96256 13.4583 9.96967 13.4583 9.97677 13.4583C9.98388 13.4583 9.99098 13.4583 9.99808 13.4583C10.0052 13.4583 10.0123 13.4583 10.0194 13.4583C10.0265 13.4583 10.0336 13.4583 10.0407 13.4583C10.0478 13.4583 10.0549 13.4583 10.062 13.4583C10.069 13.4583 10.0761 13.4583 10.0832 13.4583C10.0903 13.4583 10.0974 13.4583 10.1045 13.4583C10.1116 13.4583 10.1186 13.4583 10.1257 13.4583C10.1328 13.4583 10.1399 13.4583 10.147 13.4583C10.154 13.4583 10.1611 13.4583 10.1682 13.4583C10.1752 13.4583 10.1823 13.4583 10.1894 13.4583C10.1964 13.4583 10.2035 13.4583 10.2106 13.4583C10.2176 13.4583 10.2247 13.4583 10.2317 13.4583C10.2388 13.4583 10.2459 13.4583 10.2529 13.4583C10.26 13.4583 10.267 13.4583 10.274 13.4583C10.2811 13.4583 10.2881 13.4583 10.2952 13.4583C10.3022 13.4583 10.3092 13.4583 10.3163 13.4583C10.3233 13.4583 10.3303 13.4583 10.3374 13.4583C10.3444 13.4583 10.3514 13.4583 10.3584 13.4583C10.3655 13.4583 10.3725 13.4583 10.3795 13.4583C10.3865 13.4583 10.3935 13.4583 10.4005 13.4583C10.4075 13.4583 10.4145 13.4583 10.4215 13.4583C10.4285 13.4583 10.4355 13.4583 10.4425 13.4583C10.4495 13.4583 10.4565 13.4583 10.4635 13.4583C10.4705 13.4583 10.4775 13.4583 10.4844 13.4583C10.4914 13.4583 10.4984 13.4583 10.5054 13.4583C10.5123 13.4583 10.5193 13.4583 10.5263 13.4583C10.5332 13.4583 10.5402 13.4583 10.5471 13.4583C10.5541 13.4583 10.561 13.4583 10.568 13.4583C10.5749 13.4583 10.5819 13.4583 10.5888 13.4583C10.5957 13.4583 10.6027 13.4583 10.6096 13.4583C10.6165 13.4583 10.6235 13.4583 10.6304 13.4583C10.6373 13.4583 10.6442 13.4583 10.6511 13.4583C10.658 13.4583 10.6649 13.4583 10.6718 13.4583C10.6787 13.4583 10.6856 13.4583 10.6925 13.4583C10.6994 13.4583 10.7063 13.4583 10.7132 13.4583C10.7201 13.4583 10.7269 13.4583 10.7338 13.4583C10.7407 13.4583 10.7476 13.4583 10.7544 13.4583C10.7613 13.4583 10.7681 13.4583 10.775 13.4583C10.7818 13.4583 10.7887 13.4583 10.7955 13.4583C10.8024 13.4583 10.8092 13.4583 10.816 13.4583C10.8229 13.4583 10.8297 13.4583 10.8365 13.4583C10.8433 13.4583 10.8501 13.4583 10.8569 13.4583C10.8637 13.4583 10.8705 13.4583 10.8773 13.4583C10.8841 13.4583 10.8909 13.4583 10.8977 13.4583C10.9045 13.4583 10.9113 13.4583 10.918 13.4583C10.9248 13.4583 10.9316 13.4583 10.9383 13.4583C10.9451 13.4583 10.9519 13.4583 10.9586 13.4583C10.9654 13.4583 10.9721 13.4583 10.9788 13.4583C10.9856 13.4583 10.9923 13.4583 10.999 13.4583C11.0057 13.4583 11.0125 13.4583 11.0192 13.4583C11.0259 13.4583 11.0326 13.4583 11.0393 13.4583C11.046 13.4583 11.0527 13.4583 11.0594 13.4583C11.066 13.4583 11.0727 13.4583 11.0794 13.4583C11.0861 13.4583 11.0927 13.4583 11.0994 13.4583C11.106 13.4583 11.1127 13.4583 11.1193 13.4583C11.126 13.4583 11.1326 13.4583 11.1392 13.4583C11.1459 13.4583 11.1525 13.4583 11.1591 13.4583C11.1657 13.4583 11.1723 13.4583 11.1789 13.4583C11.1855 13.4583 11.1921 13.4583 11.1987 13.4583C11.2053 13.4583 11.2119 13.4583 11.2185 13.4583C11.225 13.4583 11.2316 13.4583 11.2381 13.4583C11.2447 13.4583 11.2513 13.4583 11.2578 13.4583C11.2643 13.4583 11.2709 13.4583 11.2774 13.4583C11.2839 13.4583 11.2904 13.4583 11.2969 13.4583C11.3035 13.4583 11.31 13.4583 11.3165 13.4583C11.323 13.4583 11.3294 13.4583 11.3359 13.4583C11.3424 13.4583 11.3489 13.4583 11.3553 13.4583C11.3618 13.4583 11.3682 13.4583 11.3747 13.4583C11.3811 13.4583 11.3876 13.4583 11.394 13.4583C11.4004 13.4583 11.4069 13.4583 11.4133 13.4583C11.4197 13.4583 11.4261 13.4583 11.4325 13.4583C11.4389 13.4583 11.4453 13.4583 11.4516 13.4583C11.458 13.4583 11.4644 13.4583 11.4707 13.4583C11.4771 13.4583 11.4835 13.4583 11.4898 13.4583C11.4961 13.4583 11.5025 13.4583 11.5088 13.4583C11.5151 13.4583 11.5214 13.4583 11.5278 13.4583C11.5341 13.4583 11.5404 13.4583 11.5466 13.4583C11.5529 13.4583 11.5592 13.4583 11.5655 13.4583C11.5718 13.4583 11.578 13.4583 11.5843 13.4583C11.5905 13.4583 11.5968 13.4583 11.603 13.4583C11.6092 13.4583 11.6155 13.4583 11.6217 13.4583C11.6279 13.4583 11.6341 13.4583 11.6403 13.4583C11.6465 13.4583 11.6527 13.4583 11.6588 13.4583C11.665 13.4583 11.6712 13.4583 11.6773 13.4583C11.6835 13.4583 11.6896 13.4583 11.6958 13.4583C11.7019 13.4583 11.708 13.4583 11.7142 13.4583C11.7203 13.4583 11.7264 13.4583 11.7325 13.4583C11.7386 13.4583 11.7447 13.4583 11.7507 13.4583C11.7568 13.4583 11.7629 13.4583 11.7689 13.4583C11.775 13.4583 11.781 13.4583 11.7871 13.4583C11.7931 13.4583 11.7991 13.4583 11.8051 13.4583C11.8111 13.4583 11.8171 13.4583 11.8231 13.4583C11.8291 13.4583 11.8351 13.4583 11.8411 13.4583C11.847 13.4583 11.853 13.4583 11.859 13.4583C11.8649 13.4583 11.8708 13.4583 11.8768 13.4583C11.8827 13.4583 11.8886 13.4583 11.8945 13.4583C11.9004 13.4583 11.9063 13.4583 11.9122 13.4583C11.9181 13.4583 11.924 13.4583 11.9298 13.4583C11.9357 13.4583 11.9415 13.4583 11.9474 13.4583C11.9532 13.4583 11.959 13.4583 11.9648 13.4583C11.9707 13.4583 11.9765 13.4583 11.9822 13.4583C11.988 13.4583 11.9938 13.4583 11.9996 13.4583C12.0054 13.4583 12.0111 13.4583 12.0169 13.4583C12.0226 13.4583 12.0283 13.4583 12.0341 13.4583C12.0398 13.4583 12.0455 13.4583 12.0512 13.4583C12.0569 13.4583 12.0626 13.4583 12.0682 13.4583C12.0739 13.4583 12.0796 13.4583 12.0852 13.4583C12.0909 13.4583 12.0965 13.4583 12.1021 13.4583C12.1078 13.4583 12.1134 13.4583 12.119 13.4583C12.1246 13.4583 12.1302 13.4583 12.1357 13.4583C12.1413 13.4583 12.1469 13.4583 12.1524 13.4583C12.158 13.4583 12.1635 13.4583 12.169 13.4583C12.1746 13.4583 12.1801 13.4583 12.1856 13.4583C12.1911 13.4583 12.1966 13.4583 12.202 13.4583C12.2075 13.4583 12.213 13.4583 12.2184 13.4583C12.2239 13.4583 12.2293 13.4583 12.2347 13.4583C12.2401 13.4583 12.2455 13.4583 12.2509 13.4583C12.2563 13.4583 12.2617 13.4583 12.2671 13.4583C12.2725 13.4583 12.2778 13.4583 12.2832 13.4583C12.2885 13.4583 12.2938 13.4583 12.2991 13.4583C12.3045 13.4583 12.3098 13.4583 12.315 13.4583C12.3203 13.4583 12.3256 13.4583 12.3309 13.4583C12.3361 13.4583 12.3414 13.4583 12.3466 13.4583C12.3518 13.4583 12.3571 13.4583 12.3623 13.4583C12.3675 13.4583 12.3727 13.4583 12.3779 13.4583C12.383 13.4583 12.3882 13.4583 12.3933 13.4583C12.3985 13.4583 12.4036 13.4583 12.4088 13.4583C12.4139 13.4583 12.419 13.4583 12.4241 13.4583C12.4292 13.4583 12.4342 13.4583 12.4393 13.4583C12.4444 13.4583 12.4494 13.4583 12.4545 13.4583C12.4595 13.4583 12.4645 13.4583 12.4695 13.4583C12.4745 13.4583 12.4795 13.4583 12.4845 13.4583C12.4895 13.4583 12.4944 13.4583 12.4994 13.4583C12.5043 13.4583 12.5093 13.4583 12.5142 13.4583C12.5191 13.4583 12.524 13.4583 12.5289 13.4583C12.5338 13.4583 12.5387 13.4583 12.5435 13.4583C12.5484 13.4583 12.5532 13.4583 12.5581 13.4583C12.5629 13.4583 12.5677 13.4583 12.5725 13.4583C12.5773 13.4583 12.5821 13.4583 12.5868 13.4583C12.5916 13.4583 12.5964 13.4583 12.6011 13.4583C12.6058 13.4583 12.6106 13.4583 12.6153 13.4583C12.62 13.4583 12.6247 13.4583 12.6293 13.4583C12.634 13.4583 12.6387 13.4583 12.6433 13.4583C12.6479 13.4583 12.6526 13.4583 12.6572 13.4583C12.6618 13.4583 12.6664 13.4583 12.671 13.4583C12.6755 13.4583 12.6801 13.4583 12.6847 13.4583C12.6892 13.4583 12.6937 13.4583 12.6982 13.4583C12.7028 13.4583 12.7073 13.4583 12.7117 13.4583C12.7162 13.4583 12.7207 13.4583 12.7251 13.4583C12.7296 13.4583 12.734 13.4583 12.7384 13.4583C12.7428 13.4583 12.7472 13.4583 12.7516 13.4583C12.756 13.4583 12.7604 13.4583 12.7647 13.4583C12.7691 13.4583 12.7734 13.4583 12.7777 13.4583C12.782 13.4583 12.7863 13.4583 12.7906 13.4583C12.7949 13.4583 12.7992 13.4583 12.8034 13.4583C12.8077 13.4583 12.8119 13.4583 12.8161 13.4583C12.8203 13.4583 12.8245 13.4583 12.8287 13.4583C12.8329 13.4583 12.8371 13.4583 12.8412 13.4583C12.8454 13.4583 12.8495 13.4583 12.8536 13.4583C12.8577 13.4583 12.8618 13.4583 12.8659 13.4583C12.87 13.4583 12.874 13.4583 12.8781 13.4583C12.8821 13.4583 12.8861 13.4583 12.8901 13.4583C12.8941 13.4583 12.8981 13.4583 12.9021 13.4583C12.9061 13.4583 12.91 13.4583 12.9139 13.4583C12.9179 13.4583 12.9218 13.4583 12.9257 13.4583C12.9296 13.4583 12.9335 13.4583 12.9373 13.4583C12.9412 13.4583 12.945 13.4583 12.9489 13.4583C12.9527 13.4583 12.9565 13.4583 12.9603 13.4583C12.9641 13.4583 12.9679 13.4583 12.9716 13.4583C12.9754 13.4583 12.9791 13.4583 12.9828 13.4583C12.9865 13.4583 12.9902 13.4583 12.9939 13.4583C12.9976 13.4583 13.0013 13.4583 13.0049 13.4583C13.0085 13.4583 13.0122 13.4583 13.0158 13.4583C13.0194 13.4583 13.023 13.4583 13.0265 13.4583C13.0301 13.4583 13.0336 13.4583 13.0372 13.4583C13.0407 13.4583 13.0442 13.4583 13.0477 13.4583C13.0512 13.4583 13.0547 13.4583 13.0581 13.4583C13.0616 13.4583 13.065 13.4583 13.0684 13.4583C13.0718 13.4583 13.0752 13.4583 13.0786 13.4583C13.082 13.4583 13.0853 13.4583 13.0887 13.4583C13.092 13.4583 13.0953 13.4583 13.0986 13.4583C13.1019 13.4583 13.1052 13.4583 13.1084 13.4583C13.1117 13.4583 13.1149 13.4583 13.1182 13.4583C13.1214 13.4583 13.1246 13.4583 13.1278 13.4583C13.1309 13.4583 13.1341 13.4583 13.1372 13.4583C13.1404 13.4583 13.1435 13.4583 13.1466 13.4583C13.1497 13.4583 13.1528 13.4583 13.1558 13.4583C13.1589 13.4583 13.1619 13.4583 13.1649 13.4583C13.168 13.4583 13.171 13.4583 13.1739 13.4583C13.1769 13.4583 13.1799 13.4583 13.1828 13.4583C13.1857 13.4583 13.1887 13.4583 13.1915 13.4583C13.1944 13.4583 13.1973 13.4583 13.2002 13.4583C13.203 13.4583 13.2059 13.4583 13.2087 13.4583C13.2115 13.4583 13.2143 13.4583 13.217 13.4583C13.2198 13.4583 13.2226 13.4583 13.2253 13.4583C13.228 13.4583 13.2307 13.4583 13.2334 13.4583C13.2361 13.4583 13.2388 13.4583 13.2414 13.4583C13.2441 13.4583 13.2467 13.4583 13.2493 13.4583C13.2519 13.4583 13.2545 13.4583 13.257 13.4583C13.2596 13.4583 13.2621 13.4583 13.2646 13.4583C13.2672 13.4583 13.2696 13.4583 13.2721 13.4583C13.2746 13.4583 13.277 13.4583 13.2795 13.4583C13.2819 13.4583 13.2843 13.4583 13.2867 13.4583C13.2891 13.4583 13.2914 13.4583 13.2938 13.4583C13.2961 13.4583 13.2985 13.4583 13.3007 13.4583C13.303 13.4583 13.3053 13.4583 13.3076 13.4583C13.3098 13.4583 13.3121 13.4583 13.3143 13.4583C13.3165 13.4583 13.3187 13.4583 13.3208 13.4583C13.323 13.4583 13.3251 13.4583 13.3273 13.4583C13.3294 13.4583 13.3315 13.4583 13.3336 13.4583C13.3356 13.4583 13.3377 13.4583 13.3397 13.4583C13.3418 13.4583 13.3438 13.4583 13.3457 13.4583C13.3477 13.4583 13.3497 13.4583 13.3516 13.4583C13.3536 13.4583 13.3555 13.4583 13.3574 13.4583C13.3593 13.4583 13.3611 13.4583 13.363 13.4583C13.3648 13.4583 13.3667 13.4583 13.3685 13.4583C13.3703 13.4583 13.372 13.4583 13.3738 13.4583C13.3756 13.4583 13.3773 13.4583 13.379 13.4583C13.3807 13.4583 13.3824 13.4583 13.3841 13.4583C13.3857 13.4583 13.3874 13.4583 13.389 13.4583C13.3906 13.4583 13.3922 13.4583 13.3937 13.4583C13.3953 13.4583 13.3969 13.4583 13.3984 13.4583C13.3999 13.4583 13.4014 13.4583 13.4029 13.4583C13.4043 13.4583 13.4058 13.4583 13.4072 13.4583C13.4086 13.4583 13.41 13.4583 13.4114 13.4583C13.4128 13.4583 13.4142 13.4583 13.4155 13.4583C13.4168 13.4583 13.4181 13.4583 13.4194 13.4583C13.4207 13.4583 13.4219 13.4583 13.4232 13.4583C13.4244 13.4583 13.4256 13.4583 13.4268 13.4583C13.428 13.4583 13.4291 13.4583 13.4303 13.4583C13.4314 13.4583 13.4325 13.4583 13.4336 13.4583C13.4347 13.4583 13.4358 13.4583 13.4368 13.4583C13.4378 13.4583 13.4388 13.4583 13.4398 13.4583C13.4408 13.4583 13.4418 13.4583 13.4427 13.4583C13.4436 13.4583 13.4446 13.4583 13.4454 13.4583C13.4463 13.4583 13.4472 13.4583 13.448 13.4583C13.4489 13.4583 13.4497 13.4583 13.4505 13.4583C13.4512 13.4583 13.452 13.4583 13.4527 13.4583C13.4535 13.4583 13.4542 13.4583 13.4549 13.4583C13.4556 13.4583 13.4562 13.4583 13.4569 13.4583C13.4575 13.4583 13.4581 13.4583 13.4587 13.4583C13.4593 13.4583 13.4598 13.4583 13.4604 13.4583C13.4609 13.4583 13.4614 13.4583 13.4619 13.4583C13.4624 13.4583 13.4628 13.4583 13.4632 13.4583V14.4583ZM5.89058 14.4583V15.4583C5.2187 15.4583 4.55847 14.9331 4.55847 14.1476H5.55847H6.55847C6.55847 13.7051 6.19564 13.4583 5.89058 13.4583V14.4583ZM5.55847 14.1476H4.55847C4.55847 13.362 5.2187 12.8368 5.89058 12.8368V13.8368V14.8368C6.19564 14.8368 6.55847 14.59 6.55847 14.1476H5.55847ZM5.89058 13.8368V12.8368H6.36152V13.8368V14.8368H5.89058V13.8368ZM6.36152 13.8368H5.36152V12.9479H6.36152H7.36152V13.8368H6.36152ZM6.36152 12.9479H5.36152C5.36152 11.3745 6.22298 10.0797 7.30825 9.14593L7.96046 9.90396L8.61267 10.662C7.79489 11.3656 7.36152 12.1531 7.36152 12.9479H6.36152ZM7.96046 9.90396L7.30823 9.14594C7.74305 8.77182 8.17171 8.48856 8.49855 8.29508L9.00796 9.15561L9.51736 10.0161C9.26349 10.1664 8.93676 10.3831 8.61268 10.662L7.96046 9.90396ZM9.00796 9.15561L8.49855 10.0161C8.17171 9.82266 7.74305 9.5394 7.30823 9.16528L7.96046 8.40725L8.61268 7.64923C8.93676 7.92807 9.26349 8.1448 9.51736 8.29508L9.00796 9.15561ZM7.96046 8.40725L7.30825 9.16529C6.10436 8.12948 5.36152 6.81982 5.36152 5.36329H6.36152H7.36152C7.36152 6.0895 7.72447 6.88502 8.61266 7.64921L7.96046 8.40725ZM6.36152 5.36329H5.36152V4.47437H6.36152H7.36152V5.36329H6.36152ZM6.36152 4.47437V5.47437H5.89058V4.47437V3.47437H6.36152V4.47437ZM5.89058 4.47437V5.47437C5.2187 5.47437 4.55847 4.94927 4.55847 4.16367H5.55847H6.55847C6.55847 3.72125 6.19564 3.47437 5.89058 3.47437V4.47437ZM5.55847 4.16367H4.55847C4.55847 3.37807 5.2187 2.85297 5.89058 2.85297V3.85297V4.85297C6.19564 4.85297 6.55847 4.60608 6.55847 4.16367H5.55847ZM5.89058 3.85297V2.85297H13.4632V3.85297V4.85297H5.89058V3.85297ZM13.4632 3.85297V2.85297C14.1351 2.85297 14.7953 3.37807 14.7953 4.16367H13.7953H12.7953C12.7953 4.60608 13.1582 4.85297 13.4632 4.85297V3.85297ZM13.7953 4.16367H14.7953C14.7953 4.94927 14.1351 5.47437 13.4632 5.47437V4.47437V3.47437C13.1582 3.47437 12.7953 3.72125 12.7953 4.16367H13.7953ZM12.3281 12.9479H11.3281C11.3281 12.721 11.2838 12.4964 11.19 12.2691L12.1143 11.8875L13.0386 11.5059C13.2288 11.9666 13.3281 12.4499 13.3281 12.9479H12.3281ZM12.1143 11.8875V12.8875H7.23951V11.8875V10.8875H12.1143V11.8875ZM7.23951 11.8875L8.16383 12.2691C8.06998 12.4965 8.02574 12.721 8.02574 12.9479H7.02574H6.02574C6.02574 12.4499 6.12498 11.9666 6.31518 11.5059L7.23951 11.8875ZM7.02574 12.9479H8.02574V13.8368H7.02574H6.02574V12.9479H7.02574ZM7.02574 13.8368V12.8368H12.3281V13.8368V14.8368H7.02574V13.8368ZM12.3281 13.8368H11.3281V12.9479H12.3281H13.3281V13.8368H12.3281ZM7.58034 11.2661V10.2661H11.7735V11.2661V12.2661H7.58034V11.2661ZM11.7735 11.2661L10.9502 11.8337C10.5744 11.2887 9.91009 10.7539 9.2169 10.3948L9.67692 9.50685L10.1369 8.61894C11.0327 9.083 11.9856 9.81204 12.5968 10.6985L11.7735 11.2661ZM9.67692 9.50685L10.1369 10.3948C9.44306 10.7542 8.77916 11.289 8.40364 11.8337L7.58034 11.2661L6.75705 10.6985C7.36785 9.81253 8.32028 9.08347 9.2169 8.61894L9.67692 9.50685ZM9.6769 8.80437L9.21686 7.91647C10.4975 7.25298 11.3281 6.27895 11.3281 5.36329H12.3281H13.3281C13.3281 7.44064 11.6072 8.93051 10.1369 9.69227L9.6769 8.80437ZM12.3281 5.36329H11.3281V4.9144H12.3281H13.3281V5.36329H12.3281ZM12.3281 4.9144L13.0341 5.6226L12.2295 6.42477L11.5234 5.71658L10.8174 5.00838L11.6221 4.20621L12.3281 4.9144ZM11.5234 5.71658L12.2295 6.42476C11.5465 7.10563 10.6276 7.47145 9.65968 7.47148L9.65965 6.47148L9.65962 5.47148C10.1189 5.47146 10.5238 5.30114 10.8174 5.00839L11.5234 5.71658ZM9.65965 6.47148V7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964H9.65964H9.65964H9.65964H9.65963C9.65963 7.47148 9.65963 7.47148 9.65963 7.47148C9.65963 7.47148 9.65963 7.47148 9.65963 7.47148C9.65963 7.47148 9.65963 6.47148 9.65963 5.47148C9.65963 5.47148 9.65963 5.47148 9.65963 5.47148H9.65963C9.65963 5.47148 9.65963 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148H9.65964C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148H9.65964H9.65964C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148H9.65964H9.65964C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148H9.65964C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65965 5.47148H9.65965H9.65965C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148H9.65965H9.65965C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148H9.65965H9.65965H9.65965C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148H9.65965H9.65965C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 6.47148 9.65965 7.47148H9.65965H9.65965C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148H9.65965C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148H9.65965C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148H9.65965H9.65965V6.47148V5.47148H9.65965H9.65965C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148H9.65965C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148H9.65965C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148H9.65965H9.65965C9.65965 6.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148H9.65965H9.65965C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148H9.65965H9.65965H9.65965C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148H9.65965H9.65965C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148C9.65965 7.47148 9.65965 7.47148 9.65965 7.47148H9.65965H9.65965C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964H9.65964C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964H9.65964C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148H9.65964C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65964 7.47148 9.65964 7.47148 9.65964 7.47148C9.65963 7.47148 9.65963 7.47148 9.65963 7.47148H9.65963C9.65963 7.47148 9.65963 7.47148 9.65963 7.47148C9.65963 6.47148 9.65963 5.47148 9.65963 5.47148C9.65963 5.47148 9.65963 5.47148 9.65963 5.47148C9.65963 5.47148 9.65963 5.47148 9.65963 5.47148H9.65964H9.65964H9.65964H9.65964H9.65964C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148H9.65964C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148H9.65964C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148C9.65964 5.47148 9.65964 5.47148 9.65964 5.47148H9.65964C9.65964 5.47148 9.65964 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148C9.65965 5.47148 9.65965 5.47148 9.65965 5.47148V6.47148ZM9.65965 6.47148V7.47148C8.69173 7.47148 7.77272 7.10557 7.08982 6.42477L7.79584 5.71658L8.50186 5.00838C8.7955 5.30112 9.20046 5.47148 9.65965 5.47148V6.47148ZM7.79584 5.71658L7.08982 6.42476L6.31972 5.65702L7.02574 4.94883L7.73176 4.24064L8.50186 5.00839L7.79584 5.71658ZM7.02574 4.94883H8.02574V5.36331H7.02574H6.02574V4.94883H7.02574ZM7.02574 5.36331L8.02574 5.36332C8.02573 6.27346 8.85034 7.24988 10.1369 7.91647L9.6769 8.80437L9.21687 9.69228C7.73608 8.92507 6.02571 7.43208 6.02574 5.36329L7.02574 5.36331ZM7.4607 4.47437L8.16671 3.76618L8.98619 4.58314L8.28017 5.29133L7.57415 5.99952L6.75468 5.18256L7.4607 4.47437ZM8.28017 5.29133L8.9862 4.58315C9.15297 4.74942 9.38585 4.85007 9.65963 4.85007V5.85007V6.85007C8.87711 6.85007 8.13017 6.55386 7.57413 5.99951L8.28017 5.29133ZM9.65963 5.85007L9.65959 4.85007C9.9334 4.85006 10.1663 4.74941 10.3331 4.58313L11.0391 5.29133L11.7451 5.99953C11.189 6.55387 10.4421 6.85004 9.65967 6.85007L9.65963 5.85007ZM11.0391 5.29133L10.3331 4.58313L11.1526 3.76617L11.8586 4.47437L12.5646 5.18257L11.7451 5.99953L11.0391 5.29133ZM11.8586 4.47437V5.47437H7.4607V4.47437V3.47437H11.8586V4.47437Z" fill="#1759CA" mask="url(#path-2-inside-1_61_93)"/>
</svg>
`,
                    `<!-- Icon orang --> <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="9.67691" cy="9.82357" rx="9.60969" ry="8.99019" fill="white"/>
                                        <mask id="path-2-inside-1_61_89" fill="white">
                                        <path d="M14.9078 10.9268C14.7347 9.98028 14.0878 9.22034 13.2348 8.86786C13.6133 8.55964 13.8551 8.09292 13.8551 7.57154C13.8551 6.64548 13.0927 5.89206 12.1556 5.89206C11.2185 5.89206 10.4561 6.64541 10.4561 7.57154C10.4561 8.09323 10.6981 8.5601 11.0771 8.8684C10.9357 8.92695 10.7987 8.99684 10.6678 9.0783C10.338 9.28363 10.0565 9.55403 9.83952 9.87071C9.61869 9.72829 9.38123 9.60978 9.13089 9.51885C9.66892 9.14121 10.0207 8.52027 10.0207 7.81957C10.0207 6.67127 9.07602 5.73706 7.91486 5.73706C6.75369 5.73706 5.80904 6.67127 5.80904 7.81957C5.80904 8.52027 6.16079 9.14121 6.69882 9.51885C5.55382 9.93478 4.67562 10.9239 4.44784 12.1692C4.40577 12.3994 4.47039 12.6343 4.625 12.8135C4.77816 12.9909 5.00163 13.0927 5.23813 13.0927H10.5916C10.8281 13.0927 11.0516 12.9909 11.2047 12.8135C11.3594 12.6343 11.4239 12.3994 11.3819 12.1692C11.3538 12.0156 11.3157 11.8659 11.2686 11.7208H14.2276C14.4312 11.7208 14.6237 11.6332 14.7555 11.4805C14.8884 11.3265 14.9439 11.1246 14.9078 10.9268ZM11.0703 7.57154C11.0703 6.97428 11.5572 6.48846 12.1556 6.48846C12.754 6.48846 13.2409 6.97428 13.2409 7.57154C13.2409 8.16871 12.754 8.65453 12.1556 8.65453C11.5572 8.65453 11.0703 8.16871 11.0703 7.57154ZM6.42329 7.81957C6.42329 7.00014 7.09241 6.33346 7.91486 6.33346C8.7373 6.33346 9.4065 7.00014 9.4065 7.81957C9.4065 8.639 8.7373 9.30568 7.91486 9.30568C7.09241 9.30568 6.42329 8.639 6.42329 7.81957ZM10.7339 12.4304C10.7127 12.455 10.666 12.4963 10.5916 12.4963H5.23813C5.16375 12.4963 5.11704 12.455 5.09576 12.4304C5.07369 12.4048 5.03866 12.3503 5.05266 12.2734C5.30396 10.8994 6.50767 9.90208 7.91486 9.90208C9.32204 9.90208 10.5258 10.8994 10.7771 12.2734C10.7911 12.3503 10.7561 12.4048 10.7339 12.4304ZM14.2847 11.0974C14.276 11.1075 14.257 11.1244 14.2277 11.1244H11.0104C10.8287 10.7938 10.5953 10.4972 10.3216 10.2442C10.7216 9.62633 11.4054 9.25093 12.1556 9.25093C13.2113 9.25093 14.1143 9.99954 14.3029 11.0311C14.3089 11.0636 14.2941 11.0867 14.2847 11.0974Z"/>
                                        </mask>
                                        <path d="M14.9078 10.9268C14.7347 9.98028 14.0878 9.22034 13.2348 8.86786C13.6133 8.55964 13.8551 8.09292 13.8551 7.57154C13.8551 6.64548 13.0927 5.89206 12.1556 5.89206C11.2185 5.89206 10.4561 6.64541 10.4561 7.57154C10.4561 8.09323 10.6981 8.5601 11.0771 8.8684C10.9357 8.92695 10.7987 8.99684 10.6678 9.0783C10.338 9.28363 10.0565 9.55403 9.83952 9.87071C9.61869 9.72829 9.38123 9.60978 9.13089 9.51885C9.66892 9.14121 10.0207 8.52027 10.0207 7.81957C10.0207 6.67127 9.07602 5.73706 7.91486 5.73706C6.75369 5.73706 5.80904 6.67127 5.80904 7.81957C5.80904 8.52027 6.16079 9.14121 6.69882 9.51885C5.55382 9.93478 4.67562 10.9239 4.44784 12.1692C4.40577 12.3994 4.47039 12.6343 4.625 12.8135C4.77816 12.9909 5.00163 13.0927 5.23813 13.0927H10.5916C10.8281 13.0927 11.0516 12.9909 11.2047 12.8135C11.3594 12.6343 11.4239 12.3994 11.3819 12.1692C11.3538 12.0156 11.3157 11.8659 11.2686 11.7208H14.2276C14.4312 11.7208 14.6237 11.6332 14.7555 11.4805C14.8884 11.3265 14.9439 11.1246 14.9078 10.9268ZM11.0703 7.57154C11.0703 6.97428 11.5572 6.48846 12.1556 6.48846C12.754 6.48846 13.2409 6.97428 13.2409 7.57154C13.2409 8.16871 12.754 8.65453 12.1556 8.65453C11.5572 8.65453 11.0703 8.16871 11.0703 7.57154ZM6.42329 7.81957C6.42329 7.00014 7.09241 6.33346 7.91486 6.33346C8.7373 6.33346 9.4065 7.00014 9.4065 7.81957C9.4065 8.639 8.7373 9.30568 7.91486 9.30568C7.09241 9.30568 6.42329 8.639 6.42329 7.81957ZM10.7339 12.4304C10.7127 12.455 10.666 12.4963 10.5916 12.4963H5.23813C5.16375 12.4963 5.11704 12.455 5.09576 12.4304C5.07369 12.4048 5.03866 12.3503 5.05266 12.2734C5.30396 10.8994 6.50767 9.90208 7.91486 9.90208C9.32204 9.90208 10.5258 10.8994 10.7771 12.2734C10.7911 12.3503 10.7561 12.4048 10.7339 12.4304ZM14.2847 11.0974C14.276 11.1075 14.257 11.1244 14.2277 11.1244H11.0104C10.8287 10.7938 10.5953 10.4972 10.3216 10.2442C10.7216 9.62633 11.4054 9.25093 12.1556 9.25093C13.2113 9.25093 14.1143 9.99954 14.3029 11.0311C14.3089 11.0636 14.2941 11.0867 14.2847 11.0974Z" fill="white"/>
                                        <path d="M14.9078 10.9268L16.2168 10.6876L16.2168 10.6874L14.9078 10.9268ZM13.2348 8.86786L12.3946 7.83592L10.6637 9.24526L12.7266 10.0977L13.2348 8.86786ZM11.0771 8.8684L11.5862 10.0979L13.6477 9.24426L11.9169 7.83613L11.0771 8.8684ZM10.6678 9.0783L9.9645 7.94861L9.96443 7.94865L10.6678 9.0783ZM9.83952 9.87071L9.11826 10.989L10.2058 11.6904L10.9373 10.6229L9.83952 9.87071ZM9.13089 9.51885L8.36638 8.42964L6.2753 9.89737L8.67655 10.7696L9.13089 9.51885ZM6.69882 9.51885L7.15317 10.7696L9.5544 9.89736L7.46333 8.42964L6.69882 9.51885ZM4.44784 12.1692L3.13883 11.9297L3.13878 11.93L4.44784 12.1692ZM4.625 12.8135L3.61735 13.6827L3.61763 13.683L4.625 12.8135ZM11.2047 12.8135L12.2121 13.683L12.2121 13.6829L11.2047 12.8135ZM11.3819 12.1692L12.6909 11.93L12.6909 11.9299L11.3819 12.1692ZM11.2686 11.7208V10.3901H9.43763L10.0029 12.1316L11.2686 11.7208ZM14.7555 11.4805L15.7627 12.3501L15.7628 12.3501L14.7555 11.4805ZM10.7339 12.4304L9.72726 11.5601L9.72712 11.5603L10.7339 12.4304ZM5.09576 12.4304L4.08752 13.2989L4.08894 13.3006L5.09576 12.4304ZM5.05266 12.2734L3.74364 12.034L3.74345 12.035L5.05266 12.2734ZM10.7771 12.2734L9.46803 12.5127L9.46809 12.5131L10.7771 12.2734ZM14.2847 11.0974L13.2792 10.2258L13.2775 10.2277L14.2847 11.0974ZM11.0104 11.1244L9.84428 11.7654L10.2234 12.4551H11.0104V11.1244ZM10.3216 10.2442L9.20452 9.52101L8.59574 10.4613L9.4184 11.2215L10.3216 10.2442ZM14.3029 11.0311L15.6122 10.7929L15.6119 10.7916L14.3029 11.0311ZM14.9078 10.9268L16.2168 10.6874C15.9592 9.279 14.9975 8.15639 13.743 7.63799L13.2348 8.86786L12.7266 10.0977C13.1781 10.2843 13.5101 10.6816 13.5987 11.1661L14.9078 10.9268ZM13.2348 8.86786L14.075 9.89979C14.7469 9.35277 15.1859 8.51468 15.1859 7.57154H13.8551H12.5244C12.5244 7.67117 12.4798 7.76651 12.3946 7.83592L13.2348 8.86786ZM13.8551 7.57154H15.1859C15.1859 5.89572 13.8128 4.56133 12.1556 4.56133V5.89206V7.22279C12.3727 7.22279 12.5244 7.39525 12.5244 7.57154H13.8551ZM12.1556 5.89206V4.56133C10.4985 4.56133 9.12536 5.8956 9.12536 7.57154H10.4561H11.7868C11.7868 7.39521 11.9385 7.22279 12.1556 7.22279V5.89206ZM10.4561 7.57154H9.12536C9.12536 8.51527 9.56486 9.35363 10.2373 9.90066L11.0771 8.8684L11.9169 7.83613C11.8314 7.76658 11.7868 7.6712 11.7868 7.57154H10.4561ZM11.0771 8.8684L10.568 7.63891C10.3614 7.72442 10.1593 7.82735 9.9645 7.94861L10.6678 9.0783L11.3711 10.208C11.438 10.1663 11.5099 10.1295 11.5862 10.0979L11.0771 8.8684ZM10.6678 9.0783L9.96443 7.94865C9.47811 8.25146 9.06243 8.65053 8.74176 9.11853L9.83952 9.87071L10.9373 10.6229C11.0506 10.4575 11.198 10.3158 11.3712 10.208L10.6678 9.0783ZM9.83952 9.87071L10.5608 8.75239C10.2573 8.5567 9.93048 8.39349 9.58522 8.26808L9.13089 9.51885L8.67655 10.7696C8.83198 10.8261 8.98003 10.8999 9.11826 10.989L9.83952 9.87071ZM9.13089 9.51885L9.8954 10.6081C10.7686 9.99515 11.3514 8.97749 11.3514 7.81957H10.0207H8.68995C8.68995 8.06304 8.56923 8.28726 8.36638 8.42964L9.13089 9.51885ZM10.0207 7.81957H11.3514C11.3514 5.9224 9.79696 4.40633 7.91486 4.40633V5.73706V7.06779C8.35508 7.06779 8.68995 7.42013 8.68995 7.81957H10.0207ZM7.91486 5.73706V4.40633C6.03275 4.40633 4.47831 5.9224 4.47831 7.81957H5.80904H7.13977C7.13977 7.42013 7.47463 7.06779 7.91486 7.06779V5.73706ZM5.80904 7.81957H4.47831C4.47831 8.97749 5.0611 9.99515 5.93431 10.6081L6.69882 9.51885L7.46333 8.42964C7.26048 8.28726 7.13977 8.06304 7.13977 7.81957H5.80904ZM6.69882 9.51885L6.24448 8.26808C4.67167 8.83941 3.45478 10.2025 3.13883 11.9297L4.44784 12.1692L5.75685 12.4086C5.89647 11.6453 6.43596 11.0301 7.15317 10.7696L6.69882 9.51885ZM4.44784 12.1692L3.13878 11.93C3.02334 12.5618 3.20348 13.2029 3.61735 13.6827L4.625 12.8135L5.63264 11.9443C5.73731 12.0656 5.7882 12.2371 5.7569 12.4084L4.44784 12.1692ZM4.625 12.8135L3.61763 13.683C4.02626 14.1564 4.61887 14.4234 5.23813 14.4234V13.0927V11.7619C5.38438 11.7619 5.53007 11.8255 5.63236 11.944L4.625 12.8135ZM5.23813 13.0927V14.4234H10.5916V13.0927V11.7619H5.23813V13.0927ZM10.5916 13.0927V14.4234C11.2108 14.4234 11.8035 14.1564 12.2121 13.683L11.2047 12.8135L10.1974 11.944C10.2996 11.8255 10.4453 11.7619 10.5916 11.7619V13.0927ZM11.2047 12.8135L12.2121 13.6829C12.6265 13.2028 12.8063 12.5615 12.6909 11.93L11.3819 12.1692L10.0728 12.4084C10.0416 12.2374 10.0923 12.0657 10.1973 11.9441L11.2047 12.8135ZM11.3819 12.1692L12.6909 11.9299C12.6521 11.7173 12.5994 11.5104 12.5343 11.31L11.2686 11.7208L10.0029 12.1316C10.0321 12.2215 10.0555 12.3138 10.0728 12.4084L11.3819 12.1692ZM11.2686 11.7208V13.0515H14.2276V11.7208V10.3901H11.2686V11.7208ZM14.2276 11.7208V13.0515C14.8138 13.0515 15.3754 12.7988 15.7627 12.3501L14.7555 11.4805L13.7482 10.6109C13.8719 10.4676 14.0486 10.3901 14.2276 10.3901V11.7208ZM14.7555 11.4805L15.7628 12.3501C16.1555 11.8952 16.3263 11.2867 16.2168 10.6876L14.9078 10.9268L13.5987 11.1659C13.5615 10.9625 13.6213 10.7579 13.7481 10.611L14.7555 11.4805ZM11.0703 7.57154H12.4011C12.4011 7.71179 12.2896 7.81919 12.1556 7.81919V6.48846V5.15773C10.8248 5.15773 9.73962 6.23677 9.73962 7.57154H11.0703ZM12.1556 6.48846V7.81919C12.0217 7.81919 11.9101 7.71179 11.9101 7.57154H13.2409H14.5716C14.5716 6.23677 13.4864 5.15773 12.1556 5.15773V6.48846ZM13.2409 7.57154H11.9101C11.9101 7.43114 12.0217 7.3238 12.1556 7.3238V8.65453V9.98526C13.4863 9.98526 14.5716 8.90629 14.5716 7.57154H13.2409ZM12.1556 8.65453V7.3238C12.2895 7.3238 12.4011 7.43114 12.4011 7.57154H11.0703H9.73962C9.73962 8.90629 10.8249 9.98526 12.1556 9.98526V8.65453ZM6.42329 7.81957H7.75402C7.75402 7.73967 7.82275 7.66419 7.91486 7.66419V6.33346V5.00273C6.36207 5.00273 5.09256 6.26061 5.09256 7.81957H6.42329ZM7.91486 6.33346V7.66419C8.00705 7.66419 8.07577 7.73977 8.07577 7.81957H9.4065H10.7372C10.7372 6.26052 9.46755 5.00273 7.91486 5.00273V6.33346ZM9.4065 7.81957H8.07577C8.07577 7.89938 8.00705 7.97495 7.91486 7.97495V9.30568V10.6364C9.46755 10.6364 10.7372 9.37863 10.7372 7.81957H9.4065ZM7.91486 9.30568V7.97495C7.82275 7.97495 7.75402 7.89947 7.75402 7.81957H6.42329H5.09256C5.09256 9.37853 6.36207 10.6364 7.91486 10.6364V9.30568ZM10.7339 12.4304L9.72712 11.5603C9.8383 11.4316 10.1271 11.1655 10.5916 11.1655V12.4963V13.827C11.2048 13.827 11.587 13.4784 11.7408 13.3006L10.7339 12.4304ZM10.5916 12.4963V11.1655H5.23813V12.4963V13.827H10.5916V12.4963ZM5.23813 12.4963V11.1655C5.70261 11.1655 5.99141 11.4316 6.10259 11.5603L5.09576 12.4304L4.08894 13.3006C4.24267 13.4784 4.62489 13.827 5.23813 13.827V12.4963ZM5.09576 12.4304L6.10401 11.5619C6.22055 11.6972 6.44807 12.0383 6.36186 12.5118L5.05266 12.2734L3.74345 12.035C3.62925 12.6623 3.92683 13.1124 4.08752 13.2989L5.09576 12.4304ZM5.05266 12.2734L6.36167 12.5128C6.49609 11.7779 7.14318 11.2328 7.91486 11.2328V9.90208V8.57135C5.87216 8.57135 4.11182 10.0209 3.74364 12.034L5.05266 12.2734ZM7.91486 9.90208V11.2328C8.68659 11.2328 9.33368 11.7779 9.46803 12.5127L10.7771 12.2734L12.0861 12.0341C11.718 10.0209 9.95749 8.57135 7.91486 8.57135V9.90208ZM10.7771 12.2734L9.46809 12.5131C9.38126 12.0388 9.6091 11.6968 9.72726 11.5601L10.7339 12.4304L11.7406 13.3007C11.9031 13.1128 12.201 12.6617 12.086 12.0337L10.7771 12.2734ZM14.2847 11.0974L13.2775 10.2277C13.3901 10.0974 13.7065 9.79366 14.2277 9.79366V11.1244V12.4551C14.8076 12.4551 15.1619 12.1177 15.2919 11.9672L14.2847 11.0974ZM14.2277 11.1244V9.79366H11.0104V11.1244V12.4551H14.2277V11.1244ZM11.0104 11.1244L12.1766 10.4834C11.925 10.0256 11.6023 9.61586 11.2247 9.26693L10.3216 10.2442L9.4184 11.2215C9.58837 11.3786 9.73246 11.562 9.84428 11.7654L11.0104 11.1244ZM10.3216 10.2442L11.4386 10.9675C11.5932 10.7287 11.8563 10.5817 12.1556 10.5817V9.25093V7.9202C10.9545 7.9202 9.85005 8.52398 9.20452 9.52101L10.3216 10.2442ZM12.1556 9.25093V10.5817C12.5751 10.5817 12.922 10.8774 12.9939 11.2705L14.3029 11.0311L15.6119 10.7916C15.3065 9.12173 13.8475 7.9202 12.1556 7.9202V9.25093ZM14.3029 11.0311L12.9937 11.2692C12.8997 10.7526 13.1458 10.3796 13.2792 10.2258L14.2847 11.0974L15.2902 11.9691C15.4423 11.7937 15.718 11.3745 15.6122 10.7929L14.3029 11.0311Z" fill="#1759CA" mask="url(#path-2-inside-1_61_89)"/>
                                        </svg>`,
                    `<!-- Icon print --> <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="9.67691" cy="9.17647" rx="9.60969" ry="8.99019" fill="white"/>
                                        <path d="M13.5594 6.62788H13.2591V5.54424C13.2591 4.71435 12.5374 4.03918 11.6503 4.03918H7.70347C6.8164 4.03918 6.09471 4.71435 6.09471 5.54424V6.62788H5.7944C4.90733 6.62788 4.18564 7.30305 4.18564 8.13293V10.541C4.18564 11.3709 4.90733 12.0461 5.7944 12.0461H6.09471V13.4107C6.09471 13.9086 6.52772 14.3137 7.05996 14.3137H12.2938C12.8261 14.3137 13.2591 13.9086 13.2591 13.4107V12.0461H13.5594C14.4464 12.0461 15.1681 11.3709 15.1681 10.541V8.13293C15.1681 7.30305 14.4464 6.62788 13.5594 6.62788ZM6.73821 5.54424C6.73821 5.04631 7.17123 4.64121 7.70347 4.64121H11.6503C12.1825 4.64121 12.6156 5.04631 12.6156 5.54424V6.62788H6.73821V5.54424ZM12.6156 13.4107C12.6156 13.5766 12.4712 13.7117 12.2938 13.7117H7.05996C6.88255 13.7117 6.73821 13.5766 6.73821 13.4107V10.4407H12.6156V13.4107ZM14.5246 10.541C14.5246 11.039 14.0916 11.4441 13.5594 11.4441H13.2591V10.4407H13.4521C13.6298 10.4407 13.7739 10.3059 13.7739 10.1397C13.7739 9.97343 13.6298 9.83866 13.4521 9.83866H5.90165C5.72396 9.83866 5.5799 9.97343 5.5799 10.1397C5.5799 10.3059 5.72396 10.4407 5.90165 10.4407H6.09471V11.4441H5.7944C5.26216 11.4441 4.82914 11.039 4.82914 10.541V8.13293C4.82914 7.635 5.26216 7.2299 5.7944 7.2299H13.5594C14.0916 7.2299 14.5246 7.635 14.5246 8.13293V10.541Z" fill="#1759CA"/>
                                        <path d="M10.5349 11.123H8.81889C8.6412 11.123 8.49714 11.2578 8.49714 11.4241C8.49714 11.5903 8.6412 11.7251 8.81889 11.7251H10.5349C10.7126 11.7251 10.8567 11.5903 10.8567 11.4241C10.8567 11.2578 10.7126 11.123 10.5349 11.123Z" fill="#1759CA"/>
                                        <path d="M10.5349 12.4072H8.81889C8.6412 12.4072 8.49714 12.542 8.49714 12.7082C8.49714 12.8745 8.6412 13.0092 8.81889 13.0092H10.5349C10.7126 13.0092 10.8567 12.8745 10.8567 12.7082C10.8567 12.542 10.7126 12.4072 10.5349 12.4072Z" fill="#1759CA"/>
                                        <path d="M6.93129 7.91211H5.90168C5.72398 7.91211 5.57992 8.04688 5.57992 8.21312C5.57992 8.37936 5.72398 8.51413 5.90168 8.51413H6.93129C7.10898 8.51413 7.25304 8.37936 7.25304 8.21312C7.25304 8.04688 7.10898 7.91211 6.93129 7.91211Z" fill="#1759CA"/>
                                        </svg>`,
                    `<!-- Icon file --> <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="9.67691" cy="9.00985" rx="9.60969" ry="8.99019" fill="white"/>
                                        <path d="M14.3478 6.22683H14.2631C14.2135 5.82925 13.8715 5.51404 13.4441 5.47547C13.3951 5.04687 13.0064 4.71191 12.5356 4.71191H6.70388C5.92344 4.71191 5.28847 5.30595 5.28847 6.03601V9.86133C5.28847 10.305 5.65133 10.6706 6.11384 10.7124C6.16238 11.1364 6.54341 11.4686 7.0075 11.4755C7.0087 11.5508 7.02129 11.6234 7.04413 11.6919C7.14807 12.0041 7.45998 12.2314 7.82743 12.2314H14.3478C14.8002 12.2314 15.1681 11.8871 15.1681 11.464V6.99426C15.1681 6.57109 14.8002 6.22683 14.3478 6.22683ZM14.7822 6.99426V9.95011L13.9428 9.35693C13.7135 9.19488 13.4033 9.19234 13.171 9.35065L12.1092 10.0744L10.0843 8.1214C9.84306 7.8887 9.45058 7.87438 9.19076 8.08889L7.39312 9.57264V6.99426C7.39312 6.77016 7.58797 6.58787 7.82743 6.58787H14.3478C14.5874 6.58787 14.7822 6.77016 14.7822 6.99426ZM14.7822 11.464C14.7822 11.6881 14.5874 11.8704 14.3479 11.8704H7.82743C7.63289 11.8704 7.46774 11.75 7.41272 11.5847C7.39998 11.5465 7.39312 11.506 7.39312 11.464V10.0541L9.44613 8.35961C9.55113 8.27287 9.70972 8.27865 9.80726 8.37272L10.7211 9.25418C10.7211 9.25425 10.7211 9.25425 10.7212 9.25425L12.5401 11.0086C12.5779 11.0452 12.6282 11.0635 12.6786 11.0635C12.727 11.0635 12.7755 11.0466 12.813 11.0126C12.8894 10.9431 12.8913 10.8289 12.8171 10.7573L12.3805 10.3362L13.3982 9.64252C13.4922 9.57849 13.6175 9.57948 13.7102 9.64499L14.7822 10.4025V11.464ZM6.49449 10.6208V6.35848C6.49449 6.34036 6.49547 6.32238 6.49743 6.30475C6.52622 6.03982 6.76614 5.83243 7.05679 5.83243H9.41711C9.52369 5.83243 9.61008 5.75161 9.61008 5.6519C9.61008 5.55219 9.52369 5.47138 9.41711 5.47138H7.05679C6.53391 5.47138 6.10856 5.86931 6.10856 6.35848V10.3476C5.86201 10.3061 5.6744 10.1038 5.6744 9.86133V6.03601C5.6744 5.50502 6.13622 5.07296 6.70388 5.07296H12.5356C12.7917 5.07296 13.0058 5.24453 13.0536 5.47138H10.9608C10.8542 5.47138 10.7679 5.55219 10.7679 5.6519C10.7679 5.75161 10.8542 5.83243 10.9608 5.83243H13.2565H13.2567H13.3557C13.6103 5.83243 13.8234 6.00202 13.8728 6.22683H7.82743C7.37518 6.22683 7.00719 6.57109 7.00719 6.99426V9.97247V9.97261V11.1145C6.7231 11.1069 6.49449 10.8884 6.49449 10.6208Z" fill="#1759CA"/>
                                        <path d="M12.7854 7.32251C12.33 7.32251 11.9596 7.6691 11.9596 8.09502C11.9596 8.52101 12.33 8.8676 12.7854 8.8676C13.2407 8.8676 13.6111 8.52101 13.6111 8.09502C13.6111 7.6691 13.2407 7.32251 12.7854 7.32251ZM12.7854 8.50656C12.5428 8.50656 12.3455 8.32194 12.3455 8.09509C12.3455 7.86817 12.5428 7.68356 12.7854 7.68356C13.0279 7.68356 13.2252 7.86817 13.2252 8.09509C13.2252 8.32194 13.0279 8.50656 12.7854 8.50656Z" fill="#1759CA"/>
                                        <path d="M10.0109 5.72101C10.0157 5.73187 10.0217 5.74231 10.0286 5.75211C10.0357 5.76198 10.0437 5.77122 10.0526 5.77954C10.0616 5.78779 10.0714 5.79541 10.082 5.80211C10.0924 5.80859 10.1038 5.81416 10.1155 5.81868C10.127 5.82319 10.1392 5.82665 10.1516 5.82897C10.1639 5.8313 10.1767 5.83243 10.1893 5.83243C10.202 5.83243 10.2146 5.8313 10.2272 5.82897C10.2395 5.82665 10.2515 5.82319 10.2633 5.81868C10.2749 5.81416 10.2861 5.80859 10.2966 5.80211C10.3071 5.79541 10.317 5.78779 10.3259 5.77954C10.335 5.77122 10.3429 5.76198 10.3501 5.75211C10.357 5.74231 10.363 5.73187 10.3678 5.72101C10.3727 5.71001 10.3764 5.69866 10.3789 5.68731C10.3813 5.67553 10.3826 5.66361 10.3826 5.65191C10.3826 5.64013 10.3813 5.62821 10.3789 5.61672C10.3764 5.60515 10.3727 5.59373 10.3678 5.58294C10.363 5.57194 10.357 5.56143 10.3501 5.5517C10.3429 5.54176 10.335 5.53259 10.3259 5.52427C10.317 5.51595 10.3071 5.5084 10.2966 5.50185C10.2861 5.49536 10.2749 5.48979 10.2633 5.48527C10.2515 5.48076 10.2395 5.47731 10.2272 5.47498C10.2022 5.47025 10.1765 5.47025 10.1516 5.47498C10.1392 5.47731 10.127 5.48076 10.1155 5.48527C10.1038 5.48979 10.0924 5.49536 10.082 5.50185C10.0714 5.5084 10.0616 5.51595 10.0526 5.52427C10.0437 5.53259 10.0357 5.54176 10.0286 5.5517C10.0217 5.56143 10.0157 5.57194 10.0109 5.58294C10.0061 5.59373 10.0024 5.60515 9.99987 5.61672C9.99738 5.62821 9.99602 5.64013 9.99602 5.65191C9.99602 5.66361 9.99738 5.67553 9.99987 5.68731C10.0024 5.69866 10.0061 5.71001 10.0109 5.72101Z" fill="#1759CA"/>
                                        </svg>`
                ];

                const detailItems = pkg.description
                    .split('\n')
                    .map((line, index) => {
                        const icon = icons[index + 1] || '';
                        return `<li><span class="icon">${icon}</span>${line}</li>`;
                    })
                    .join('');

                div.innerHTML = `
                    <div class="header">
                        <h2>${pkg.title}</h2>
                        <span class="price">Rp${Number(pkg.price).toLocaleString()}</span>
                    </div>
                    <ul class="details-list">
                        ${detailItems}
                        <li><span class="icon">${icons[0]}</span>Durasi: ${pkg.duration_minutes} menit</li>
                    </ul>
                `;

                container.appendChild(div);
            });
        })
        .catch(error => console.error('Gagal load paket:', error));
}


    // Submit data booking ke server
    async function submitBooking() {
        const name = document.getElementById('nama').value;
        const email = document.getElementById('email').value;
        const whatsapp = document.getElementById('whatsapp').value;
        const peopleCount = parseInt(document.getElementById('jumlah-orang').value);
        const agreement = document.getElementById('agreement').checked;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Validasi input frontend
        if (!bookingData.package || !bookingData.package_id || !bookingData.date || !bookingData.time) {
            Swal.fire({ icon: 'warning', title: 'Lengkapi Data', text: 'Harap pilih paket, tanggal, dan waktu terlebih dahulu.' });
            return;
        }

        if (!name || !email || !whatsapp || !peopleCount || !agreement) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Harap lengkapi semua form dan setujui syarat & ketentuan.' });
            return;
        }
        let finalPrice = Number(bookingData.price);
        let extraPerPerson = Number(bookingData.extras_people) || 0;
        if (peopleCount > 1) {
            finalPrice += (peopleCount - 1) * extraPerPerson;
        }
        if (bookingData.voucher_discount) {
            finalPrice -= bookingData.voucher_discount;
        }

        if (finalPrice < 0) finalPrice = 0;
        const dataToSend = {
            name,
            email,
            whatsapp,
            people_count: peopleCount,
            package_id: bookingData.package_id,
            background_id: bookingData.background_id,
            date: bookingData.date,
            time: bookingData.time,
            price: finalPrice,
            voucher_id: bookingData.voucher_id ?? null,
            confirmation: agreement ? 1 : 0,
            extras_people: bookingData.extras_people,
            downPayment: bookingData.downPayment,
        };

        try {
            const res = await fetch('/api/submit-booking', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(dataToSend)
            });

            const result = await res.json();

            if (res.ok) {
                resetBookingForm();
                goToPage('page-sukses', dataToSend);
                setTimeout(() => location.reload(), 50000);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal Booking', text: result.message || 'Terjadi kesalahan saat menyimpan booking.' });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Gagal Terhubung', text: 'Tidak bisa terhubung ke server. Coba lagi nanti.' });
            console.error(err);
        }
    }


    // Reset seluruh data form dan UI ke kondisi awal
    function resetBookingForm() {
        document.getElementById('nama').value = '';
        document.getElementById('email').value = '';
        document.getElementById('whatsapp').value = '';
        document.getElementById('jumlah-orang').value = '';
        document.getElementById('agreement').checked = false;

        document.querySelectorAll('.background-item.selected').forEach(item => item.classList.remove('selected'));
        document.querySelectorAll('.time-slot.selected').forEach(item => item.classList.remove('selected'));
        document.querySelectorAll('.fc-day-selected').forEach(item => item.classList.remove('fc-day-selected'));

        bookingData = { package: null, background: null, date: null, time: null };
        pageHistory = ['page-pilih-paket'];
    }

    // --- INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('calendar');

        // Inisialisasi kalender menggunakan FullCalendar
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            selectable: true,
            validRange: { start: new Date().toISOString().split('T')[0] },
            headerToolbar: { left: 'prev', center: 'title', right: 'next' },
            dateClick: async function (info) {
                const clickedDate = info.date;
                const dayName = dayMap[clickedDate.getDay()];
                if (closedDays.includes(dayName)) {
                    Swal.fire({ icon: 'warning', title: 'Studio Tutup', text: `Hari ${dayName} kami tutup, silakan pilih hari lain.` });
                    return;
                }

                document.querySelectorAll('.fc-day-selected').forEach(el => el.classList.remove('fc-day-selected'));
                info.dayEl.classList.add('fc-day-selected');

                bookingData.date = info.dateStr;
                bookingData.time = null;

                await loadBookedTimes(info.dateStr);
                updateSlotsForDate(new Date(info.dateStr), bookingData.package_id);
            },

            dayCellDidMount: function (info) {
                const dayName = dayMap[info.date.getDay()];
                if (closedDays.includes(dayName)) info.el.classList.add('fc-day-disabled');
            }
        });

        // Load data awal dari backend
        goToPage('page-pilih-paket');
        loadPackages();
        loadClosedDays();
        loadTodayCloseTime();
        loadBlockedDates();
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
