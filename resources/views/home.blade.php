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
        .package-summary ul li { display: flex; align-items: center; margin-bottom: 1rem; font-weight: 500; }
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
                            <h3 id="time-slots-date-header">Pilih Tanggal Dulu</h3>
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
                    <label for="jumlah-orang">Jumlah Orang (Tambah 15 rb/org jika lebih dari 1)<span class="red-star">*</span></label>
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
            let dp = 0;
            if (data.package_id === 1) {
                dp = 15000;
            } else if (data.package_id === 2 || data.package_id === 3) {
                dp = 30000;
            }
            document.getElementById('dp-price').textContent = `Rp ${dp.toLocaleString()}`;
            document.getElementById('confirmedDp').textContent = `DP sebesar Rp ${dp.toLocaleString()} hangus`;
        }
        if (pageId === 'page-sukses') {
            let dp = 0;
            if (data.package_id === 1) {
                dp = 15000;
            } else if (data.package_id === 2 || data.package_id === 3) {
                dp = 30000;
            }
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
        console.log(new Date('2025-07-19T00:00:00Z'))
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
        let extraPerPerson = 0;
        switch (bookingData.package_id) {
            case 1: extraPerPerson = 15000; break;
            case 2: extraPerPerson = 20000; break;
        }
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
    async function selectPackage(packageName, packageId, packagePrice,packageDescription) {
        bookingData.package = packageName;
        bookingData.packageDescription = packageDescription;
        bookingData.package_id = packageId;
        bookingData.price = packagePrice;
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
    async function updateSlotsForDate(date) {
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

                if (isBooked || isPastTime) {
                    slotEl.classList.add('booked');
                    slotEl.style.opacity = '0.5';
                    slotEl.style.pointerEvents = 'none';
                    slotEl.title = isBooked ? 'Sudah dibooking' : 'Waktu telah lewat';
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

                    div.onclick = () => selectPackage(pkg.title, pkg.id, pkg.price, pkg.description);
                    const detailItems = pkg.description
                        .split('\n')
                        .map(line => `<li><span class="icon"></span>${line}</li>`)
                        .join('');

                    div.innerHTML = `
                        <div class="header">
                            <h2>${pkg.title}</h2>
                            <span class="price">Rp${Number(pkg.price).toLocaleString()}</span>
                        </div>
                        <ul class="details-list">
                            ${detailItems}
                            <li><span class="icon"></span>Durasi: ${pkg.duration_minutes} menit</li>
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
        let extraPerPerson = 0;
        switch (bookingData.package_id) {
            case 1: extraPerPerson = 15000; break;
            case 2: extraPerPerson = 20000; break;
        }
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
            confirmation: agreement ? 1 : 0
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
                updateSlotsForDate(new Date(info.dateStr));
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
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
