<head>
    <!-- Tambahkan CSS Filament -->
    <link rel="stylesheet" href="https://unpkg.com/@filament/forms@3.0/dist/styles.css">

    <!-- Tambahkan Alpine.js yang diperlukan oleh Filament -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Tambahkan Tailwind dari Laravel jika digunakan -->
    @vite('resources/css/app.css')
</head>
<body>
    {{ $slot }}

    <!-- Tambahkan JavaScript Filament -->
    <script src="https://unpkg.com/@filament/forms@3.0/dist/scripts.js"></script>
</body>
