<!--Ketik Nomor Photo/Pilih Foto Grid 1-->
        <div class="page page11 slide-right" id="page11">
            <div class="header-rect"></div>
            <div class="content-bg"></div>
            <div class="logo">SF</div>
            <div class="back-button" onclick="goBack()">←</div>
            <div class="header-text">Pilih Foto</div>

            <div class="photo-title">Ketik Nomor Foto</div>
                <div class="photo-grid3 capture-wrapper" id="photo-grid-capture">
                    <input type="text" class="photo-grid-input photo-grid-3-slot-1" placeholder="" />
                    <input type="text" class="photo-grid-input photo-grid-3-slot-2" placeholder="" />
                    <input type="text" class="photo-grid-input photo-grid-3-slot-3" placeholder="" />
                </div>
              <div class="user-input3">
                <label for="username">Masukkan Nama Kamu:</label>
                <input type="text" id="username" placeholder="Contoh: Dedik" />
            </div>
            <div class="frame-title3">Pilih Warna Frame</div>
            <div class="frame-colors3">
                <div class="color-option color-white" onclick="selectColor(this)"></div>
                <div class="color-option color-black selected" onclick="selectColor(this)"></div>
                <div class="color-option color-blue" onclick="selectColor(this)"></div>
            </div>

            <button class="confirm-button3" onclick="confirmSelection()">Konfirmasi</button>
            <div class="footer">Snap Fun Studio by One Dream Creative</div>
        </div>
