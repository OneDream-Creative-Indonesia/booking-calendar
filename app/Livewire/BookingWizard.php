<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use App\Services\GoogleCalendarService;
class BookingWizard extends Component implements HasForms
{
    use InteractsWithForms;
    public $date;
    public $time;
    public $name;
    public $whatsapp;
    public $package;
    public $confirmation;
    public $email;
    public $people_count;
    protected $listeners = ['setDate'];


    public function setDate($date)
    {
        $this->date = $date;
    }
    public function mount()
    {
        $this->form->fill([
            'date' => $this->date,
            'time' => '',
            'name' => '',
            'whatsapp' => '',
            'package' => '',
            'email' => '',
            'people_count' => '',
            "confirmation" => false
        ]);
    }

    public function getFormSchema(): array
    {
        return [
            Wizard::make([
                 Wizard\Step::make('Pilih Jadwal')
                    ->schema([
                        Select::make('time')
                            ->label('Jam Booking')
                            ->options(fn () => $this->getTimeOptions())
                            ->searchable()
                            ->required()
                            ->reactive(),
                    ]),

                Wizard\Step::make('Pilih Paket')
                    ->schema([
                        Select::make('package')
                            ->label('Pilih Paket')
                            ->options(\App\Models\Package::pluck('title', 'title')->toArray())
                            ->required()
                            ->reactive(),
                            Placeholder::make('basic_package')
                            ->visible(fn ($get) => $get('package') === 'Basic')
                            ->label('Paket Basic')
                            ->content('Paket ini mencakup sesi foto 30 menit dengan 5 hasil edit terbaik.'),

                        Placeholder::make('premium_package')
                            ->visible(fn ($get) => $get('package') === 'Premium')
                            ->label('Paket Premium')
                            ->content('Paket ini mencakup sesi foto 1 jam dengan 10 hasil edit terbaik + cetak 4R.'),

                        Placeholder::make('exclusive_package')
                            ->visible(fn ($get) => $get('package') === 'Exclusive')
                            ->label('Paket Exclusive')
                            ->content('Paket ini mencakup sesi foto 2 jam dengan 20 hasil edit terbaik + cetak 10R & album.'),
                    ]),

                Wizard\Step::make('Data Pemesan')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),

                        TextInput::make('whatsapp')
                            ->label('Nomor WhatsApp')
                            ->required()
                            ->numeric(),
                        TextInput::make('email')
                        ->label('Alamat Email')
                        ->required(),
                        Select::make('people_count')
                            ->label('Jumlah Orang (Lebih dari 2 orang +25k/orang)')
                            ->options(array_combine(range(1, 15), range(1, 15)))
                            ->required(),
                            Placeholder::make('dp_info')
                            ->label('Down Payment (DP)')
                            ->content(new HtmlString('Rp. 30.000')),

                        Placeholder::make('qris_payment')
                            ->label('')
                            ->content(new HtmlString('Pembayaran DP melalui Transfer QRIS <a href="LINK_QRIS" target="_blank" style="color: blue; text-decoration: underline;">KLIK DISINI</a>, bila ada kesulitan silahkan hubungi admin kami <a href="LINK_ADMIN" target="_blank" style="color: blue; text-decoration: underline;">KLIK DISINI</a>.')),
                        Toggle::make('confirmation')
                            ->label('
                            Saya bersedia Kehilangan DP senilai Rp.30.000, jika melakukan cancel di HARI JADWAL SESI yang saya ambil. (Diperlukan)')
                            ->required(),
                            Placeholder::make('qris_payment')
                            ->label('')
                            ->content(new HtmlString('WAJIB! KIRIM BUKTI BOOKING YANG MUNCUL SETELAH KLIK (Confirm Booking) DAN KIRIM BUKTI TRANSFER KE ADMIN KAMI (Via WhatsApp).')),
                    ]),
                    ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        wire:click="submit"
                        type="submit"
                        size="sm"
                    >
                        Submit
                    </x-filament::button>
                BLADE))),
            ];
    }

    protected function getTimeOptions(): array
    {
        if (!$this->date) {
            return [];
        }

        $times = [];
        $start = \Carbon\Carbon::createFromTime(9, 0); // Mulai dari jam 9 pagi
        $end = \Carbon\Carbon::createFromTime(17, 0);  // Sampai jam 5 sore
        $now = \Carbon\Carbon::now();

        // Ambil semua jam yang sudah dipesan di tanggal yang dipilih
        $bookedTimes = \App\Models\Booking::where('date', $this->date)
            ->pluck('time')
            ->map(fn ($time) => \Carbon\Carbon::parse($time)->format('H:i'))
            ->toArray();

        while ($start <= $end) {
            $time = $start->format('H:i');

            // Nonaktifkan jam yang sudah lewat jika memilih hari ini
            if ($this->date === $now->toDateString() && $start->lessThan($now)) {
                $start->addMinutes(30);
                continue;
            }

            // Nonaktifkan jam yang sudah dibooking
            if (in_array($time, $bookedTimes)) {
                $start->addMinutes(30);
                continue;
            }

            $times[$time] = $time;
            $start->addMinutes(30);
        }

        return $times;
    }


    public function submit(GoogleCalendarService $googleCalendarService)
    {
        $this->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|numeric',
            'email' => 'required|email',
            'package' => 'required|string',
            'people_count' => 'required|integer|min:1|max:15',
            'confirmation' => 'required|boolean',
        ]);

        $package = \App\Models\Package::where('title', $this->package)->first();

        if (!$package) {
            session()->flash('error', 'Paket tidak ditemukan.');
            return;
        }

        $booking = \App\Models\Booking::create([
            'date' => $this->date,
            'time' => $this->time,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'package_id' => $package->id,
            'people_count' => $this->people_count,
            'confirmation' => $this->confirmation,
            'status' => 'pending',
        ]);
        $googleCalendarService->refreshTokenIfNeeded();

        $googleCalendarService->createEvent($booking);
        $this->dispatch('bookingSuccess', 'Silakan kirim bukti booking ke admin kami.');
        return redirect()->route('booking.calendar');
    }
    public function render()
    {
        return view('livewire.booking-wizard');
    }
}
