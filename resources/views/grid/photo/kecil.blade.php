<!--Ketik Nomor Photo kecil-->
        <div class="page page8 slide-right" id="page8">
            <div class="header-rect"></div>
            <div class="content-bg"></div>
            <div class="logo">SF</div>
            <div class="back-button" onclick="goBack()">←</div>
            <div class="header-text">Pilih Foto</div>

            <div class="photo-title">Ketik Nomor Foto</div>
                <div class="photo-grid8 capture-wrapper" id="photo-grid-capture ">
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-1" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-2" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-3" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-4" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-5" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-6" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-7" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-8" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-9" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-10" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-11" placeholder="" />
                    <input type="text" class="photo-grid-input-kecil photo-kecil-slot-12" placeholder="" />
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
