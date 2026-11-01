<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; background: #fff; margin: 0 auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .header { background-color: #004fbf; color: white; padding: 20px; border-radius: 12px 12px 0 0; margin: -30px -30px 20px -30px; }
        .detail-box { text-align: left; background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e5e7eb; }
        .qr-box { margin: 25px auto; padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; display: inline-block; }
        .footer { margin-top: 30px; font-size: 12px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">Konfirmasi Booking Studio</h2>
        </div>
        
        <p>Halo <strong>{{ $data['nama'] }}</strong>, pendaftaran booking studio kamu telah berhasil dicatat.</p>
        
        <div class="detail-box">
            <p style="margin: 5px 0;"><strong>Jadwal:</strong> {{ $data['tanggal'] }} | {{ $data['waktu'] }}</p>
            <p style="margin: 5px 0;"><strong>Paket:</strong> {{ $data['paket_nama'] }}</p>
        </div>

        <div class="qr-box">
            <p style="margin: 0 0 10px 0; font-size: 13px; font-weight: bold; color: #4b5563; text-transform: uppercase;">QR Code Hasil Foto</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data={{ urlencode($data['drive_link']) }}" alt="QR Code Galeri" width="180" height="180" style="border-radius: 8px;">
        </div>

        <p>Simpan email ini. Kamu bisa mengakses galeri foto kamu melalui link di bawah ini setelah sesi foto selesai:</p>
        <a href="{{ $data['drive_link'] }}" style="display: inline-block; background-color: #004fbf; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: bold; margin-top: 15px; border: 1px solid #003a8c;">
            <span style="color: #ffffff;">Buka Galeri Foto</span>
        </a>

        <div class="footer">
            ID Folder: <strong>{{ $data['album_id'] }}</strong><br>
            &copy; {{ date('Y') }} Snapfun Studio
        </div>
    </div>
</body>
</html>