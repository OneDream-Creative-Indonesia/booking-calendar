<?php

namespace App\Filament\Resources\TicketingResource\Pages;

use App\Filament\Resources\TicketingResource;
use App\Models\Event;
use App\Models\Ticketing;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use App\Services\GoogleDriveService;
use Illuminate\Support\Str; 

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
        // Event view masih bisa ceklis bagian section foto
        if ($column === 'is_foto') {
            abort_unless(auth()->user()?->can('view_any_ticketing'), 403, 'Anda tidak memiliki hak untuk mengubah status foto.');
        } else {
            abort_unless(auth()->user()?->can('update_ticketing'), 403, 'Anda tidak memiliki hak untuk mengubah status ini.');
        }

        $ticket = Ticketing::find($ticketId);
        if ($ticket) {
            $ticket->$column = !$ticket->$column;
            $ticket->save();
        }
    }

    // ==========================================
    // FUNGSI TAMBAH (+) & KURANG (-) ORANG, CETAK, ADDITIONAL
    // ==========================================
    public function updateCounter($ticketId, $column, $change)
    {
        abort_unless(auth()->user()?->can('update_ticketing'), 403, 'Anda tidak memiliki hak untuk mengubah data.');
        if (!in_array($column, ['jumlah', 'cetak', 'additional'])) {
            return;
        }

        $ticket = Ticketing::find($ticketId);
        if ($ticket) {
            $currentValue = (int) ($ticket->$column ?? 0);
            $newValue = max(0, $currentValue + (int) $change);

            DB::table('ticketings')
                ->where('id', $ticketId)
                ->update([$column => $newValue]);
        }
    }

    // ==========================================
    // FUNGSI EXPORT CSV PER FOLDER EVENT
    // ==========================================
    public function exportCsv($eventId = null)
    {
        $query = Ticketing::query();
        $namaEvent = 'Tanpa-Event';
        $event = null;

        if ($eventId && $eventId !== 'no-event') {
            $event = Event::find($eventId);
            $namaEvent = $event ? Str::slug($event->nama_event) : 'Event';
            $query->where('event_id', $eventId);
        } else {
            $query->whereNull('event_id');
        }

        $allEventTicketIds = (clone $query)->pluck('id')->toArray();
        $selectedInThisEvent = array_intersect($this->selectedTickets, $allEventTicketIds);

        if (!empty($selectedInThisEvent)) {
            $query->whereIn('id', $selectedInThisEvent);
        }

        $tickets = $query->orderBy('id', 'asc')->get();

        if ($tickets->isEmpty()) {
            Notification::make()
                ->title('Tidak ada data antrean untuk di-export pada event ini.')
                ->warning()
                ->send();
            return;
        }

        $fileName = 'Export-' . $namaEvent . '-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($tickets, $event) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            $stripPrice = $event ? ($event->strip_price ?? 0) : 0;
            $addPrice = ($event && $event->is_additional_active) ? ($event->additional_price ?? 0) : 0;

            fputcsv($handle, [
                'No. Antrean',
                'Nama',
                'Email',
                'No. HP',
                'Jumlah Orang',
                'Jumlah Cetak',
                'Jumlah Additional',
                'Pembayaran',
                'Pendapatan',
                'Status',
                'Foto',
                'Export',
                'Print',
                'Waktu Daftar',
            ]);

            foreach ($tickets as $ticket) {
                $pendapatan = ($stripPrice * (int) $ticket->cetak) + ($addPrice * (int) ($ticket->additional ?? 0));

                fputcsv($handle, [
                    $ticket->queue_number ?? '#' . $ticket->id,
                    $ticket->nama,
                    $ticket->email,
                    $ticket->telpon,
                    (int) $ticket->jumlah,
                    (int) $ticket->cetak,
                    (int) ($ticket->additional ?? 0),
                    strtoupper($ticket->transaction_type ?? '-'),
                    $pendapatan,
                    $ticket->status ?? 'menunggu',
                    $ticket->is_foto ? 'Ya' : 'Tidak',
                    $ticket->is_export ? 'Ya' : 'Tidak',
                    $ticket->is_print ? 'Ya' : 'Tidak',
                    $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ==========================================
    // FUNGSI PANGGIL ANTREAN DARI ADMIN
    // ==========================================
    public function panggilAntrean($ticketId)
    {
        $ticket = Ticketing::find($ticketId);
        
        if ($ticket) {
            $ticket->status = 'dipanggil'; 
            $ticket->save();

            $nomorAntrean = $ticket->queue_number ?? '#' . $ticket->id;

            Notification::make()
                ->title("Antrean {$nomorAntrean} berhasil dipanggil!")
                ->success()
                ->send();
        }
    }

    // ==========================================
    // FUNGSI HAPUS 1 BARIS TIKET / LIST
    // ==========================================
    public function deleteTicket($ticketId)
    {
        abort_unless(auth()->user()?->can('delete_ticketing'), 403, 'Anda tidak memiliki hak untuk menghapus data.');
        $ticket = Ticketing::find($ticketId);
        if ($ticket) {
            $ticket->delete();
            Notification::make()
                ->title('Data antrean berhasil dihapus!')
                ->success()
                ->send();
        }
    }

    // ==========================================
    // FUNGSI POP-UP EDIT EVENT & HARGA STRIP
    // ==========================================
    public function editEventAction(): Action
    {
        return Action::make('editEvent')
            ->visible(fn () => auth()->user()?->can('update_ticketing') ?? false)
            ->modalHeading('Edit Event & Harga')
            ->fillForm(function (array $arguments): array {
                $event = Event::find($arguments['id'] ?? null);
                return $event ? [
                    'nama_event' => $event->nama_event,
                    'tanggal_event' => $event->tanggal_event,
                    'strip_price' => (int) ($event->strip_price ?? 0),
                    'is_additional_active' => (bool) ($event->is_additional_active ?? false),
                    'additional_price' => (int) ($event->additional_price ?? 0),
                ] : [];
            })
            ->form([
                TextInput::make('nama_event')
                    ->label('Nama Event')
                    ->required(),
                DatePicker::make('tanggal_event')
                    ->label('Tanggal Event')
                    ->required(),
                TextInput::make('strip_price')
                    ->label('Harga Strip')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Toggle::make('is_additional_active')
                    ->label('Aktifkan Fitur Additional?')
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (!$state) {
                            $set('additional_price', 0);
                        }
                    }),
                TextInput::make('additional_price')
                    ->label('Harga Additional')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->visible(fn (Get $get): bool => (bool) $get('is_additional_active'))
                    ->required(fn (Get $get): bool => (bool) $get('is_additional_active')),
            ])
            ->action(function (array $data, array $arguments): void {
                $eventId = $arguments['id'] ?? null;
                if ($eventId) {
                    $stripPrice = (float) preg_replace('/[^0-9]/', '', (string) ($data['strip_price'] ?? 0));
                    $isAdditional = (bool) ($data['is_additional_active'] ?? false);
                    $additionalPrice = $isAdditional ? (float) preg_replace('/[^0-9]/', '', (string) ($data['additional_price'] ?? 0)) : 0;

                    DB::table('events')->where('id', $eventId)->update([
                        'nama_event' => $data['nama_event'],
                        'tanggal_event' => $data['tanggal_event'],
                        'strip_price' => $stripPrice,
                        'is_additional_active' => $isAdditional ? 1 : 0,
                        'additional_price' => $additionalPrice,
                        'updated_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Data Event & Harga berhasil diperbarui!')
                        ->success()
                        ->send();
                }
            });
    }

    public function deleteSelected()
    {
        abort_unless(auth()->user()?->can('delete_ticketing'), 403, 'Anda tidak memiliki hak untuk menghapus data.');
        Ticketing::whereIn('id', $this->selectedTickets)->delete();
        $this->selectedTickets = [];
        Notification::make()->title('Tiket berhasil dihapus')->success()->send();
    }

    public function toggleEventActive($eventId)
    {
        abort_unless(auth()->user()?->can('update_ticketing'), 403, 'Anda tidak memiliki hak untuk mengubah data.');
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

    public function deleteEvent($eventId)
    {
        abort_unless(auth()->user()?->can('delete_ticketing'), 403, 'Anda tidak memiliki hak untuk menghapus data.');
        $event = Event::find($eventId);
        
        if (!$event) return;

        Ticketing::where('event_id', $event->id)->delete();
        $event->delete();

        Notification::make()
            ->title('Event dan Tiket berhasil dihapus (Folder GDrive aman)!')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add_event')
                ->visible(fn () => auth()->user()?->can('create_ticketing') ?? false)
                ->label('Add Event')
                ->icon('heroicon-o-plus')
                ->modalHeading('Buat Event Baru')
                ->form([
                    TextInput::make('nama_event')
                        ->label('Nama Event')
                        ->required(),
                    
                    DatePicker::make('tanggal_event')
                        ->label('Tanggal Event')
                        ->default(now())
                        ->required(),

                    TextInput::make('strip_price')
                        ->label('Harga Strip')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required(),

                    Toggle::make('is_additional_active')
                        ->label('Aktifkan Fitur Additional?')
                        ->default(false)
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (!$state) {
                                $set('additional_price', 0);
                            }
                        }),

                    TextInput::make('additional_price')
                        ->label('Harga Additional')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->visible(fn (Get $get): bool => (bool) $get('is_additional_active'))
                        ->required(fn (Get $get): bool => (bool) $get('is_additional_active')),

                    Toggle::make('is_active')
                        ->label('Buka Pendaftaran?')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $folderName = $data['nama_event'] . ' - ' . $data['tanggal_event'];
                    $eventSlug = Str::slug($data['nama_event']);
                    
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

                            $stripPrice = (float) preg_replace('/[^0-9]/', '', (string) ($data['strip_price'] ?? 0));
                            $isAdditional = (bool) ($data['is_additional_active'] ?? false);
                            $additionalPrice = $isAdditional ? (float) preg_replace('/[^0-9]/', '', (string) ($data['additional_price'] ?? 0)) : 0;

                            Event::unguard();
                            $newEvent = Event::create([
                                'nama_event' => $data['nama_event'],
                                'slug' => $eventSlug,
                                'tanggal_event' => $data['tanggal_event'],
                                'is_active' => $data['is_active'],
                                'gdrive_folder_id' => $folderId,
                                'strip_price' => $stripPrice,
                                'is_additional_active' => $isAdditional,
                                'additional_price' => $additionalPrice,
                            ]);
                            Event::reguard();

                            DB::table('events')->where('id', $newEvent->id)->update([
                                'strip_price' => $stripPrice,
                                'is_additional_active' => $isAdditional ? 1 : 0,
                                'additional_price' => $additionalPrice,
                            ]);

                            DB::table('albums')->insert([
                                'id' => strtoupper(Str::random(6)),
                                'name' => $data['nama_event'], 
                                'paket' => 'Photobooth',
                                'folder_id' => $folderId,
                                'drive_link' => 'https://drive.google.com/drive/folders/' . $folderId,
                                'group_name' => $folderName,
                                'expires_at' => now()->addDays(14)->timestamp,
                                'created_at' => now()->timestamp,
                            ]);

                            Notification::make()->title('Event & Folder Berhasil Dibuat!')->success()->send();
                        } else {
                            $errorDetail = $response->json();
                            $pesanError = $errorDetail['error']['message'] ?? 'API Google Error';
                            
                            Notification::make()
                                ->title('Gagal di Google API')
                                ->body($pesanError)
                                ->danger()
                                ->duration(10000)
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('System Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->duration(10000)
                            ->send();
                    }
                }),
        ];
    }
}