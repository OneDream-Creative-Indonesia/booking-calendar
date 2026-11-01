<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; background: #fff; margin: 0 auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .header { background-color: #CE004F; color: white; padding: 20px; border-radius: 12px 12px 0 0; margin: -30px -30px 20px -30px; }
        .queue-number { font-size: 36px; font-weight: 900; color: #CE004F; margin: 10px 0; }
        .qr-box { margin: 25px auto; padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; display: inline-block; }
        .btn { display: inline-block; background-color: #004fbf; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; margin-top: 15px; }
        .footer { margin-top: 30px; font-size: 12px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">Terima Kasih, {{ $data['nama'] }}!</h2>
        </div>
        
        <p>Pendaftaran antrean photobooth kamu berhasil dicatat.</p>
        <p style="margin-bottom: 0;">Nomor Antrean Kamu:</p>
        <div class="queue-number">{{ $data['queue_number'] }}</div>

        <div class="qr-box">
            <p style="margin: 0 0 10px 0; font-size: 13px; font-weight: bold; color: #4b5563; text-transform: uppercase;">QR Code Hasil Foto</p>
            <!-- Menggenerate QR Code Otomatis Menggunakan API -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data={{ urlencode($data['drive_link']) }}" alt="QR Code Galeri" width="180" height="180" style="border-radius: 8px;">
        </div>

        <p>Simpan email ini. Kamu bisa mengakses galeri foto kamu melalui link di bawah ini setelah sesi foto selesai:</p>
        <a href="{{ $data['drive_link'] }}" class="btn">Buka Galeri Foto</a>

        <div class="footer">
            ID Folder: <strong>{{ $data['album_id'] }}</strong><br>
            &copy; {{ date('Y') }} Snapfun Studio
        </div>
    </div>
</body>
</html>