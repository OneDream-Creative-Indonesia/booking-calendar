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
    max-width: 100vw;
    height: 100vh;
    overflow: hidden;
}

.page {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    display: none;
    overflow-y:auto;
}

.page.active {
    display: block; /* hanya yang aktif ditampilin */
    transform: translateX(0) !important;
}

body {
    font-family: 'Montserrat', sans-serif;
    background-color: #FFFFFF;
    overflow: hidden;
}

/* Container untuk semua halaman */
.app-container {
    position: relative;
    width: 100%;
    max-width: 100vw;
    height: 100vh;
    margin: 0 auto;
    overflow: hidden;
}

.page {
    position: absolute;
    width: 100%;
    height: 100vh;
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
    width: 100%;
    height: 131px;
    left: 0px;
    top: 0px;
    background: #1759CA;
}

.content-bg {
    position: absolute;
    width: 100%;
    height: 100vh;
    left: 0px;
    top: 108px;
    background: #ffffff;
    border-radius: 20px 20px 0px 0px;
}

.logo {
    position: absolute;
    left: calc(50% - 24.75px/2 - 133px);
    top: 39px;
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
    bottom: 22px;
    font-family: 'Montserrat';
    font-style: normal;
    font-weight: 900;
    font-size: 10px;
    line-height: 114%;
    text-align: center;
    color: #282828;
    top: 760px;
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

/* User Input Styling - Responsive positioning */
.user-input {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    left: 50%;
    transform: translateX(-50%);
    bottom: 200px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    color: #282828;
    top: 510px;
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
    width: calc(100% - 55px);
    max-width: 320px;
    left: 50%;
    transform: translateX(-50%);
    top: 380px;
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
    width: calc(100% - 55px);
    max-width: 320px;
    left: 50%;
    transform: translateX(-50%);
    top: 380px;
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
    width: calc(100% - 55px);
    max-width: 320px;
    left: 50%;
    transform: translateX(-50%);
    top: 380px;
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
    right: 25px;
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
    width: calc(100% - 50px);
    max-width: 370px;
    height: 88px;
    left: 50%;
    transform: translateX(-50%);
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
    background-image: url('/img/group(5).png');
    background-size: cover;
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
    width: calc(50% - 30px);
    max-width: 151px;
    height: 178px;
    background: #FFFFFF;
    border: 1px solid #E0E0E0;
    border-radius: 10px;
    cursor: pointer;
    overflow: hidden;
}
.layout-card-yellow {
    position: absolute;
    width: 100%;
    height: 101px;
    background-image: url('/img/group(1).png');
    background-size: cover;
    border-radius: 10px 10px 0px 0px;
    top: 0;
}
.layout-card-yellow2 {
    position: absolute;
    width: 100%;
    height: 101px;
    background-image: url('/img/group(2).png');
    background-size: cover;
    border-radius: 10px 10px 0px 0px;
    top: 0;
}
.layout-card-yellow3 {
    position: absolute;
    width: 100%;
    height: 101px;
    background-image: url('/img/group(3).png');
    background-size: cover;
    border-radius: 10px 10px 0px 0px;
    top: 0;
}
.layout-card-yellow4 {
    position: absolute;
    width: 100%;
    height: 101px;
    background-image: url('/img/group(4).png');
    background-size: cover;
    border-radius: 10px 10px 0px 0px;
    top: 0;
}

.card-1 {
    left: 25px;
    top: 265px;
}
.card-2 {
    right: 25px;
    top: 265px;
}
.card-3 {
    left: 25px;
    top: 465px;
}
.card-4 {
    right: 25px;
    top: 465px;
}

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

.label-1 {
    width: calc(50% - 30px);
    max-width: 151px;
    height: 77px;
    left: 25px;
    top: 366px;
}
.label-2 {
    width: calc(50% - 30px);
    max-width: 151px;
    height: 77px;
    right: 25px;
    top: 366px;
}
.label-3 {
    width: calc(50% - 30px);
    max-width: 151px;
    height: 77px;
    left: 25px;
    top: 566px;
}
.label-4 {
    width: calc(50% - 30px);
    max-width: 151px;
    height: 77px;
    right: 25px;
    top: 566px;
}

.card-arrow {
    color: #CE004F;
    font-size: 16px;
    font-weight: bold;
    margin-left: 5px;
}

/* PAGE 2 - Logo posisi kanan */
.page2 .logo {
    right: 25px;
    left: auto;
}

.page2 .header-text {
    position: absolute;
    width: 93px;
    height: 20px;
    left: 50%;
    transform: translateX(-50%);
    top: 44px;
    font-family: 'Montserrat';
    font-weight: 500;
    font-size: 20px;
    line-height: 99%;
    color: #FFFFFF;
}

.keychain-item {
    position: absolute;
    width: calc(100% - 48px);
    max-width: 327px;
    height: 90px;
    left: 50%;
    transform: translateX(-50%);
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
    top: 25px;
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
    width: calc(100% - 48px);
    max-width: 327px;
    height: 90px;
    left: 50%;
    transform: translateX(-50%);
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

/* PAGE 3 & 6+ - Logo posisi kanan */
.page3 .logo, .page6 .logo, .page7 .logo, .page8 .logo, .page9 .logo,
 .page10 .logo, .page11 .logo, .page12 .logo, .page13 .logo,
  .page14 .logo, .page15 .logo, .page16 .logo, .page17 .logo {
    right: 25px;
    left: auto;
}

.page3 .header-text, .page6 .header-text, .page7 .header-text,
.page8 .header-text, .page9 .header-text, .page10 .header-text,
.page11 .header-text, .page12 .header-text, .page13 .header-text,
.page14 .header-text, .page15 .header-text, .page16 .header-text,
.page17 .header-text {
    position: absolute;
    width: 96px;
    height: 20px;
    left: 50%;
    transform: translateX(-50%);
    top: 44px;
    font-family: 'Montserrat';
    font-weight: 500;
    font-size: 20px;
    line-height: 99%;
    color: #FFFFFF;
}

/* PAGE 4 - Logo posisi tengah */
.page4 .logo {
    left: 50%;
    transform: translateX(-50%);
}

/* PAGE 5 - Logo posisi kanan */
.page5 .logo {
    right: 25px;
    left: auto;
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
    left: 50%;
    transform: translateX(-50%);
    top: 140px;
    font-family: 'Montserrat';
    font-weight: 700;
    font-size: 16px;
    line-height: 99%;
    color: #282828;
    text-align: center;
}
.photo-grid9{
    position: relative;
    left: 50%;
    width: 80%;
    max-width: 300px;
    height: 350px;
    transform: translateX(-50%);
    top: 160px;
    background-size: 65%;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.photo-grid17{
    position: relative;
    left: 50%;
    width: 80%;
    max-width: 300px;
    height: 350px;
    transform: translateX(-50%);
    top: 130px;
    background-size: 65%;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}
 .photo-grid15{
    position: relative;
    left: 50%;
    width: 80%;
    max-width: 300px;
    height: 350px;
    transform: translateX(-50%);
    top: 100px;
    background-size: 65%;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
 }
/* Photo Grid Responsive Containers */
.photo-grid7, .photo-grid8, .photo-grid14,
.photo-grid16, .photo-grid1, .photo-grid2, .photo-grid3,
.photo-grid4, .photo-grid6 {
    position: relative;
    left: 50%;
    width: 80%;
    max-width: 300px;
    height: 350px;
    transform: translateX(-50%);
    top: 170px;
    background-size: 65%;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Specific grid backgrounds */
.photo-grid9 { background-image: url('img/grid/stikers.png'); }
.photo-grid7 { background-image: url('img/grid/gede.png'); }
.photo-grid8 { background-image: url('img/grid/kecil.png'); }
.photo-grid14 { background-image: url('img/grid/kunci/Bulet.png'); }
.photo-grid15 { background-image: url('img/grid/kunci/Kotak.png'); }
.photo-grid16 { background-image: url('img/grid/kunci/Love.png'); }
.photo-grid17 {
    background-image: url('img/grid/kunci/persegiPanjang.png');
    height: 400px;
}
.photo-grid1 { background-image: url('img/grid/photo/grid(1).png'); }
.photo-grid2 {
    background-image: url('img/grid/photo/grid(2).png');
    height: 200px;
}
.photo-grid3 {
    background-image: url('img/grid/photo/grid(3).png');
    height: 200px;
}
.photo-grid4 { background-image: url('img/grid/photo/grid(4).png'); }
.photo-grid6 { background-image: url('img/grid/photo/grid(6).png'); }

/* Input field styles for all grids */
.keychain-slot-love{
    top: 150px;
}
.keychain-slot-persegiPanjang{
    top: 200px;
}
.photo-grid-input-stikers, .photo-grid-input, .photo-grid-input-kecil,
.keychain-slot-1, .photo-grid-1, .photo-grid-2, .photo-grid-3,
.photo-grid-4, .photo-grid-6 {
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

/* Photo slot positioning (keeping existing relative positions) */
.photo-stikers-slot-1 { top: 100px; left: 62px; }
.photo-stikers-slot-2 { top: 100px; right: 62px; }
.photo-stikers-slot-3 { top: 65px; left: 115px; }
.photo-stikers-slot-4 { bottom: 145px; left: 62px; }
.photo-stikers-slot-5 { bottom: 145px; right: 57px; }
.photo-stikers-slot-6 { bottom: 145px; right: 115px; }
.photo-stikers-slot-7 { bottom: 65px; left: 62px; }
.photo-stikers-slot-8 { bottom: 65px; right: 57px; }
.photo-stikers-slot-9 { bottom: 65px; right: 115px; }

.photo-gede-slot-1 { top: 70px; left: 60px; }
.photo-gede-slot-2 { top: 70px; right: 60px; }
.photo-gede-slot-3 { left: 60px; }
.photo-gede-slot-4 { right: 60px; }
.photo-gede-slot-5 { top: 245px; left: 60px; }
.photo-gede-slot-6 { top: 245px; right: 60px; }

.photo-kecil-slot-1 { top: 50px; left: 50px; }
.photo-kecil-slot-2 { top: 50px; right: 120px; }
.photo-kecil-slot-3 { top: 50px; right: 60px; }
.photo-kecil-slot-4 { top: 125px; left: 50px; }
.photo-kecil-slot-5 { top: 125px; left: 110px; }
.photo-kecil-slot-6 { top: 125px; right: 60px; }
.photo-kecil-slot-7 { bottom: 115px; left: 50px; }
.photo-kecil-slot-8 { bottom: 115px; right: 120px; }
.photo-kecil-slot-9 { bottom: 115px; right: 60px; }
.photo-kecil-slot-10 { bottom: 40px; left: 50px; }
.photo-kecil-slot-11 { bottom: 40px; right: 120px; }
.photo-kecil-slot-12 { bottom: 40px; right: 60px; }

.photo-grid-2-slot-1 { top: 80px; left: 55px; }
.photo-grid-2-slot-2 { top: 80px; right: 55px; }

.photo-grid-3-slot-1 { top: 80px; left: 45px; }
.photo-grid-3-slot-2 { top: 80px; }
.photo-grid-3-slot-3 { top: 80px; right: 40px; }

.photo-grid-4-slot-1 { top: 90px; left: 60px; }
.photo-grid-4-slot-2 { top: 90px; right: 60px; }
.photo-grid-4-slot-3 { bottom: 105px; left: 60px; }
.photo-grid-4-slot-4 { bottom: 105px; right: 60px; }

.photo-grid-6-slot-1 { top: 70px; left: 60px; }
.photo-grid-6-slot-2 { top: 70px; right: 60px; }
.photo-grid-6-slot-3 { left: 62px; }
.photo-grid-6-slot-4 { right: 62px; }
.photo-grid-6-slot-5 { top: 240px; left: 60px; }
.photo-grid-6-slot-6 { top: 240px; right: 60px; }

.photo-slot {
    position: absolute;
    border: 2px dashed #FFFFFF;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
}

.slot-1 { top: 50px; left: 85px; }
.slot-2 { top: 50px; right: 90px; }
.slot-3 { left: 90px; }
.slot-4 { right: 90px; }
.slot-5 { top: 270px; left: 90px; }
.slot-6 { top: 270px; right: 90px; }

/* Frame Title and Colors - Responsive */
.frame-title {
    position: absolute;
    width: 155px;
    height: 20px;
    left: 50%;
    transform: translateX(-50%);
    bottom: 120px;
    font-family: 'Montserrat';
    font-weight: 700;
    font-size: 16px;
    color: #363636;
    text-align: center;
    top: 600px;
}

.frame-colors {
    position: absolute;
    width: 140px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
    bottom: 100px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    top: 620px;
}

.frame-title2 {
    position: absolute;
    width: 155px;
    height: 20px;
    left: 50%;
    transform: translateX(-50%);
    top: 470px;
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
    left: 50%;
    transform: translateX(-50%);
    top: 490px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.frame-title3 {
    position: absolute;
    width: 155px;
    height: 20px;
    left: 50%;
    transform: translateX(-50%);
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
    left: 50%;
    transform: translateX(-50%);
    top: 490px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.frame-title15 {
    position: absolute;
    width: 155px;
    height: 20px;
    left: 50%;
    transform: translateX(-50%);
    top: 470px;
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
    left: 50%;
    transform: translateX(-50%);
    top: 490px;
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
    border: 2px solid #FEDD03;
}

.color-white { background: #FFFFFF; border: 1px solid #ddd; }
.color-black { background: #000000; }
.color-blue { background: #1759CA; }

/* Confirm Buttons - Responsive */
.confirm-button {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
    bottom: 40px;
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
    top: 680px;
}
.confirm-button17 {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
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
.confirm-button9 {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
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

.confirm-button2 {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
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

.confirm-button3 {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
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

.confirm-button15 {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
    top: 460px;
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
.confirm-button14 {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
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
.confirm-button16 {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
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
/* PAGE 4 - Konfirmasi - Responsive */
.success-icon {
    position: absolute;
    width: 80px;
    height: 80px;
    left: 50%;
    transform: translateX(-50%);
    top: 40%;
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
    width: calc(100% - 75px);
    max-width: 300px;
    height: 60px;
    left: 50%;
    transform: translateX(-50%);
    top: calc(40% + 100px);
    font-family: 'Montserrat';
    font-weight: 700;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    color: #170F49;
}

.success-desc {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 80px;
    left: 50%;
    transform: translateX(-50%);
    top: calc(40% + 170px);
    font-family: 'Montserrat';
    font-weight: 400;
    font-size: 13px;
    line-height: 150%;
    text-align: center;
    color: rgba(40, 40, 40, 0.8);
}

.finish-button {
    position: absolute;
    width: calc(100% - 55px);
    max-width: 320px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
    bottom: 100px;
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

/* Media Queries for better mobile responsiveness */
@media screen and (max-width: 480px) {
    .logo {
        font-size: 7px;
    }

    .nav-header-bg {
        width: calc(100% - 30px);
    }

    .layout-card, .card-label {
        width: calc(50% - 20px);
        max-width: 140px;
    }

    .card-1, .label-1 { left: 40px; }
    .card-2, .label-2 { right: 40px; }
    .card-3, .label-3 { left: 40px; }
    .card-4, .label-4 { right: 40px; }

    .keychain-item, .grid-item {
        width: calc(100% - 30px);
    }
    .photo-grid7{
        max-width: 300px;
    }
    .photo-grid8{
        max-width: 380px;
    }
    .photo-grid9{
        max-width: 400px;
    }
    .photo-grid17{
        max-width: 300px;
    }
    ..photo-grid14{
        max-width: 300px;
    }
    .photo-grid15, .photo-grid1,
    .photo-grid2, .photo-grid3, {
        width: 90%;
        max-width: 280px;
    }

    .user-input, .user-input2, .user-input3, .user-input15 {
        width: calc(100% - 40px);
    }

    .confirm-button, .confirm-button2, .confirm-button3,
    .confirm-button15, .finish-button {
        width: calc(100% - 40px);
    }
}

@media screen and (max-width: 360px) {
    .header-text {
        font-size: 14px !important;
    }

    .nav-title {
        font-size: 18px;
    }

    .card-label {
        font-size: 12px;
    }

    .keychain-title, .grid-title {
        font-size: 14px;
    }

    .photo-title {
        font-size: 14px;
    }
}
@media screen and (max-width: 430px) {
    .layout-card, .card-label {
        width: calc(50% - 25px);
        max-width: 145px;
    }
    .card-1, .label-1 { left: 30px; }
    .card-2, .label-2 { right: 30px; }
    .card-3, .label-3 { left: 30px; }
    .card-4, .label-4 { right: 30px; }
}

/* iPhone 14/15 Plus - 414px */
@media screen and (max-width: 414px) {
    .layout-card, .card-label {
        width: calc(50% - 24px);
        max-width: 142px;
    }
    .card-1, .label-1 { left: 28px; }
    .card-2, .label-2 { right: 28px; }
    .card-3, .label-3 { left: 28px; }
    .card-4, .label-4 { right: 28px; }
}

/* iPhone 12/13/14/15 - 390px */
@media screen and (max-width: 390px) {
    .layout-card, .card-label {
        width: calc(50% - 22px);
        max-width: 138px;
    }
    .card-1, .label-1 { left: 25px; }
    .card-2, .label-2 { right: 25px; }
    .card-3, .label-3 { left: 25px; }
    .card-4, .label-4 { right: 25px; }
}

/* iPhone X/XS/11 Pro - 375px */
@media screen and (max-width: 375px) {
    .layout-card, .card-label {
        width: calc(50% - 20px);
        max-width: 135px;
    }
    .card-1, .label-1 { left: 22px; }
    .card-2, .label-2 { right: 22px; }
    .card-3, .label-3 { left: 22px; }
    .card-4, .label-4 { right: 22px; }
}

/* Samsung Galaxy A series, Pixel 6a - 360px */
@media screen and (max-width: 360px) {
    .layout-card, .card-label {
        width: calc(50% - 18px);
        max-width: 130px;
    }
    .card-1, .label-1 { left: 20px; }
    .card-2, .label-2 { right: 20px; }
    .card-3, .label-3 { left: 20px; }
    .card-4, .label-4 { right: 20px; }

    .card-label {
        font-size: 12px;
        padding: 8px;
    }
}

/* Device kecil seperti iPhone SE - 320px */
@media screen and (max-width: 320px) {
    .layout-card, .card-label {
        width: calc(50% - 15px);
        max-width: 120px;
    }
    .card-1, .label-1 { left: 15px; }
    .card-2, .label-2 { right: 15px; }
    .card-3, .label-3 { left: 15px; }
    .card-4, .label-4 { right: 15px; }

    .card-label {
        font-size: 11px;
        padding: 6px;
    }
}

/* Untuk device Android yang sangat beragam */
@media screen and (min-width: 361px) and (max-width: 413px) {
    /* Android mid-range */
    .layout-card, .card-label {
        width: calc(50% - 23px);
        max-width: 140px;
    }
    .card-1, .label-1 { left: 26px; }
    .card-2, .label-2 { right: 26px; }
    .card-3, .label-3 { left: 26px; }
    .card-4, .label-4 { right: 26px; }
}

/* Untuk device dengan layar sangat lebar */
@media screen and (min-width: 431px) {
    .layout-card, .card-label {
        width: calc(50% - 35px);
        max-width: 160px;
    }
    .card-1, .label-1 { left: 35px; }
    .card-2, .label-2 { right: 35px; }
    .card-3, .label-3 { left: 35px; }
    .card-4, .label-4 { right: 35px; }
}

/* Landscape mode - untuk device yang dirotate */
@media screen and (orientation: landscape) and (max-height: 500px) {
    .layout-card, .card-label {
        width: calc(50% - 40px);
        max-width: 180px;
    }
    .card-1, .label-1 { left: 40px; }
    .card-2, .label-2 { right: 40px; }
    .card-3, .label-3 { left: 40px; }
    .card-4, .label-4 { right: 40px; }
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
                `<img src="/img/persegiPanjang.png" width="38" height="55" alt="Grid 1" />`,
                `<img src="/img/kotak.png" width="38" height="55" alt="Grid 2" />`,
                `<img src="/img/bulet.png" width="38" height="55" alt="Grid 3" />`,
                `<img src="/img/love.png" width="38" height="55" alt="Grid 4" />`,
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
                `<img src="/img/grid(1).png" width="50" height="45" alt="Grid 1" />`,
                `<img src="/img/grid(2).png" width="50" height="45" alt="Grid 2" />`,
                `<img src="/img/grid(3).png" width="50" height="45" alt="Grid 3" />`,
                `<img src="/img/grid(4).png" width="50" height="45" alt="Grid 4" />`,
                `<img src="/img/grid(6).png" width="50" height="45" alt="Grid 6" />`,
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

                gridItem.style.position = 'absolute';
                gridItem.style.left = '50%';
                gridItem.style.transform = 'translateX(-50%)';
                gridItem.style.top = `${150 + (index * 120)}px`;

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
        if (!name) {
            alert("Masukkan nama dulu sebelum konfirmasi!");
            return;
        }
        if (!selectedType) {
            alert("Pilih tipe dulu sebelum konfirmasi!");
            return;
        }
         if (selectedType === "stiker" || selectedType === "gantungan_kunci") {
            color = null;
        }
        try {
            const canvas = await html2canvas(captureElement, {
                allowTaint: true,
                useCORS: true
            });

            const layoutImage = canvas.toDataURL("image/png");

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
