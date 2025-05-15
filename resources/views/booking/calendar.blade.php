<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Photo Studio</title>

    {{-- FullCalendar CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    {{-- Livewire Styles --}}
    @livewireStyles
    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body class="bg-light">

    <div class="container my-5">
        <h2 class="mb-4">Pilih Tanggal</h2>

        {{-- Kalender --}}
        <div id="calendar"></div>

        {{-- Input hidden untuk menyimpan tanggal terpilih --}}
        <input type="hidden" id="selected-date">
    </div>

    {{-- Modal Bootstrap --}}
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Booking Wizard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Livewire Wizard --}}
                    @livewire('booking-wizard')
                </div>
            </div>
        </div>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts
    @filamentScripts
    @vite('resources/js/app.js')

    {{-- jQuery, FullCalendar, Bootstrap JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const res = await fetch('/operational-hours');
            const { closed_days: closedDays } = await res.json();
            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                validRange: {
                    start: new Date().toISOString().split('T')[0]
                },
                dateClick: async function (info) {
                    const day = info.date.getDay();
                    const dayName = dayNames[day];

                    if (closedDays.includes(dayName)) {
                        Swal.fire({
                            title: 'Tanggal Tidak Tersedia',
                            text: `Hari ${dayName} kami tutup, Silahkan pilih hari lain`,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    const selectedDate = info.dateStr;
                    const today = new Date().toISOString().split('T')[0];

                    if (selectedDate === today) {
                        const res = await fetch('/jam-tutup');
                        const { close_time } = await res.json();

                        const now = new Date();
                        const closeTimeToday = new Date();
                        const [jam, menit] = close_time.split(':');
                        closeTimeToday.setHours(jam);
                        closeTimeToday.setMinutes(menit);
                        closeTimeToday.setSeconds(0);

                        if (now >= closeTimeToday) {
                            Swal.fire({
                                title: 'Booking Ditutup',
                                text: 'Studio sudah tutup untuk hari ini. Silakan pilih tanggal lain.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                    }

                    // Kalau aman, munculkan modal booking
                    Livewire.dispatch('setDate', { date: info.dateStr });
                    let modalEl = document.getElementById('bookingModal');
                    let modal = new bootstrap.Modal(modalEl);
                    modal.show();
                },

                dayCellDidMount: async function (info) {
                    const day = info.date.getDay();
                    const dayName = dayNames[day];
                    const today = new Date();
                    const isToday = info.date.toDateString() === today.toDateString();

                    // Cek hari tutup biasa
                    if (closedDays.includes(dayName)) {
                        info.el.style.backgroundColor = '#ffcccc';
                        info.el.style.color = '#990000';
                        info.el.style.pointerEvents = 'auto';
                        info.el.style.cursor = 'not-allowed';
                        info.el.title = `Hari ${dayName} tutup`;
                        return;
                    }

                    if (isToday) {
                        const res = await fetch('/jam-tutup');
                        const data = await res.json();
                        const closeTime = data.close_time || '18:00'; // fallback

                        const [closeHour, closeMinute] = closeTime.split(':').map(Number);
                        const nowHour = today.getHours();
                        const nowMinute = today.getMinutes();

                        const nowTotalMinutes = nowHour * 60 + nowMinute;
                        const closeTotalMinutes = closeHour * 60 + closeMinute;

                        if (nowTotalMinutes >= closeTotalMinutes) {
                            info.el.style.backgroundColor = '#e9ecef';
                            info.el.style.color = '#6c757d';
                            info.el.style.pointerEvents = 'auto';
                            info.el.style.cursor = 'not-allowed';
                            info.el.title = `Hari ini sudah lewat jam tutup (${closeTime})`;
                        }
                    }
                }

            });

            calendar.render();
        });
    </script>




    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('bookingSuccess', function (message) {
                Swal.fire({
                    title: 'Booking Berhasil!',
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>

</body>
</html>
