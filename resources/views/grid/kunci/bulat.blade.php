<!--Ketik Nomor Photo/Pilih Foto Grid 1-->
        <div class="page page14 slide-right" id="page14">
            <div class="header-rect"></div>
            <div class="content-bg"></div>
            <div class="logo"><svg width="25" height="30" viewBox="0 0 25 30" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0 16.8704V11.1558H10.0132C10.0132 19.9397 9.98614 12.7707 9.98614 16.5138C9.98614 19.5439 10.9328 21.239 12.6116 21.239C14.0325 21.239 14.8494 20.3915 14.8494 18.8774V11.1576H24.7508V19.1462C24.7508 25.7421 19.8857 29.9763 12.2672 29.9763C4.2177 29.9763 0 25.4751 0 16.8742V16.8704Z" fill="white"/>
<path d="M7.31563 8.37942C9.49163 8.37942 11.2556 6.55309 11.2556 4.30019C11.2556 2.04729 9.49163 0.220947 7.31563 0.220947C5.13962 0.220947 3.37563 2.04729 3.37563 4.30019C3.37563 6.55309 5.13962 8.37942 7.31563 8.37942Z" fill="#FEDD03"/>
<path d="M19.9938 8.59807C19.7342 8.59807 19.4727 8.52712 19.2401 8.37777L15.4966 5.98063C14.898 5.59791 14.5409 4.97063 14.5409 4.30413C14.5409 3.63764 14.8962 3.01035 15.493 2.62763L19.2383 0.22116C19.8748 -0.187697 20.7313 -0.0140718 21.1497 0.613216C21.5662 1.23864 21.3877 2.07875 20.7512 2.48948L17.9291 4.30226L20.7494 6.10758C21.3877 6.51644 21.568 7.35656 21.1515 7.98385C20.8864 8.3815 20.4446 8.59807 19.9938 8.59807Z" fill="#FEDD03"/>
</svg>
</div>
            <div class="back-button" onclick="goBack()">←</div>
            <div class="header-text">Pilih Foto</div>

            <div class="photo-title">Ketik Nomor Foto</div>
                <div class="photo-grid14 capture-wrapper" id="photo-grid-capture">
                    <input type="text" class="photo-grid-input keychain-slot-1" placeholder="" />
                </div>
              <div class="user-input">
                <label for="username">Masukkan Nama Kamu:</label>
                <input type="text" id="username" placeholder="Contoh: Dedik" />
            </div>
            <div class="frame-title">Pilih Warna Frame</div>
            <div class="frame-colors">
                <div class="color-option color-white" onclick="selectColor(this)"></div>
                <div class="color-option color-black selected" onclick="selectColor(this)"></div>
                <div class="color-option color-blue" onclick="selectColor(this)"></div>
            </div>

            <button class="confirm-button" onclick="confirmSelection()">Konfirmasi</button>
            <div class="footer">Snap Fun Studio by One Dream Creative</div>
        </div>
