<?php

namespace App\Livewire;

use App\Models\Ticketing;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TicketingForms extends Component implements HasForms
{
    use InteractsWithForms;

    public $isSuccess = false;
    public $isWaiting = false;
    public $customer_id = null;
    public $queue_number = null;
    public $queueStatus;
    public $album_id = null; 
    public $drive_link = null; // Properti QR Code

    // Properti Logika Event
    public $event_id;
    public $isMultipleEvents = false;

    // Properti Form
    public $nama;
    public $email;
    public $telpon;
    public $jumlah;
    public $cetak;
    public $transaction_type;

    public function mount()
    {
        $activeEvents = DB::table('events')->where('is_active', true)->get();

        // JIKA LEBIH DARI 1 EVENT AKTIF, MUNCULKAN DROPDOWN PILIHAN
        if ($activeEvents->count() > 1) {
            $this->isMultipleEvents = true;
            $this->form->fill();
        } 
        // JIKA HANYA 1 EVENT AKTIF, ATAU TIDAK ADA SAMA SEKALI (0 EVENT)
        else {
            $this->isMultipleEvents = false;
            // Ambil ID jika ada 1, jika kosong maka jadikan null (Antrean Tanpa Event)
            $this->event_id = $activeEvents->first()->id ?? null;
            $this->form->fill([
                'event_id' => $this->event_id,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            $this->isMultipleEvents 
                ? Select::make('event_id')
                    ->label('Lokasi Event')
                    ->options(function () {
                        return DB::table('events')->where('is_active', true)->pluck('nama_event', 'id');
                    })
                    ->placeholder('Pilih lokasi event saat ini...')
                    ->required()
                : Hidden::make('event_id'),

            TextInput::make('nama')->placeholder('Masukkan nama lengkap kamu')->required(),
            TextInput::make('email')->email()->placeholder('contoh@email.com')->required(),
            TextInput::make('telpon')->label('Nomor Handphone (WhatsApp)')->numeric()->placeholder('08xx-xxxx-xxxx')->required(),
            
            Grid::make(2)->schema([
                TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label('Jumlah Orang')
                    ->placeholder('2'),
                TextInput::make('cetak')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label('Jumlah Cetak')
                    ->placeholder('4'),
            ]),

            Select::make('transaction_type')
                ->options(['tunai' => 'Tunai', 'qris' => 'QRIS'])
                ->placeholder('Pilih metode pembayaran...')
                ->live()
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->transaction_type = $state)
                ->label('Jenis Pembayaran')
                ->required(),
        ]);
    }

    public function submit()
    {
        $validate = $this->validate([
            'event_id' => 'nullable', // Boleh kosong (null) jika tidak ada event
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telpon' => 'required|string|max:15',
            'jumlah' => 'required|integer|min:0',
            'cetak' => 'required|integer|min:0',
            'transaction_type' => 'required|string|in:tunai,qris',
        ]);

        $newNumber = 'SF001';
        $insertedId = null;

        // 1. BLOK TRANSAKSI ANTREAN
        DB::transaction(function () use (&$newNumber, &$insertedId, $validate) {
            $query = DB::table('ticketings');
            
            // Cek apakah mendaftar untuk event atau "Antrean Tanpa Event"
            if (empty($validate['event_id'])) {
                $query->whereNull('event_id');
            } else {
                $query->where('event_id', $validate['event_id']);
            }

            $lastTicket = $query->orderBy('id', 'desc')->lockForUpdate()->first();
            
            if ($lastTicket && $lastTicket->queue_number) {
                $lastNumber = (int) str_replace('SF', '', $lastTicket->queue_number);
                $newNumber = 'SF' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            }

            $validate['queue_number'] = $newNumber; 
            $validate['status'] = 'menunggu';
            $validate['created_at'] = now();
            $validate['updated_at'] = now();

            $insertedId = DB::table('ticketings')->insertGetId($validate);
        });
        
        // =========================================================
        // 2. OLAH NAMA FOLDER GDRIVE & NUXT SNAPLINK
        // =========================================================
        $angkaAntrean = (int) str_replace('SF', '', $newNumber);
        $nomorFormat = str_pad($angkaAntrean, 2, '0', STR_PAD_LEFT);
        $namaDepan = strtolower(explode(' ', trim($validate['nama']))[0]);
        $namaFolderDrive = $nomorFormat . ' ' . $namaDepan;

        // =========================================================
        // 3. AMBIL DATA EVENT & SET PARENT FOLDER GDRIVE
        // =========================================================
        $event = null;
        $folderUtamaDriveId = null;

        if (!empty($validate['event_id'])) {
            $event = DB::table('events')->where('id', $validate['event_id'])->first();
            $folderUtamaDriveId = $event ? $event->gdrive_folder_id : null;
        } else {
            // Jika Antrean Tanpa Event, ambil Folder Pusat dari Setting
            $setting = DB::table('settings')->where('key', 'google_upload_folder')->first();
            if ($setting) {
                $folderUtamaDriveId = json_decode($setting->value, true)['folder_id'] ?? null;
            }
        }
        
        // 4. REMOTE GOOGLE DRIVE: Buat Sub-Folder
        $driveData = $this->createDriveFolder($namaFolderDrive, $folderUtamaDriveId);

        // 5. GENERATE ID ALBUM UNTUK NUXT
        $albumId = strtoupper(Str::random(6));

        // 6. INSERT KE TABEL ALBUMS (Database Nuxt)
        DB::table('albums')->insert([
            'id' => $albumId,
            'name' => $namaFolderDrive,
            'phone' => $validate['telpon'],
            'paket' => 'Self Photo',
            'drive_link' => $driveData['webViewLink'] ?? null,
            'folder_id' => $driveData['id'] ?? null,
            'group_name' => $event ? $event->nama_event : 'Antrean Tanpa Event', // Masuk ke grup tanpa event
            'expires_at' => now()->addDays(14)->timestamp,
            'created_at' => now()->timestamp,
        ]);

        // Simpan Data untuk View Tiket & QR Code
        $this->customer_id = $insertedId;
        $this->queue_number = $newNumber; 
        $this->event_id = $validate['event_id'];
        $this->album_id = $albumId;
        $this->drive_link = rtrim(config('services.snaplink.url'), '/') . '/gallery/' . $albumId; // Menangkap link untuk QR Code

        $this->isSuccess = true;
    }

    /**
     * Helper Function: Membuat Folder di Google Drive via API
     */
    private function createDriveFolder($folderName, $parentFolderId)
    {
        try {
            $setting = DB::table('settings')->where('key', 'google_oauth_tokens')->first();
            if (!$setting) return null;

            $tokens = json_decode($setting->value, true);
            $accessToken = $tokens['access_token'];

            $response = Http::withToken($accessToken)
                ->post('https://www.googleapis.com/drive/v3/files', [
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

    // ==========================================
    // FUNGSI KONFIRMASI WA (KIRIM KE ADMIN SNAP FUN)
    // ==========================================
    public function confirmToWA()
    {
        $pembayaran = $this->transaction_type === 'qris' ? 'QRIS' : 'Tunai';

        $pesan = "*Halo Snap Fun!*\n\nSaya ingin konfirmasi booking *Pop Up Self Photo*\n\n━━━━━━━━━━━━━━━━\n\n*Nama:* {$this->nama}\n*Nomor Antrian:* {$this->queue_number}\n*Email:* {$this->email}\n*No. HP:* {$this->telpon}\n*Jumlah Orang:* {$this->jumlah}\n*Jumlah Cetak:* {$this->cetak}\n*Pembayaran:* {$pembayaran}\n\n━━━━━━━━━━━━━━━━\nMohon konfirmasinya ya, terima kasih 🙏";

        $encodedPesan = urlencode($pesan);
        $nomorWA = '6285117607254';

        // Ubah state menjadi menunggu
        $this->isWaiting = true;

        $this->dispatch(
            'redirectToWA',
            url: "https://api.whatsapp.com/send?phone={$nomorWA}&text={$encodedPesan}"
        );
    }

    // ==========================================
    // FUNGSI LIVE ANTREAN (MENGHITUNG SISA ORANG)
    // ==========================================
    public function getQueueStatusProperty()
    {
        if (!$this->customer_id) {
            return null;
        }

        $myTicket = DB::table('ticketings')->where('id', $this->customer_id)->first();
        if (!$myTicket) {
            return ['peopleAhead' => 0, 'isCalled' => false];
        }

        // Hitung berapa orang di depan yang masih "menunggu"
        $query = DB::table('ticketings')
            ->where('status', 'menunggu')
            ->where('id', '<', $this->customer_id);

        // Pisahkan hitungan antara yang punya event vs yang tidak punya event
        if ($this->event_id) {
            $query->where('event_id', $this->event_id);
        } else {
            $query->whereNull('event_id');
        }

        $peopleAhead = $query->count();

        return [
            'peopleAhead' => $peopleAhead,
            'isCalled' => $myTicket->status !== 'menunggu', // Jika status diubah kru, muncul "GILIRAN KAMU!"
        ];
    }
    
    public function render()
    {
        return view('livewire.ticketing-forms');
    }
}