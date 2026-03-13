<?php

namespace App\Livewire;

use App\Models\Ticketing;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class TicketingForms extends Component implements HasForms
{
    use InteractsWithForms;

    public $nama;
    public $email;
    public $jumlah;
    public $cetak;
    public $telpon;
    public $transaction_type;

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama')
                ->required(),
            TextInput::make('email')
                ->email()
                ->required(),
            TextInput::make('jumlah')
                ->required()
                ->label('Jumlah Orang'),
            TextInput::make('cetak')
                ->required()
                ->numeric()
                ->label('Jumlah Cetak'),
            TextInput::make('telpon')
                ->label('Nomor Handphone')
                ->numeric()
                ->placeholder('08xx-xxxx-xxxx')
                ->required(),
            Select::make('transaction_type')
                ->options([
                    'tunai' => "Tunai",
                    'qris' => 'Qris'
                ])
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
            'jumlah' => 'required|integer|min:1',
            'cetak' => 'required|integer|min:1',
            'telpon' => 'required|string|max:15',
            'transaction_type' => 'required|string|in:tunai,qris',
        ]);

        Ticketing::create($validate);

        $pembayaran = $this->transaction_type === 'qris' ? 'QRIS' : 'Tunai';

       $pesan = "Halo Snap Fun!
        Saya ingin konfirmasi booking Pop Up Self Photo:
        ━━━━━━━━━━━━━━━━
        Nama: {$this->nama}
        Email: {$this->email}
        Jumlah Orang: {$this->jumlah}
        Jumlah Cetak: {$this->cetak}
        No. HP: {$this->telpon}
        Pembayaran: {$pembayaran}
        ━━━━━━━━━━━━━━━━
        Mohon konfirmasinya ya, terima kasih!";

        $encodedPesan = urlencode($pesan);

        $nomorWA = '6285117607254';

        $this->dispatch(
            'redirectToWA',
            url: "https://api.whatsapp.com/send?phone={$nomorWA}&text={$encodedPesan}"
        );


        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nama = '';
        $this->email = '';
        $this->jumlah = '';
        $this->cetak = '';
        $this->telpon = '';
        $this->transaction_type = '';
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.ticketing-forms');
    }
}
