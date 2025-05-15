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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap');

        body {
          margin: 0;
          background: radial-gradient(circle at top left, #ffecb3, #ffa726);
          font-family: 'Fredoka One', cursive;
          display: flex;
          justify-content: center;
          align-items: flex-start;
          min-height: 100vh;
          padding: 2rem;
          user-select: none;
        }

        .calendar-box {
          width: 100%;
          max-width: 540px;
          background: #fff4e5;
          border-radius: 24px;
          box-shadow: 0 10px 30px rgba(255, 167, 38, 0.45);
          padding: 35px 35px 50px;
          text-align: center;
          box-sizing: border-box;
        }

        .calendar-title {
          font-size: 3.2rem;
          color: #ff7043;
          margin-bottom: 6px;
          letter-spacing: 7px;
          text-shadow: 1px 1px 12px #ffa270;
          user-select: none;
        }

        .calendar-subtitle {
          color: #d84315;
          font-weight: 700;
          font-size: 1.25rem;
          margin-bottom: 16px;
          text-transform: uppercase;
          letter-spacing: 3px;
          text-shadow: 0 0 6px #ff7043;
        }

        /* FullCalendar theme overrides */
        .fc {
          font-family: 'Fredoka One', cursive;
          font-weight: 700;
        }

        .fc .fc-toolbar-title {
          font-size: 2rem;
          color: #d84315;
          text-shadow: 0 0 8px #ff7043;
        }

        .fc .fc-button {
          color: #fff;
          background: #f4511e;
          border: none;
          border-radius: 18px;
          padding: 0.45em 1.2em;
          font-weight: 900;
          box-shadow: 0 6px 14px rgba(244, 81, 30, 0.6);
          cursor: pointer;
          transition: background-color 0.3s ease, box-shadow 0.3s ease;
          user-select: none;
        }
        .fc .fc-button:hover,
        .fc .fc-button:focus {
          background: #ff7043;
          box-shadow: 0 10px 25px rgba(255, 112, 67, 0.85);
          outline: none;
        }

        .fc .fc-daygrid-day.fc-day-today {
          background: #ffccbc;
          border-radius: 16px;
          transition: background 0.3s ease;
        }
        .fc .fc-daygrid-day.fc-day-today:hover {
          background: #ff8a65;
        }

        .fc .fc-daygrid-day-number {
          font-size: 1.4rem;
          color: #bf360c;
          text-shadow: 0 0 3px #fff;
        }

        .fc .fc-scrollgrid-sync-inner {
          border-radius: 16px;
          box-shadow: 0 8px 22px rgba(244, 81, 30, 0.4);
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
          body {
            padding: 1rem;
            align-items: center;
          }

          .calendar-box {
            padding: 20px 15px 30px;
            max-width: 100%;
            box-shadow: 0 6px 18px rgba(255, 167, 38, 0.35);
            border-radius: 18px;
          }

          .calendar-title {
            font-size: 2.4rem;
            letter-spacing: 4px;
            margin-bottom: 4px;
          }

          .calendar-subtitle {
            font-size: 1rem;
            letter-spacing: 2px;
            margin-bottom: 12px;
          }

          .fc .fc-toolbar-title {
            font-size: 1.4rem;
          }

          .fc .fc-button {
            padding: 0.35em 0.75em;
            font-size: 0.9rem;
            border-radius: 14px;
            box-shadow: 0 4px 10px rgba(244, 81, 30, 0.5);
          }

          .fc .fc-daygrid-day-number {
            font-size: 1.1rem;
          }
        }
      </style>
</head>
<body class="bg-light">

    <div class="calendar-box" role="region" aria-label="SnapFun Calendar Alternative">
        <div class="calendar-title">SnapFun</div>
        <div class="calendar-subtitle">Let's have some fun!</div>
        <div id="calendar"></div>
      </div>


    {{-- Modal Bootstrap --}}
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">SnapFun Studio</h5>
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
