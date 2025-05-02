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
    document.addEventListener('DOMContentLoaded', function() {
    // Tunggu hingga Livewire dimuat
    setTimeout(function() {
        let calendarEl = document.getElementById('calendar');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            validRange: {
                start: new Date().toISOString().split('T')[0]
            },
            dateClick: function(info) {
                Livewire.dispatch('setDate', { date: info.dateStr });

                // Tampilkan modal atau aksi lainnya
                let modalEl = document.getElementById('bookingModal');
                let modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
        calendar.render();
    }, 100); // Tunda 100ms untuk memberi waktu Livewire dimuat
});

    </script>
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
