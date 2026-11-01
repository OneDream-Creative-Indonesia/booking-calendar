<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class BookingSuccess extends Component
{
    public $album_id;
    public $albumData;

    public function mount($album_id)
    {
        $this->album_id = $album_id;
        $this->albumData = DB::table('albums')->where('id', $album_id)->first();

        if (!$this->albumData) {
            return redirect('/'); // Kembali ke awal jika tidak ditemukan
        }
    }

    public function render()
    {
        return view('livewire.booking-success')->layout('layouts.app'); // Sesuaikan layout Anda
    }
}