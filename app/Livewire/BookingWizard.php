<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OperationalHour;
use App\Models\Booking;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Contracts\HasForms;
use App\Services\GoogleCalendarService;
use App\Services\GoogleDriveService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingStudioTicketMail;

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
    protected static ?string $title = 'Booking Studio';  
    protected static ?string $heading = 'Form Booking Studio';

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
    
    public function render()
    {
        $disabledDates = OperationalHour::pluck('day')->toArray(); 

        return view('livewire.booking-wizard', [
            'disabledDates' => $disabledDates,
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
                            ->options(\App\Models\Package::pluck('title', 'id')->toArray())
                            ->required()
                            ->reactive(),
                            Placeholder::make('package_info')
                            ->visible(fn ($get) => $get('package'))
                            ->label('Detail Paket')
                            ->content(function ($get) {
                                $package = \App\Models\Package::find($get('package'));
                                return $package
                                    ? new HtmlString("
                                        <strong>{$package->title}</strong><br>
                                        Durasi: {$package->duration_minutes} menit<br>
                                        Harga: Rp " . number_format($package->price, 0, ',', '.') . "<br>
                                        " . nl2br($package->description) . "
                                    ")
                                    : '';
                            }),
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
                            ->label('Saya bersedia Kehilangan DP senilai Rp.30.000, jika melakukan cancel di HARI JADWAL SESI yang saya ambil. (Diperlukan)')
                            ->required(),
                            Placeholder::make('qris_payment2')
                            ->label('')
                            ->content(new HtmlString('WAJIB! KIRIM BUKTI BOOKING YANG MUNCUL SETELAH KLIK (Confirm Booking) DAN KIRIM BUKTI TRANSFER KE ADMIN KAMI (Via WhatsApp).')),
                    ]),
            ])->submitAction(new HtmlString(Blade::render('
                <x-filament::button
                    wire:click="submit"
                    type="button"
                    size="sm"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                >
                    <span wire:loading.remove wire:target="submit">Submit Booking</span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-1">
                        Memproses...
                    </span>
                </x-filament::button>
            '))),
        ];
    }

    protected function getTimeOptions(): array
    {
        $dayOfWeek = \Carbon\Carbon::parse($this->date)->locale('id')->translatedFormat('l');

        $operationalHour = \App\Models\OperationalHour::where('day', $dayOfWeek)->first();
        if (!$operationalHour || !$operationalHour->is_open) {
            return [];
        }

        $times = [];
        $start = \Carbon\Carbon::createFromTimeString($operationalHour->open_time);
        $end = \Carbon\Carbon::createFromTimeString($operationalHour->close_time);
        $now = \Carbon\Carbon::now();

        $bookedTimes = \App\Models\Booking::where('date', $this->date)
            ->pluck('time')
            ->map(fn ($time) => \Carbon\Carbon::parse($time)->format('H:i'))
            ->toArray();

        while ($start <= $end) {
            $time = $start->format('H:i');

            if ($this->date === $now->toDateString() && $start->lessThan($now)) {
                $start->addMinutes(30);
                continue;
            }

            if (in_array($time, $bookedTimes)) {
                $start->addMinutes(30);
                continue;
            }

            $times[$time] = $time;
            $start->addMinutes(30);
        }
        return $times;
    }


    public function submit()
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
        
        $package = \App\Models\Package::find($this->package);

        if (!$package) {
            session()->flash('error', 'Paket tidak ditemukan.');
            return;
        }

        // 1. Simpan data booking utama
        Booking::create([
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

        // 2. Persiapan Album ID & Link
        $albumId = strtoupper(Str::random(6));
        $baseUrl = config('services.snaplink.url') ?: 'https://send.snapfunstudio.id';
        $finalDriveLink = rtrim($baseUrl, '/') . '/gallery/' . $albumId; 

        // 3. Buat Folder Drive & Record di Tabel Albums
        try {
            $namaFolderDrive = trim($this->name) . ' - ' . \Carbon\Carbon::parse($this->date)->format('dMy') . ' ' . str_replace(':', '', $this->time);
            
            $setting = DB::table('settings')->where('key', 'google_upload_folder')->first();
            $folderUtamaDriveId = $setting ? (json_decode($setting->value, true)['folder_id'] ?? null) : null;
            
            $driveData = $this->createDriveFolder($namaFolderDrive, $folderUtamaDriveId);
            $childFolderId = $driveData['id'] ?? null;
            $actualDriveLink = $childFolderId 
                ? ('https://drive.google.com/drive/folders/' . $childFolderId) 
                : ($driveData['webViewLink'] ?? null);

            DB::table('albums')->insert([
                'id' => $albumId,
                'name' => $namaFolderDrive,
                'phone' => $this->whatsapp,
                'paket' => $package->title,
                'drive_link' => $actualDriveLink,
                'folder_id' => $childFolderId,
                'group_name' => 'Booking Studio',
                'expires_at' => now()->addDays(14)->timestamp,
                'created_at' => now()->timestamp,
            ]);
        } catch (\Throwable $e) {
            Log::error("Gagal buat Album: " . $e->getMessage());
        }

        // 4. Kirim Email 
        try {
            $emailData = [
                'nama' => $this->name,
                'tanggal' => \Carbon\Carbon::parse($this->date)->translatedFormat('d F Y'),
                'waktu' => $this->time,
                'paket_nama' => $package->title,
                'album_id' => $albumId,
                'drive_link' => $finalDriveLink
            ];

            Mail::to($this->email)->send(new BookingStudioTicketMail($emailData));
        } catch (\Throwable $e) {
            Log::error("Gagal kirim email: " . $e->getMessage());
        }

        // 5. REDIRECT PAKSA KE HALAMAN SUKSES (Bebas dari bug Livewire!)
        return redirect()->route('booking.success', ['album_id' => $albumId]);
    }

    private function createDriveFolder($folderName, $parentFolderId)
    {
        try {
            $drive = app(GoogleDriveService::class);
            $accessToken = $drive->getValidAccessToken();

            if (!$accessToken) {
                return null;
            }

            if ($parentFolderId) {
                $escapedName = addslashes($folderName);
                $checkQuery = "mimeType='application/vnd.google-apps.folder' and name='{$escapedName}' and '{$parentFolderId}' in parents and trashed=false";
                
                $checkResponse = Http::withToken($accessToken)
                    ->get('https://www.googleapis.com/drive/v3/files', [
                        'q' => $checkQuery,
                        'fields' => 'files(id, name, webViewLink)',
                    ]);

                if ($checkResponse->successful() && !empty($checkResponse->json()['files'])) {
                    return $checkResponse->json()['files'][0];
                }
            }

            $response = Http::withToken($accessToken)
                ->post('https://www.googleapis.com/drive/v3/files?fields=id,name,webViewLink', [
                    'name' => $folderName,
                    'mimeType' => 'application/vnd.google-apps.folder',
                    'parents' => $parentFolderId ? [$parentFolderId] : []
                ]);

            if ($response->successful()) {
                return $response->json(); 
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}