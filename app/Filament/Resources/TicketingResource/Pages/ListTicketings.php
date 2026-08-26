<?php

namespace App\Filament\Resources\TicketingResource\Pages;

use App\Filament\Resources\TicketingResource;
use App\Models\Event;
use App\Models\Ticketing;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use App\Services\GoogleDriveService;

class ListTicketings extends Page
{
    protected static string $resource = TicketingResource::class;

    protected static string $view = 'filament.resources.ticketing-resource.pages.custom-list';

    public array $selectedTickets = [];

    public function getEventsProperty()
    {
        return Event::with('ticketings')->orderBy('tanggal_event', 'desc')->get();
    }

    public function getTicketsWithoutEventProperty()
    {
        return Ticketing::whereNull('event_id')->orderBy('created_at', 'desc')->get();
    }

    public function toggleSelectAll($eventId = null)
    {
        if ($eventId) {
            $ticketIds = Ticketing::where('event_id', $eventId)->pluck('id')->toArray();
        } else {
            $ticketIds = Ticketing::whereNull('event_id')->pluck('id')->toArray();
        }

        $allSelected = empty(array_diff($ticketIds, $this->selectedTickets));

        if ($allSelected) {
            $this->selectedTickets = array_diff($this->selectedTickets, $ticketIds); 
        } else {
            $this->selectedTickets = array_unique(array_merge($this->selectedTickets, $ticketIds));
        }
    }

    public function toggleStatus($ticketId, $column)
    {
        $ticket = Ticketing::find($ticketId);
        if ($ticket) {
            $ticket->$column = !$ticket->$column;
            $ticket->save();
        }
    }

    public function deleteSelected()
    {
        Ticketing::whereIn('id', $this->selectedTickets)->delete();
        $this->selectedTickets = [];
        Notification::make()->title('Tiket berhasil dihapus')->success()->send();
    }

    // ==========================================
    // FUNGSI TOGGLE ACTIVE / INACTIVE EVENT
    // ==========================================
    public function toggleEventActive($eventId)
    {
        $event = Event::find($eventId);
        if ($event) {
            $event->is_active = !$event->is_active;
            $event->save();
            
            $statusTeks = $event->is_active ? 'Dibuka' : 'Ditutup';
            Notification::make()
                ->title("Pendaftaran Event Berhasil {$statusTeks}")
                ->success()
                ->send();
        }
    }

    // ==========================================
    // FUNGSI HAPUS EVENT (TANPA HAPUS FOLDER GDRIVE)
    // ==========================================
    public function deleteEvent($eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) return;

        // 1. Hapus semua antrean (tiket) yang ada di dalam event ini
        Ticketing::where('event_id', $event->id)->delete();

        // 2. Hapus Event-nya dari Database
        $event->delete();

        // Tampilkan Notifikasi Baru
        Notification::make()
            ->title('Event dan Tiket berhasil dihapus (Folder GDrive aman)!')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_ticketing')
                ->label('Export Terpilih (' . count($this->selectedTickets) . ')')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray')
                ->disabled(count($this->selectedTickets) === 0)
                ->action(function () {
                    // Logika export CSV
                }),

            Actions\Action::make('add_event')
                ->label('Add Event')
                ->icon('heroicon-o-plus')
                ->modalHeading('Buat Event Baru')
                ->form([
                    TextInput::make('nama_event')->label('Nama Event')->required(),
                    DatePicker::make('tanggal_event')->label('Tanggal Event')->default(now())->required(),
                    Toggle::make('is_active')->label('Buka Pendaftaran?')->default(true),
                ])
                ->action(function (array $data) {
                    $folderName = $data['nama_event'] . ' - ' . $data['tanggal_event'];
                    
                    try {
                        $drive = app(GoogleDriveService::class);
                        $accessToken = $drive->getValidAccessToken();

                        if (!$accessToken) {
                            Notification::make()->title('Gagal: Token GDrive tidak valid')->danger()->send();
                            return;
                        }

                        $folderPusat = DB::table('settings')->where('key', 'google_upload_folder')->first();
                        $parentId = $folderPusat ? (json_decode($folderPusat->value, true)['folder_id'] ?? null) : null;

                        $response = Http::withToken($accessToken)
                            ->post('https://www.googleapis.com/drive/v3/files', [
                                'name' => $folderName,
                                'mimeType' => 'application/vnd.google-apps.folder',
                                'parents' => $parentId ? [$parentId] : []
                            ]);

                        if ($response->successful()) {
                            $folderId = $response->json()['id'];

                            // 1. Simpan ke tabel events
                            Event::create([
                                'nama_event' => $data['nama_event'],
                                'tanggal_event' => $data['tanggal_event'],
                                'is_active' => $data['is_active'],
                                'gdrive_folder_id' => $folderId,
                            ]);

                            // 2. Simpan ke tabel albums Nuxt
                            DB::table('albums')->insert([
                                'name' => $data['nama_event'], 
                                'paket' => 'Self Photo',
                                'folder_id' => $folderId,
                                'drive_link' => 'https://drive.google.com/drive/folders/' . $folderId,
                                'group_name' => $folderName,
                                'expires_at' => now()->addDays(14)->timestamp,
                                'created_at' => now(),
                                // 'updated_at' => now(),
                            ]);

                            Notification::make()->title('Event & Folder Berhasil Dibuat!')->success()->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()->title('Error membuat folder')->danger()->send();
                    }
                }),
        ];
    }
}