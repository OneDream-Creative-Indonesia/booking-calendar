<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title> Snap Fun by One Dream - Pop Up Self Photo</title>

        <!-- ========== Favicon Icon ========== -->
        <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @filamentStyles
        @vite('resources/css/app.css')

    </head>

    <body class="antialiased">

            <div class="fixed-bg" style="background-image: url(../../assets/img/shape/bg-4.png);"
            <!-- Fixed BG -->
            <div class="container mx-auto">
                <div class="py-20 lg:flex lg:justify-center lg:items-center">
                    <div class="lg:w-8/12">
                        <h1 class="mb-4 text-4xl font-bold text-white text-center ">Pop Up Self Photo by Snap Fun</h1>
                    </div>
                </div>
            </div>
        </div>

        {{ $slot }}

        @filamentScripts
        @vite('resources/js/app.js')

        <footer class="text-white" style="background-color: #1759CA">
            <div class="container px-4 py-8 mx-auto">
                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 mb-4 md:w-1/4 md:mb-0">
                        <img src="../../assets/img/logo one dream putih.svg" alt="Logo" class="w-32">
                    </div>
                    <div class="w-full px-4 mb-4 md:w-1/4 md:mb-0">
                        <h4 class="mb-4 text-lg font-semibold">Studio Kami</h4>
                        <ul>
                            <li class="flex items-center mb-2">
                                <img src="../../assets/img/Icon/placeholder.png" class="h-8 mr-2" alt="Location">
                                <span>Ruko Sukses 2, Sumur Pecung - Kota Serang Banten </span>
                            </li>
                        </ul>
                    </div>
                    <div class="w-full px-4 mb-4 md:w-1/4 md:mb-0">
                        <h4 class="mb-4 text-lg font-semibold">Kontak Kami</h4>
                        <ul>
                            <li class="flex items-center mb-2">
                                <img src="../../assets/img/Icon/mail.png" class="h-8 mr-2" alt="Email">
                                <a href="mailto:hello@onedream.id" class="hover:text-gray-300">snapfunstudio@gmail.com</a>
                            </li>
                            <li class="flex items-center mb-2">
                                <img src="../../assets/img/Icon/call.png" class="h-8 mr-2" alt="Phone">
                                <a href="https://wa.me/6281213369843" class="hover:text-gray-300">+628 12 1336 9843</a>
                            </li>
                        </ul>
                    </div>
                    <div class="w-full px-4 mb-4 md:w-1/4 md:mb-0">
                        <h4 class="mb-4 text-lg font-semibold">Ikuti Kami</h4>
                        <ul class="flex space-x-2">
                            <li>
                                <a href="https://www.instagram.com/snapfunstudio/" target="_blank" class="hover:text-gray-300">
                                    <img src="../../assets/img/Icon/instagram.png" class="h-8" alt="Instagram">
                                </a>
                            </li>
                            <li>
                                <a href="https://www.tiktok.com/@snapfunstudio" target="_blank" class="hover:text-gray-300">
                                    <img src="../../assets/img/Icon/tiktok.png" class="h-8" alt="TikTok">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 text-center">
                    © One Dream Creative Indonesia
                </div>
            </div>
        </footer>
    </body>

    <script>
        document.getElementById('navbar-toggle').addEventListener('click', function () {
            document.getElementById('navbar-menu').classList.toggle('hidden');
        });
    </script>
</html>
