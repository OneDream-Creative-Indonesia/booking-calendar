<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnapFun - Multi Page App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800&display=swap" rel="stylesheet">
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .app-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            }

        .page {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none; /* sembunyikan default */
        }

        .page.active {
        display: block; /* hanya yang aktif ditampilin */
        }
        .page.active { transform: translateX(0) !important; }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #FFFFFF;
            overflow: hidden;
        }

        /* Container untuk semua halaman */
        .app-container {
            position: relative;
            width: 375px;
            height: 812px;
            margin: 0 auto;
            overflow: hidden;
        }

        .page {
            position: absolute;
            width: 375px;
            height: 812px;
            background: #FFFFFF;
            transition: transform 0.3s ease-in-out;
        }

        .page.active {
            transform: translateX(0);
        }

        .page.slide-left {
            transform: translateX(-100%);
        }

        .page.slide-right {
            transform: translateX(100%);
        }

        /* Header Components - Shared */
        .header-rect {
            position: absolute;
            width: 375px;
            height: 131px;
            left: 0px;
            top: 0px;
            background: #1759CA;
        }

        .content-bg {
            position: absolute;
            width: 375px;
            height: 704px;
            left: 0px;
            top: 108px;
            background: #ffffff;
            border-radius: 20px 20px 0px 0px;
        }

        .logo {
            position: absolute;
            width: 24.75px;
            height: 29.5px;
            left: calc(50% - 24.75px/2 - 133px);
            top: 39px;
            background: #FFFFFF;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1759CA;
            font-weight: 800;
            font-size: 8px;
        }

        .footer {
            position: absolute;
            height: 11px;
            left: calc(50% - 198px/2 - 0.5px);
            top: 790px;
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 900;
            font-size: 10px;
            line-height: 114%;
            text-align: center;
            color: #282828;
        }

        .back-button {
            position: absolute;
            width: 40px;
            height: 40px;
            left: 25px;
            top: 35px;
            cursor: pointer;
            color: white;
            font-size: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }

        /* User Input Styling - Fixed positioning for pages 3 & 6 */
        .user-input {
            position: absolute;
            width: 320px;
            left: calc(50% - 160px);
            top: 560px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: #282828;
        }
         .user-input label {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .user-input input {
            padding: 12px;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            font-size: 14px;
            background: #FFFFFF;
            box-shadow: 0 2px 8px rgba(19, 18, 66, 0.08);
            transition: border-color 0.3s ease;
        }

        .user-input input:focus {
            outline: none;
            border-color: #1759CA;
            box-shadow: 0 0 6px rgba(23, 89, 202, 0.3);
        }
        .user-input2 {
            position: absolute;
            width: 320px;
            left: calc(50% - 160px);
            top: 400px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: #282828;
        }
         .user-input2 label {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .user-input2 input {
            padding: 12px;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            font-size: 14px;
            background: #FFFFFF;
            box-shadow: 0 2px 8px rgba(19, 18, 66, 0.08);
            transition: border-color 0.3s ease;
        }

        .user-input2 input:focus {
            outline: none;
            border-color: #1759CA;
            box-shadow: 0 0 6px rgba(23, 89, 202, 0.3);
        }

        .user-input3 {
            position: absolute;
            width: 320px;
            left: calc(50% - 160px);
            top: 370px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: #282828;
        }
         .user-input3 label {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .user-input3 input {
            padding: 12px;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            font-size: 14px;
            background: #FFFFFF;
            box-shadow: 0 2px 8px rgba(19, 18, 66, 0.08);
            transition: border-color 0.3s ease;
        }

        .user-input3 input:focus {
            outline: none;
            border-color: #1759CA;
            box-shadow: 0 0 6px rgba(23, 89, 202, 0.3);
        }

        .user-input15 {
            position: absolute;
            width: 320px;
            left: calc(50% - 160px);
            top: 480px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: #282828;
        }
         .user-input15 label {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .user-input15 input {
            padding: 12px;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            font-size: 14px;
            background: #FFFFFF;
            box-shadow: 0 2px 8px rgba(19, 18, 66, 0.08);
            transition: border-color 0.3s ease;
        }

        .user-input15 input:focus {
            outline: none;
            border-color: #1759CA;
            box-shadow: 0 0 6px rgba(23, 89, 202, 0.3);
        }

        /* PAGE 1 - Pilih Layout Foto */
        .page1 .header-text {
            position: absolute;
            width: 159px;
            height: 36px;
            left: calc(50% - 159px/2 + 79px);
            top: 43px;
            font-family: 'Montserrat';
            font-weight: 800;
            font-size: 16px;
            line-height: 114%;
            text-align: right;
            color: #FFFFFF;
        }

        .nav-header-bg {
            position: absolute;
            width: 325px;
            height: 88px;
            left: calc(50% - 325px/2);
            top: 155px;
            background: #F7F7F7;
            border-radius: 10px;
        }

        .nav-back-btn {
            position: absolute;
            width: 40px;
            height: 40px;
            left: 47px;
            top: 174px;
            background: #1759CA;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .nav-title {
            position: absolute;
            width: 200px;
            height: 40px;
            left: 97px;
            top: 174px;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 20px;
            line-height: 99%;
            color: #282828;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .nav-arrow {
            color: #CE004F;
            font-size: 18px;
            margin-left: 8px;
            font-weight: bold;
        }

        .layout-card {
            position: absolute;
            width: 151px;
            height: 178px;
            background: #FFFFFF;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            cursor: pointer;
            overflow: hidden;
        }

        .layout-card-yellow {
            position: absolute;
            width: 151px;
            height: 101px;
            background: #FEDD03;
            border-radius: 10px 10px 0px 0px;
            top: 0;
        }

        .card-1 { left: calc(50% - 151px/2 - 83px); top: 265px; }
        .card-2 { left: calc(50% - 151px/2 + 87px); top: 265px; }
        .card-3 { left: calc(50% - 151px/2 - 83px); top: 465px; }
        .card-4 { left: calc(50% - 151px/2 + 87px); top: 465px; }

        .card-label {
            position: absolute;
            font-family: 'Montserrat';
            font-weight: 800;
            font-size: 14px;
            line-height: 100%;
            color: #282828;
            cursor: pointer;
            background: #FFFFFF;
            padding: 10px;
            border-radius: 0px 0px 10px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: start;
        }

        .label-1 { width: 151px; height: 77px; left: calc(50% - 151px/2 - 83px); top: 366px; }
        .label-2 { width: 151px; height: 77px; left: calc(50% - 151px/2 + 87px); top: 366px; }
        .label-3 { width: 151px; height: 77px; left: calc(50% - 151px/2 - 83px); top: 566px; }
        .label-4 { width: 151px; height: 77px; left: calc(50% - 151px/2 + 87px); top: 566px; }

        .card-arrow {
            color: #CE004F;
            font-size: 16px;
            font-weight: bold;
            margin-left: 5px;
        }

        /* PAGE 2 - Logo posisi kanan */
        .page2 .logo {
            left: calc(50% - 24.75px/2 + 133px);
        }

        .page2 .header-text {
            position: absolute;
            width: 93px;
            height: 20px;
            left: calc(50% - 93px/2);
            top: 44px;
            font-family: 'Montserrat';
            font-weight: 500;
            font-size: 20px;
            line-height: 99%;
            color: #FFFFFF;
        }

        .keychain-item {
            position: absolute;
            width: 327px;
            height: 90px;
            left: 24px;
            cursor: pointer;
        }

        .keychain-item-bg {
            position: absolute;
            width: 100%;
            height: 90px;
            background: #FFFFFF;
            border: 1px solid rgba(23, 89, 202, 0.3);
            box-shadow: 0px 2px 8px rgba(23, 89, 202, 0.1);
            border-radius: 15px;
        }

        .keychain-icon {
            position: absolute;
            width: 60px;
            height: 60px;
            left: 20px;
            top: 15px;
            background: #FEDD03;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 14px;
        }

        .keychain-title {
            position: absolute;
            left: 95px;
            top: 20px;
            font-family: 'Montserrat';
            font-weight: 800;
            font-size: 16px;
            line-height: 99%;
            color: #282828;
            width: 50%;
        }


        .keychain-arrow {
            position: absolute;
            width: 20px;
            height: 20px;
            right: 20px;
            top: 35px;
        }

        .keychain-arrow::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            border-right: 2px solid #CE004F;
            border-bottom: 2px solid #CE004F;
            transform: rotate(-45deg);
            top: 6px;
            left: 6px;
        }

        /* posisi manual biar ga numpuk */
        .keychain-1 { top: 135px; }
        .keychain-2 { top: 235px; }
        .keychain-3 { top: 335px; }
        .keychain-4 { top: 435px; }
        .keychain-5 { top: 535px; }

        .grid-item {
            position: absolute;
            width: 327px;
            height: 90px;
            left: 24px;
            cursor: pointer;
        }

        .grid-item-bg {
            position: absolute;
            width: 100%;
            height: 90px;
            background: #FFFFFF;
            border: 1px solid rgba(23, 89, 202, 0.3);
            box-shadow: 0px 2px 8px rgba(23, 89, 202, 0.1);
            border-radius: 15px;
        }

        .grid-icon {
            position: absolute;
            width: 60px;
            height: 60px;
            left: 20px;
            top: 15px;
            background: #FEDD03;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 14px;
        }

        .grid-title {
            position: absolute;
            width: 80px;
            height: 20px;
            left: 95px;
            top: 20px;
            font-family: 'Montserrat';
            font-weight: 800;
            font-size: 16px;
            line-height: 99%;
            color: #282828;
        }

        .grid-desc {
            position: absolute;
            width: 180px;
            height: 30px;
            left: 95px;
            top: 45px;
            font-family: 'Montserrat';
            font-weight: 400;
            font-size: 11px;
            line-height: 140%;
            color: rgba(40, 40, 40, 0.7);
        }

        .grid-arrow {
            position: absolute;
            width: 20px;
            height: 20px;
            right: 20px;
            top: 35px;
        }

        .grid-arrow::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            border-right: 2px solid #CE004F;
            border-bottom: 2px solid #CE004F;
            transform: rotate(-45deg);
            top: 6px;
            left: 6px;
        }

        .grid-1 { top: 135px; }
        .grid-2 { top: 235px; }
        .grid-3 { top: 335px; }
        .grid-4 { top: 435px; }
        .grid-5 { top: 535px; }

        /* PAGE 3 & 6 - Logo posisi kanan */
        .page3 .logo, .page6 .logo, .page7 .logo, .page8 .logo, .page9 .logo,
         .page10 .logo, .page11 .logo, .page12 .logo, .page13 .logo,
          .page14 .logo, .page15 .logo, .page16 .logo, .page17 .logo {
            left: calc(50% - 24.75px/2 + 133px);
        }

        .page3 .header-text, .page6 .header-text, .page7 .header-text,
        .page8 .header-text, .page9 .header-text, .page10 .header-text,
        .page11 .header-text, .page12 .header-text, .page13 .header-text,
        .page14 .header-text, .page15 .header-text, .page16 .header-text,
        .page17 .header-text {
            position: absolute;
            width: 96px;
            height: 20px;
            left: calc(50% - 96px/2 + 0.5px);
            top: 44px;
            font-family: 'Montserrat';
            font-weight: 500;
            font-size: 20px;
            line-height: 99%;
            color: #FFFFFF;
        }

        /* PAGE 4 - Logo posisi tengah */
        .page4 .logo {
            left: calc(50% - 24.75px/2);
        }

        /* PAGE 5 - Logo posisi kanan */
        .page5 .logo {
            left: calc(50% - 24.75px/2 + 133px);
        }

        .page5 .header-text {
            position: absolute;
            width: 120px;
            height: 24px;
            left: 50%;
            top: 44px;
            transform: translateX(-50%);
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            font-size: 20px;
            line-height: 1.2;
            white-space: nowrap;
            color: #ffffff;
            text-align: center;
        }

        .page5 .grid-title5 {
            position: absolute;
            height: 20px;
            left: 95px;
            top: 20px;
            font-family: 'Montserrat';
            font-weight: 800;
            font-size: 16px;
            line-height: 99%;
            color: #282828;
        }

        .photo-title {
            position: absolute;
            width: 149px;
            height: 20px;
            left: calc(50% - 149px/2);
            top: 140px;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
            line-height: 99%;
            color: #282828;
            text-align: center;
        }

          /* photo-stikers */
        .photo-grid9 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/stikers.png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-stikers-slot-1 { top: 90px; left: 80px; }
        .photo-stikers-slot-2 { top: 90px; right: 85px; }
        .photo-stikers-slot-3 { top: 50px; left: 145px; }
        .photo-stikers-slot-4 { bottom: 140px; left: 80px; }
        .photo-stikers-slot-5 { bottom: 140px; left: 150px; }
        .photo-stikers-slot-6 { bottom: 140px; right: 80px; }
        .photo-stikers-slot-7 { bottom: 45px; left: 80px; }
        .photo-stikers-slot-8 { bottom: 45px; left: 150px; }
        .photo-stikers-slot-9 { bottom: 45px; right: 80px; }

        .photo-grid-input-stikers {
            position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 14px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
        .photo-grid-input {
            position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 16px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
        /* photo-grid-gede */
        .photo-grid7 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/gede.png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-gede-slot-1 { top: 50px; left: 85px; }
        .photo-gede-slot-2 { top: 50px; right: 90px; }
        .photo-gede-slot-3 { left: 90px; }
        .photo-gede-slot-4 { right: 90px; }
        .photo-gede-slot-5 { top: 270px; left: 90px; }
        .photo-gede-slot-6 { top: 270px; right: 90px; }

        /* photo-grid-kecil */
         .photo-grid8 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/kecil.png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-kecil-slot-1 { top: 30px; left: 65px; }
        .photo-kecil-slot-2 { top: 30px; right: 155px; }
        .photo-kecil-slot-3 { top: 30px; right: 80px; }
        .photo-kecil-slot-4 { top: 120px; left: 65px; }
        .photo-kecil-slot-5 { top: 120px; left: 140px; }
        .photo-kecil-slot-6 { top: 120px; right: 80px; }
        .photo-kecil-slot-7 { bottom: 105px; left: 65px; }
        .photo-kecil-slot-8 { bottom: 105px; right: 155px; }
        .photo-kecil-slot-9 { bottom: 105px; right: 80px; }
        .photo-kecil-slot-10 { bottom: 10px; left: 65px; }
        .photo-kecil-slot-11 { bottom: 10px; right: 155px; }
        .photo-kecil-slot-12 { bottom: 10px; right: 80px; }

         .photo-grid-input-kecil {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 14px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
       /* bulat */
        .photo-grid14 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/kunci/Bulet.png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
         .keychain-slot-1 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 16px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
         /* kotak */
        .photo-grid15 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/kunci/Kotak.png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
         .keychain-slot-1 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 16px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
         /* love */
        .photo-grid16 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/kunci/Love.png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
         .keychain-slot-1 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 16px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
         /* persegi panjang */
        .photo-grid17 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            /* height: 350px; */
            /*width: 230px;*/
            height: 400px;
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/kunci/persegiPanjang.png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
         .keychain-slot-1 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 16px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
        /* grid 1 */
        .photo-grid1 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/photo/grid(1).png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
         .photo-grid-1 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 16px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
         /* grid 2 */
        .photo-grid2 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 200px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/photo/grid(2).png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
         .photo-grid-2 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 14px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
        .photo-grid-2-slot-1 { top: 80px; left: 85px; }
        .photo-grid-2-slot-2 { top: 80px; right: 85px; }

          /* grid 3 */
        .photo-grid3 {
             /* position: absolute; */
            position: relative;
            left: 50%;
            height: 200px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/photo/grid(3).png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-grid-3 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 14px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
        .photo-grid-3-slot-1 { top: 80px; left: 70px; }
        .photo-grid-3-slot-2 { top: 80px; }
        .photo-grid-3-slot-3 { top: 80px; right: 65px; }
         /* grid 4 */
        .photo-grid4 {
            /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/photo/grid(4).png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-grid-4 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 14px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }
        .photo-grid-4-slot-1 { top: 80px; left: 90px; }
        .photo-grid-4-slot-2 { top: 80px; right: 90px; }
        .photo-grid-4-slot-3 { bottom: 80px; left: 90px; }
        .photo-grid-4-slot-4 { bottom: 80px; right: 90px; }


        /* grid 6 */
        .photo-grid6 {
            /* position: absolute; */
            position: relative;
            left: 50%;
            height: 350px;
            /*width: 230px;*/
            /* height: 400px;*/
            transform: translateX(-50%);
            top: 170px;
            background-image: url('img/grid/photo/grid(6).png');
            background-size: 65%;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-grid-6 {
          position: absolute;
            width: 80px;
            height: 30px;
            text-align: center;
            font-size: 14px;
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }

        .photo-grid-6-slot-1 { top: 50px; left: 85px; }
        .photo-grid-6-slot-2 { top: 50px; right: 90px; }
        .photo-grid-6-slot-3 { left: 90px; }
        .photo-grid-6-slot-4 { right: 90px; }
        .photo-grid-6-slot-5 { top: 270px; left: 90px; }
        .photo-grid-6-slot-6 { top: 270px; right: 90px; }

        .photo-slot {
            position: absolute;
            border: 2px dashed #FFFFFF;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        /*  */

        .slot-1 { top: 50px; left: 85px; }
        .slot-2 { top: 50px; right: 90px; }
        .slot-3 { left: 90px; }
        .slot-4 { right: 90px; }
        .slot-5 { top: 270px; left: 90px; }
        .slot-6 { top: 270px; right: 90px; }

        .frame-title {
            position: absolute;
            width: 155px;
            height: 20px;
            left: calc(50% - 155px/2);
            top: 640px;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
            color: #363636;
            text-align: center;
        }

        .frame-colors {
            position: absolute;
            width: 140px;
            height: 50px;
            left: calc(50% - 140px/2);
            top: 660px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .frame-title2 {
            position: absolute;
            width: 155px;
            height: 20px;
            left: calc(50% - 155px/2);
            top: 500px;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
            color: #363636;
            text-align: center;
        }

        .frame-colors2 {
            position: absolute;
            width: 140px;
            height: 50px;
            left: calc(50% - 140px/2);
            top: 530px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

         .frame-title3 {
            position: absolute;
            width: 155px;
            height: 20px;
            left: calc(50% - 155px/2);
            top: 470px;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
            color: #363636;
            text-align: center;
        }

        .frame-colors3 {
            position: absolute;
            width: 140px;
            height: 50px;
            left: calc(50% - 140px/2);
            top: 500px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .frame-title15 {
            position: absolute;
            width: 155px;
            height: 20px;
            left: calc(50% - 155px/2);
            top: 565px;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
            color: #363636;
            text-align: center;
        }

        .frame-colors15 {
            position: absolute;
            width: 140px;
            height: 50px;
            left: calc(50% - 140px/2);
            top: 590px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .color-option.selected {
            border: 2px solid #1759CA;
        }

        .color-white { background: #FFFFFF; border: 1px solid #ddd; }
        .color-black { background: #000000; }
        .color-blue { background: #1759CA; }

        .confirm-button {
            position: absolute;
            width: 320px;
            height: 50px;
            left: calc(50% - 320px/2);
            top: 720px;
            background: #CE004F;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
        }

        .confirm-button2 {
            position: absolute;
            width: 320px;
            height: 50px;
            left: calc(50% - 320px/2);
            top: 600px;
            background: #CE004F;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
        }
        .confirm-button3 {
            position: absolute;
            width: 320px;
            height: 50px;
            left: calc(50% - 320px/2);
            top: 580px;
            background: #CE004F;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
        }
        .confirm-button15 {
            position: absolute;
            width: 320px;
            height: 50px;
            left: calc(50% - 320px/2);
            top: 660px;
            background: #CE004F;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
        }
        /* PAGE 4 - Konfirmasi */
        .success-icon {
            position: absolute;
            width: 80px;
            height: 80px;
            left: calc(50% - 40px);
            top: 280px;
            background: #1759CA;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon::before {
            content: '✓';
            color: white;
            font-size: 35px;
            font-weight: bold;
        }

        .success-title {
            position: absolute;
            width: 300px;
            height: 60px;
            left: calc(50% - 150px);
            top: 380px;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 18px;
            line-height: 130%;
            text-align: center;
            color: #170F49;
        }

        .success-desc {
            position: absolute;
            width: 320px;
            height: 80px;
            left: calc(50% - 160px);
            top: 450px;
            font-family: 'Montserrat';
            font-weight: 400;
            font-size: 13px;
            line-height: 150%;
            text-align: center;
            color: rgba(40, 40, 40, 0.8);
        }

        .finish-button {
            position: absolute;
            width: 320px;
            height: 50px;
            left: calc(50% - 160px);
            top: 550px;
            background: #CE004F;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Montserrat';
            font-weight: 700;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        @include('pages.page1')
        @include('pages.page2')
        @include('pages.page4')
        @include('pages.page5')
        @include('grid.photo.grid1')
        @include('grid.photo.grid2')
        @include('grid.photo.grid3')
        @include('grid.photo.grid4')
        @include('grid.photo.grid6')
        @include('grid.photo.gede')
        @include('grid.photo.kecil')
        @include('grid.kunci.kotak')
        @include('grid.kunci.bulat')
        @include('grid.kunci.persegiPanjang')
        @include('grid.kunci.love')
        @include('grid.stikers.stikers')
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
    let currentPage = 1;
    let pageHistory = [];
    let selectedType = null;
    let name = '';
    let color = '';
    function setTypeAndGo(type, pageNum) {
        selectedType = type;
        goToPage(pageNum);
    }

    function goToPage(pageNum) {
        if (pageNum === currentPage) return;
        const currentPageEl = document.getElementById(`page${currentPage}`);
        const nextPageEl = document.getElementById(`page${pageNum}`);
        const backButtons = document.querySelectorAll('.back-button');

        // Add current page to history if moving forward
        if (pageNum > currentPage) {
            pageHistory.push(currentPage);
        }

        // Show/hide back button
        backButtons.forEach(btn => {
            btn.style.display = pageNum === 1 ? 'none' : 'flex';
        });

        // Animate transition
        if (pageNum > currentPage) {
            // Moving forward - slide left
            currentPageEl.classList.remove('active');
            currentPageEl.classList.add('slide-left');

            nextPageEl.classList.remove('slide-right');
            nextPageEl.classList.add('active');
        } else {
            // Moving backward - slide right
            currentPageEl.classList.remove('active');
            currentPageEl.classList.add('slide-right');

            nextPageEl.classList.remove('slide-left');
            nextPageEl.classList.add('active');

            // Clear history if going back to page 1
            if (pageNum === 1) {
                pageHistory = [];
                selectedType = null; // reset kalau balik ke awal
            }
        }

        currentPage = pageNum;
    }

    function goBack() {
        if (pageHistory.length > 0) {
            const prevPage = pageHistory.pop();
            goToPage(prevPage);
        }
    }

    function selectColor(element) {
        document.querySelectorAll('.color-option').forEach(option => {
            option.classList.remove('selected');
        });
        element.classList.add('selected');
        if (element.classList.contains('color-white')) color = 'putih';
        if (element.classList.contains('color-black')) color = 'hitam';
        if (element.classList.contains('color-blue')) color = 'biru';
    }
    async function loadKeychains() {
        try {
            const response = await fetch('/keychains');
            const keychains = await response.json();
            const container = document.getElementById('keychains-container-page5');
            container.innerHTML = '';

            const svgs = [
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`
            ];

            keychains.forEach((keychain, index) => {
                const keychainItem = document.createElement('div');
                keychainItem.className = `keychain-item keychain-${index+1}`;

                let targetPage = 3;
                if (keychain.id === 1) targetPage = 17;
                if (keychain.id === 2) targetPage = 15;
                if (keychain.id === 3) targetPage = 14;
                if (keychain.id === 4) targetPage = 16;

                keychainItem.setAttribute('onclick', `goToPage(${targetPage})`);

                keychainItem.innerHTML = `
                    <div class="keychain-item-bg"></div>
                    <div class="keychain-icon">${svgs[index] || ''}</div>
                    <div class="keychain-title">${keychain.title}</div>
                    <div class="keychain-arrow"></div>
                `;

                keychainItem.style.top = `${135 + (index * 100)}px`;

                container.appendChild(keychainItem);
            });
        } catch (error) {
            console.error('Gagal load keychains:', error);
        }
    }
    async function loadPhotoGrids() {
        try {
            const response = await fetch('/photo-grids');
            const grids = await response.json();

            const container = document.getElementById('grid-container-page2');
            container.innerHTML = '';
            const svgs = [
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`,
                `<svg>...</svg>`
            ];

            grids.forEach((grid, index) => {
                const gridItem = document.createElement('div');
                gridItem.className = `grid-item grid-${index+1}`;
                let targetPage = 3;
                if (grid.id === 1) targetPage = 3;
                if (grid.id === 2) targetPage = 10;
                if (grid.id === 3) targetPage = 11;
                if (grid.id === 4) targetPage = 12;
                if (grid.id === 5) targetPage = 13;
                gridItem.setAttribute('onclick', `goToPage(${targetPage})`);

                gridItem.innerHTML = `
                    <div class="grid-item-bg"></div>
                    <div class="grid-icon">${svgs[index] || ''}</div>
                    <div class="grid-title">${grid.title}</div>
                    <div class="grid-desc">${grid.description}</div>
                    <div class="grid-arrow"></div>
                `;

                gridItem.style.top = `${135 + (index * 100)}px`;

                container.appendChild(gridItem);
            });
        } catch (error) {
            console.error('Gagal load grids:', error);
        }
    }

    function getUserName() {
        const input = document.querySelector(`#page${currentPage} #username`);
        return input.value;
    }

   async function confirmSelection() {
        const name = getUserName();
        const captureElement = document.querySelector(`#page${currentPage} .capture-wrapper`);
        console.log('Nama:', name);
        if (!name) {
            alert("Masukkan nama dulu sebelum konfirmasi!");
            return;
        }
        if (!selectedType) {
            alert("Pilih tipe dulu sebelum konfirmasi!");
            return;
        }

        try {
            const canvas = await html2canvas(captureElement, {
                allowTaint: true,
                useCORS: true
            });

            const layoutImage = canvas.toDataURL("image/png");
            console.log('layoutImage:', layoutImage);
            console.log('selectedType:', selectedType);

            const response = await fetch('/photo-orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    name: name,
                    type: selectedType,
                    layout_image: layoutImage,
                    warna: color,
                })
            });

            const text = await response.text(); // ambil dulu text
            try {
                const result = JSON.parse(text); // coba parse JSON
                if (response.ok) {
                    console.log("Data berhasil disimpan:", result);
                    goToPage(4);
                } else {
                    alert("Gagal menyimpan data: " + (result.message || text));
                }
            } catch (err) {
                console.error('Response bukan JSON:', text);
                alert('Terjadi error: Response bukan JSON. Cek console untuk detail.');
            }

        } catch (error) {
            console.error("Error saat generate layout_image atau menyimpan:", error);
            alert("Terjadi kesalahan saat menyimpan data.");
        }
    }
    // reset form
    function resetForm() {
        // Reset semua input text
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.value = '';
        });

        // Reset warna (balik ke default = black misalnya)
        document.querySelectorAll('.color-option').forEach(option => {
            option.classList.remove('selected');
        });
        document.querySelectorAll('.color-black').forEach(option => {
            option.classList.add('selected');
        });

        // Reset variable global
        selectedType = null;
        color = '';
        name = '';
        pageHistory = [];
        goToPage(1);
    }
    // Initialize back buttons
    document.addEventListener('DOMContentLoaded', function() {
        const backButtons = document.querySelectorAll('.back-button');
        backButtons.forEach(btn => {
            btn.style.display = 'none';
        });
        loadPhotoGrids();
        loadKeychains();
    });
</script>
</body>
</html>
