<?php

namespace App\Livewire;

use App\Models\Ticketing;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Closure;

class TicketingForms extends Component implements HasForms
{
    use InteractsWithForms;

    // Menandai apakah form sudah disubmit atau belum
    public $isSuccess = false;
    
    // Menandai apakah customer sudah konfirmasi WA dan masuk ke halaman menunggu
    public $isWaiting = false;
    
    // Menyimpan ID customer untuk melacak posisi antrean mereka
    public $customer_id = null;

    // Menyimpan Nomor Antrean (SF00X)
    public $queue_number = null;

    // Properti Form
    public $nama;
    public $email;
    public $telpon;
    public $jumlah;
    public $cetak;
    public $transaction_type;

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama')
                ->placeholder('Masukkan nama lengkap kamu')
                ->required(),
                
            TextInput::make('email')
                ->email()
                ->placeholder('contoh@email.com')
                ->required(),
                
            TextInput::make('telpon')
                ->label('Nomor Handphone (WhatsApp)')
                ->numeric()
                ->placeholder('08xx-xxxx-xxxx')
                ->required(),
            // Menyatukan Jumlah Orang dan Jumlah Cetak sejajar menyamping
            Grid::make(2)
                ->schema([
                    TextInput::make('jumlah')
                        ->required()
                        ->numeric()
                        ->label('Jumlah Orang')
                        ->placeholder('2'),
                        
                    TextInput::make('cetak')
                        ->required()
                        ->numeric()
                        ->label('Jumlah Cetak')
                        ->placeholder('4'),
                ]),

            Select::make('transaction_type')
                ->options([
                    'tunai' => 'Tunai',
                    'qris' => 'QRIS'
                ])
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
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telpon' => 'required|string|max:15',
            'jumlah' => 'required|integer|min:1',
            'cetak' => 'required|integer|min:1',
            'transaction_type' => 'required|string|in:tunai,qris',
        ]);

        // Cek dan buat kolom queue_number secara otomatis jika belum ada di database
        if (!\Illuminate\Support\Facades\Schema::hasColumn('ticketings', 'queue_number')) {
            \Illuminate\Support\Facades\Schema::table('ticketings', function ($table) {
                $table->string('queue_number', 20)->nullable();
            });
        }

        // MENCEGAH RACE CONDITION (Bentrokan Data Saat Submit Bersamaan)
        $newNumber = 'SF001';
        $insertedId = null;

        DB::transaction(function () use (&$newNumber, &$insertedId, $validate) {
            // lockForUpdate() mencegah beberapa orang mendapatkan nomor yang sama
            $lastTicket = DB::table('ticketings')->orderBy('id', 'desc')->lockForUpdate()->first();
            
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
        
        // Simpan ID & Nomor Antrean untuk dilacak posisinya
        $this->customer_id = $insertedId;
        $this->queue_number = $newNumber;

        // Ubah tampilan ke halaman sukses
        $this->isSuccess = true;
    }

    public function confirmToWA()
    {
        $pembayaran = $this->transaction_type === 'qris' ? 'QRIS' : 'Tunai';

        $pesan = "*Halo Snap Fun!*\n\nSaya ingin konfirmasi booking *Pop Up Self Photo*\n\n━━━━━━━━━━━━━━━━\n\n*Nama:* {$this->nama}\n*Nomor Antrian* {$this->queue_number}\n*Email:* {$this->email}\n*No. HP:* {$this->telpon}\n*Jumlah Orang:* {$this->jumlah}\n*Jumlah Cetak:* {$this->cetak}\n*Pembayaran:* {$pembayaran}\n\n━━━━━━━━━━━━━━━━\nMohon konfirmasinya ya, terima kasih 🙏";

        $encodedPesan = urlencode($pesan);
        $nomorWA = '6285117607254';

        // Ubah state menjadi menunggu
        $this->isWaiting = true;

        $this->dispatch(
            'redirectToWA',
            url: "https://api.whatsapp.com/send?phone={$nomorWA}&text={$encodedPesan}"
        );
    }
    
    // Fitur Live Antrean
    public function getQueueStatusProperty()
    {
        if (!$this->customer_id) return null;

        $peopleAhead = DB::table('ticketings')
            ->where('id', '<', $this->customer_id)
            ->where(function($query) {
                $query->where('status', 'menunggu')
                      ->orWhereNull('status')
                      ->orWhere('status', '');
            })->count();

        $isCalled = DB::table('ticketings')
            ->where('id', $this->customer_id)
            ->where('status', 'dipanggil')
            ->exists();

        return [
            'peopleAhead' => max(0, $peopleAhead),
            'isCalled' => $isCalled
        ];
    }

    public function render()
    {
        return view('livewire.ticketing-forms');
    }
}