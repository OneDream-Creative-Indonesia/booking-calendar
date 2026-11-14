<?php

namespace App\Filament\Resources\TicketingResource\Pages;

use App\Filament\Resources\TicketingResource;
use App\Models\Event;
use App\Models\Ticketing;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
    // FUNGSI PILIH BACKGROUND (1 ATAU 2, HANYA SATU & BISA ON/OFF)
    // ==========================================
    public function selectBackground($ticketId, $choice)
    {
        // Role event_viewer, event, dan super_admin dapat memilih / menonaktifkan pilihan background
        abort_unless(auth()->user()?->can('view_any_ticketing'), 403, 'Anda tidak memiliki hak untuk mengubah background.');

        $choice = (int) $choice;
        if (!in_array($choice, [1, 2], true)) {
            return;
        }

        \App\Http\Controllers\PanitiaDashboardController::ensureColumnsExist();

        $ticket = Ticketing::find($ticketId);
        if ($ticket) {
            $current = (int) ($ticket->bg_choice ?? 0);
            $newValue = ($current === $choice) ? null : $choice;

            DB::table('ticketings')
                ->where('id', $ticketId)
                ->update(['bg_choice' => $newValue]);
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
                'Background',
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
                    $ticket->bg_choice ? ('BG ' . $ticket->bg_choice) : '-',
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
                    'is_background_active' => (bool) ($event->is_background_active ?? true),
                    'revenue_share_percent' => (float) ($event->revenue_share_percent ?? 0),
                    'panitia_pin' => $event->panitia_pin ?? '',
                    'video_path' => $event->video_path ?? null,
                ] : [];
            })
            ->form([
                TextInput::make('nama_event')
                    ->label('Nama Event')
                    ->required(),
                DatePicker::make('tanggal_event')
                    ->label('Tanggal Event')
                    ->required(),
                FileUpload::make('video_path')
                    ->label('Upload Video Antrean / Waiting List')
                    ->disk('public')
                    ->directory('event-videos')
                    ->visibility('public')
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/webm'])
                    ->maxSize(256000)
                    ->helperText('Upload video MP4 untuk display antrean. Kosongkan untuk pakai default.'),
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
                Toggle::make('is_background_active')
                    ->label('Aktifkan Pilihan Background (1 & 2)?')
                    ->default(true),
                TextInput::make('revenue_share_percent')
                    ->label('Bagi Hasil Panitia (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->default(0)
                    ->helperText('Persentase bagi hasil untuk panitia pada Live Dashboard Panitia.'),
                TextInput::make('panitia_pin')
                    ->label('Kode PIN Akses Panitia (Opsional)')
                    ->maxLength(32)
                    ->placeholder('Kosongkan jika link panitia bisa dibuka langsung tanpa PIN')
                    ->helperText('Jika diisi, panitia harus memasukkan PIN ini saat membuka link dashboard.'),
            ])
            ->action(function (array $data, array $arguments): void {
                $eventId = $arguments['id'] ?? null;
                if ($eventId) {
                    \App\Http\Controllers\PanitiaDashboardController::ensureColumnsExist();
                    $stripPrice = (float) preg_replace('/[^0-9]/', '', (string) ($data['strip_price'] ?? 0));
                    $isAdditional = (bool) ($data['is_additional_active'] ?? false);
                    $additionalPrice = $isAdditional ? (float) preg_replace('/[^0-9]/', '', (string) ($data['additional_price'] ?? 0)) : 0;
                    $isBackground = (bool) ($data['is_background_active'] ?? true);
                    $sharePercent = max(0, min(100, (float) ($data['revenue_share_percent'] ?? 0)));
                    $panitiaPin = trim((string) ($data['panitia_pin'] ?? '')) ?: null;

                    if (!Schema::hasColumn('events', 'video_path')) {
                        Schema::table('events', function ($table) {
                            $table->string('video_path')->nullable();
                        });
                    }

                    $existingEvent = Event::find($eventId);
                    $slug = ($existingEvent && !empty($existingEvent->slug))
                        ? $existingEvent->slug
                        : Str::slug($data['nama_event']);

                    DB::table('events')->where('id', $eventId)->update([
                        'nama_event' => $data['nama_event'],
                        'slug' => $slug,
                        'tanggal_event' => $data['tanggal_event'],
                        'strip_price' => $stripPrice,
                        'is_additional_active' => $isAdditional ? 1 : 0,
                        'additional_price' => $additionalPrice,
                        'is_background_active' => $isBackground ? 1 : 0,
                        'revenue_share_percent' => $sharePercent,
                        'panitia_pin' => $panitiaPin,
                        'video_path' => $data['video_path'] ?? null,
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

                    FileUpload::make('video_path')
                        ->label('Upload Video Antrean / Waiting List')
                        ->disk('public')
                        ->directory('event-videos')
                        ->visibility('public')
                        ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/webm'])
                        ->maxSize(256000)
                        ->helperText('Upload video MP4 untuk display antrean. Kosongkan jika ingin video default.'),

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

                    Toggle::make('is_background_active')
                        ->label('Aktifkan Pilihan Background (1 & 2)?')
                        ->default(true),

                    TextInput::make('revenue_share_percent')
                        ->label('Bagi Hasil Panitia (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->default(0)
                        ->helperText('Persentase bagi hasil untuk panitia pada Live Dashboard Panitia.'),

                    TextInput::make('panitia_pin')
                        ->label('Kode PIN Akses Panitia (Opsional)')
                        ->maxLength(32)
                        ->placeholder('Kosongkan jika link panitia bisa dibuka langsung tanpa PIN'),

                    Toggle::make('is_active')
                        ->label('Buka Pendaftaran?')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    \App\Http\Controllers\PanitiaDashboardController::ensureColumnsExist();
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
                            $isBackground = (bool) ($data['is_background_active'] ?? true);
                            $sharePercent = max(0, min(100, (float) ($data['revenue_share_percent'] ?? 0)));
                            $panitiaPin = trim((string) ($data['panitia_pin'] ?? '')) ?: null;

                            if (!Schema::hasColumn('events', 'video_path')) {
                                Schema::table('events', function ($table) {
                                    $table->string('video_path')->nullable();
                                });
                            }

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
                                'is_background_active' => $isBackground,
                                'revenue_share_percent' => $sharePercent,
                                'panitia_pin' => $panitiaPin,
                                'video_path' => $data['video_path'] ?? null,
                            ]);
                            Event::reguard();

                            DB::table('events')->where('id', $newEvent->id)->update([
                                'strip_price' => $stripPrice,
                                'is_additional_active' => $isAdditional ? 1 : 0,
                                'additional_price' => $additionalPrice,
                                'is_background_active' => $isBackground ? 1 : 0,
                                'revenue_share_percent' => $sharePercent,
                                'panitia_pin' => $panitiaPin,
                                'video_path' => $data['video_path'] ?? null,
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