<?php

namespace App\Livewire;

use App\Models\Ticketing;
use App\Services\GoogleDriveService;
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
use Illuminate\Support\Facades\Mail;
use App\Mail\PhotoboothTicketMail;

class TicketingForms extends Component implements HasForms
{
    use InteractsWithForms;

    public $isSuccess = false;
    public $isSubmitting = false;
    public $customer_id = null;
    public $queue_number = null;
    public $album_id = null; 
    public $drive_link = null; 

    public $event_id;
    public $isMultipleEvents = false;

    public $nama;
    public $email;
    public $telpon;
    public $jumlah;
    public $cetak;
    public $transaction_type;

    public function mount()
    {
        $activeEvents = DB::table('events')->where('is_active', true)->orderBy('id', 'desc')->get();

        if ($activeEvents->count() > 1) {
            $this->isMultipleEvents = true;
            $this->form->fill();
        } else {
            $this->isMultipleEvents = false;
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
        if ($this->isSuccess || $this->isSubmitting) {
            return;
        }
        $this->isSubmitting = true;

        $validate = $this->validate([
            'event_id' => 'nullable', 
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telpon' => 'required|string|max:15',
            'jumlah' => 'required|integer|min:0',
            'cetak' => 'required|integer|min:0',
            'transaction_type' => 'required|string|in:tunai,qris',
        ]);

        if (empty($validate['event_id'])) {
            $activeEvent = DB::table('events')->where('is_active', true)->orderBy('id', 'desc')->first();
            if ($activeEvent) {
                $validate['event_id'] = $activeEvent->id;
            }
        }

        // Rapikan spasi berlebih pada nama tanpa mengubah huruf besar/kecil
        $validate['nama'] = trim($validate['nama']);

        $newNumber = 'SF001';
        $insertedId = null;

        DB::transaction(function () use (&$newNumber, &$insertedId, $validate) {
            $query = DB::table('ticketings');
            
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
        
        $angkaAntrean = (int) str_replace('SF', '', $newNumber);
        $nomorFormat = str_pad($angkaAntrean, 2, '0', STR_PAD_LEFT);
        
        // PERTAHANKAN HURUF BESAR & KECIL SESUAI INPUT REGISTRASI
        $namaAsli = $validate['nama']; 
        $namaFolderDrive = $nomorFormat . ' ' . $namaAsli;

        $event = null;
        $folderUtamaDriveId = null;
        $groupName = 'Antrean Tanpa Event';

        if (!empty($validate['event_id'])) {
            $event = DB::table('events')->where('id', $validate['event_id'])->first();
            if ($event) {
                $folderUtamaDriveId = $event->gdrive_folder_id;
                $groupName = $event->nama_event . ' - ' . $event->tanggal_event;
            }
        }

        if (!$folderUtamaDriveId) {
            $setting = DB::table('settings')->where('key', 'google_upload_folder')->first();
            if ($setting) {
                $folderUtamaDriveId = json_decode($setting->value, true)['folder_id'] ?? null;
            }
        }
        
        $driveData = $this->createDriveFolder($namaFolderDrive, $folderUtamaDriveId);
        $childFolderId = $driveData['id'] ?? null;
        $driveLink = $childFolderId 
            ? ('https://drive.google.com/drive/folders/' . $childFolderId) 
            : ($driveData['webViewLink'] ?? null);

        $existingAlbum = DB::table('albums')
            ->where('group_name', $groupName)
            ->where('name', $namaFolderDrive)
            ->first();

        if ($existingAlbum) {
            $albumId = $existingAlbum->id;
            DB::table('albums')->where('id', $albumId)->update([
                'name' => $namaFolderDrive,
                'phone' => $validate['telpon'],
                'paket' => 'Photobooth',
                'folder_id' => $childFolderId ?? $existingAlbum->folder_id,
                'drive_link' => $driveLink ?? $existingAlbum->drive_link,
            ]);
        } else {
            $albumId = strtoupper(Str::random(6));
            DB::table('albums')->insert([
                'id' => $albumId,
                'name' => $namaFolderDrive,
                'phone' => $validate['telpon'],
                'paket' => 'Photobooth',
                'drive_link' => $driveLink,
                'folder_id' => $childFolderId,
                'group_name' => $groupName, 
                'expires_at' => now()->addDays(14)->timestamp,
                'created_at' => now()->timestamp,
            ]);
        }

        // Set properties komponen
        $this->customer_id = $insertedId;
        $this->queue_number = $newNumber; 
        $this->event_id = $validate['event_id'];
        $this->album_id = $albumId;
        
        // Buat URL Galeri
        $baseUrl = config('services.snaplink.url') ?: 'https://send.snapfunstudio.id';
        $this->drive_link = rtrim($baseUrl, '/') . '/gallery/' . $albumId; 

        // MULAI DARI SINI: KODE KIRIM EMAIL OTOMATIS
        try {
            $emailData = [
                'nama' => $validate['nama'],
                'queue_number' => $newNumber,
                'album_id' => $albumId,
                'drive_link' => $this->drive_link
            ];

            Mail::to($validate['email'])->send(new PhotoboothTicketMail($emailData));
        } catch (\Exception $e) {
            // Biarkan lewat jika email gagal agar customer tetap berhasil masuk antrean
        }
        // BATAS KODE KIRIM EMAIL

        $this->isSuccess = true;
        $this->isSubmitting = false;
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

    public function checkQueueStatus()
    {
        if (!$this->customer_id) {
            return null;
        }

        $myTicket = DB::table('ticketings')->where('id', $this->customer_id)->first();
        if (!$myTicket) {
            return ['peopleAhead' => 0, 'isCalled' => false];
        }

        $query = DB::table('ticketings')
            ->where('status', 'menunggu')
            ->where('id', '<', $this->customer_id);

        if ($this->event_id) {
            $query->where('event_id', $this->event_id);
        } else {
            $query->whereNull('event_id');
        }

        $peopleAhead = $query->count();

        return [
            'peopleAhead' => $peopleAhead,
            'isCalled' => $myTicket->status !== 'menunggu', 
        ];
    }
    
    public function render()
    {
        return view('livewire.ticketing-forms');
    }
}