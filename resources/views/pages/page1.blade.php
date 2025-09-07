 <!-- PAGE 1: Pilih Layout Foto -->
        <div class="page page1 active" id="page1">
            <div class="header-rect"></div>
            <div class="content-bg"></div>
            <div class="logo">SF</div>
            <div class="header-text">Silahkan Pilih<br>Paket Cetak Kamu</div>

            <div class="nav-header-bg"></div>
            <div class="nav-back-btn" onclick="goBack()"></div>
            <div class="nav-title" onclick="setTypeAndGo('layout_photo', 2)">
                Pilih Layout Foto
                <span class="nav-arrow">›</span>
            </div>

            <!-- Layout Cards -->
            <div class="layout-card card-1" onclick="setTypeAndGo('gantungan_kunci', 5)">
                <div class="layout-card-yellow"></div>
            </div>
            <div class="layout-card card-2" onclick="setTypeAndGo('stiker', 9)">
                <div class="layout-card-yellow"></div>
            </div>
            <div class="layout-card card-3" onclick="setTypeAndGo('photostrip_kicik', 8)">
                <div class="layout-card-yellow"></div>
            </div>
            <div class="layout-card card-4" onclick="setTypeAndGo('photostrip_gede', 7)">
                <div class="layout-card-yellow"></div>
            </div>

            <!-- Labels -->
            <div class="card-label label-1" onclick="setTypeAndGo('gantungan_kunci', 5)">
                <div>Tambah<br>Gantungan Kunci</div>
                <span class="card-arrow">›</span>
            </div>
            <div class="card-label label-2" onclick="setTypeAndGo('stiker', 9)">
                <div>Tambah<br>Stiker</div>
                <span class="card-arrow">›</span>
            </div>
            <div class="card-label label-3" onclick="setTypeAndGo('photostrip_kicik', 8)">
                <div>Cetak<br>Photostrip Kicik</div>
                <span class="card-arrow">›</span>
            </div>
            <div class="card-label label-4" onclick="setTypeAndGo('photostrip_gede', 7)">
                <div>Cetak<br>Photostrip GEDE</div>
                <span class="card-arrow">›</span>
            </div>

            <div class="footer">Snap Fun Studio by One Dream Creative</div>
        </div>
