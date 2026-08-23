<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Google Drive</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; }
        .folder-item { padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .alert-success { background: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; margin-bottom: 16px; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h2>Setup Google Drive</h2>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if (!$connected)
        <p>Akun Google Drive belum terhubung.</p>
        <a href="{{ route('gdrive.connect') }}" class="btn">Hubungkan ke Google Drive</a>
    @else
        <p>✅ Akun Google Drive sudah terhubung.</p>
        <p><a href="{{ route('gdrive.connect') }}">Hubungkan ulang / ganti akun</a></p>

        <h3>Pilih Folder Utama untuk Upload</h3>
        @if (count($folders) === 0)
            <p>Tidak ada folder ditemukan di root Drive kamu.</p>
        @else
            @foreach ($folders as $folder)
                <form method="POST" action="{{ route('gdrive.save-folder') }}" class="folder-item">
                    @csrf
                    <span>📁 {{ $folder['name'] }}</span>
                    <input type="hidden" name="folder_id" value="{{ $folder['id'] }}">
                    <input type="hidden" name="folder_name" value="{{ $folder['name'] }}">
                    <button type="submit" class="btn">Pilih</button>
                </form>
            @endforeach
        @endif
    @endif
</body>
</html>