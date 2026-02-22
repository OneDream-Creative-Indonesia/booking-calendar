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
    public $jumlah;
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
            TextInput::make('jumlah')
                ->required()
                ->label('Jumlah Orang'),
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
            'jumlah' => 'required|integer|min:1',
            'telpon' => 'required|string|max:15',
            'transaction_type' => 'required|string|in:tunai,qris',
        ]);

        Ticketing::create($validate);

        Session::flash('message', 'terimakasih data kamu telah diterima, Foto Akan dikirim maks 1 minggu');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nama = '';
        $this->jumlah = '';
        $this->telpon = '';
        $this->transaction_type = '';
    }

    public function render()
    {
        return view('livewire.ticketing-forms');
    }
}
