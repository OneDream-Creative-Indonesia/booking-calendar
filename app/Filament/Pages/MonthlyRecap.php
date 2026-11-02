<?php

namespace App\Filament\Pages;

use App\Models\Background;
use App\Models\Booking;
use App\Models\Package;
use App\Services\BookingReportService;
use App\Services\GoogleSheetsService;
use App\Support\XlsxWriter;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class MonthlyRecap extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'E-Statement Booking';
    protected static ?string $title = 'E-Statement Booking';
    protected static ?string $navigationGroup = 'Laporan';

    protected static string $view = 'filament.pages.monthly-recap';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_booking') ?? false;
    }

    /** Pilihan di form (belum diterapkan sampai klik "Tarik Data"). */
    public int $selectedYear;
    public int $selectedMonth;

    /** Periode yang sedang ditampilkan. */
    public int $year;
    public int $month;

    public bool $showInfographic = false;
    public bool $showArchive = false;

    public function mount(): void
    {
        $this->selectedYear = $this->year = (int) now()->format('Y');
        $this->selectedMonth = $this->month = (int) now()->format('n');
    }

    public function getYearOptions(): array
    {
        $years = Booking::query()->selectRaw('YEAR(date) as y')->distinct()->pluck('y')->filter()->map(fn ($y) => (int) $y)->all();
        $years[] = (int) now()->format('Y');
        rsort($years);

        return array_combine(array_unique($years), array_unique($years));
    }

    public function getMonthOptions(): array
    {
        return BookingReportService::MONTHS;
    }

    public function pullData(): void
    {
        $this->year = $this->selectedYear;
        $this->month = $this->selectedMonth;
        $this->resetTable();

        Notification::make()
            ->title('Data ' . BookingReportService::monthName($this->month) . " {$this->year} berhasil ditarik")
            ->success()->send();
    }

    public function toggleInfographic(): void
    {
        $this->showInfographic = ! $this->showInfographic;
    }

    public function getPeriodLabel(): string
    {
        return BookingReportService::monthName($this->month) . ' ' . $this->year;
    }

    public function getInfographic(): array
    {
        $svc = app(BookingReportService::class);

        return $svc->summary($svc->bookings($this->year, $this->month, true));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Booking::query()
                ->with(['package', 'background', 'voucher', 'bookingAddons.addon'])
                ->whereYear('date', $this->year)
                ->whereMonth('date', $this->month)
                ->when(\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'statement_hidden_at'), fn ($q) => $q->whereNull('statement_hidden_at')))
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('time')->label('Waktu')->formatStateUsing(fn ($state) => substr((string) $state, 0, 5)),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('package.title')->label('Jenis Booking')->badge()->sortable(),
                Tables\Columns\TextColumn::make('people_count')->label('Orang')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Total Harga')
                    ->state(fn (Booking $r) => BookingReportService::rupiah(BookingReportService::total($r))),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'success', 'confirmed' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    })->formatStateUsing(fn ($state) => ucfirst($state)),
                // Kolom detail: disembunyikan secara default, bisa ditampilkan lewat tombol kolom.
                Tables\Columns\TextColumn::make('whatsapp')->label('WhatsApp')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('background.name')->label('Background')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('voucher.code_voucher')->label('Voucher')->placeholder('-')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')->label('Harga Paket')->money('IDR', locale: 'id')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('addon_total')->label('Harga Add-on')
                    ->state(fn (Booking $r) => BookingReportService::rupiah(BookingReportService::addonTotal($r)))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keychain_notes')->label('Catatan Keychain')->placeholder('-')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->multiple()->options([
                    'pending' => 'Pending', 'confirmed' => 'Confirmed', 'success' => 'Success', 'cancelled' => 'Cancelled',
                ]),
                Tables\Filters\SelectFilter::make('package_id')->label('Paket')->multiple()
                    ->options(fn () => Package::pluck('title', 'id')->all()),
                Tables\Filters\SelectFilter::make('background_id')->label('Background')->multiple()
                    ->options(fn () => Background::pluck('name', 'id')->all()),
            ], layout: FiltersLayout::Dropdown)
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->searchPlaceholder('Cari nama / WhatsApp / email')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('removeFromStatement')
                        ->label('Hapus dari E-Statement')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus dari E-Statement?')
                        ->modalDescription('Data hanya dihapus dari tampilan E-Statement. Data di List Booking, dashboard, dan spreadsheet tidak ikut terhapus.')
                        ->action(function (\Illuminate\Support\Collection $records) {
                            if (!\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'statement_hidden_at')) {
                                \Illuminate\Support\Facades\Schema::table('bookings', function (\Illuminate\Database\Schema\Blueprint $table) {
                                    $table->timestamp('statement_hidden_at')->nullable();
                                });
                            }
                            Booking::whereIn('id', $records->pluck('id'))->update(['statement_hidden_at' => now()]);
                            Notification::make()->title($records->count() . ' data dihapus dari E-Statement')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Tidak ada booking pada periode ini')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => $this->exportExcel()),
            Action::make('syncSheets')
                ->label('Sinkron Spreadsheet')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Sinkron ke Google Spreadsheet')
                ->modalDescription(fn () => 'Data ' . $this->getPeriodLabel() . ' akan digabungkan ke spreadsheet "E-Statement Booking ' . $this->year . '" pada tab ' . BookingReportService::monthName($this->month) . '. Tab baru dibuat otomatis jika belum ada. Baris lama di spreadsheet tidak akan dihapus.')
                ->action(fn () => $this->syncSheets()),
        ];
    }

    public function exportExcel()
    {
        $svc = app(BookingReportService::class);
        $bookings = $svc->bookings($this->year, $this->month, true);

        $summary = $svc->summary($bookings);
        $recap = [['Ringkasan', 'Nilai'],
            ['Periode', $this->getPeriodLabel()],
            ['Jumlah Booking', $summary['bookings']],
            ['Booking Dibatalkan', $summary['cancelled']],
            ['Jumlah Pengunjung', $summary['visitors']],
            ['Total Pendapatan', (int) $summary['revenue']],
            ['', ''],
            ['Paket', 'Jumlah Booking'],
        ];
        foreach ($summary['packages'] as $p) {
            $recap[] = [$p['name'], $p['bookings']];
        }

        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        (new XlsxWriter())
            ->addSheet(BookingReportService::monthName($this->month), $svc->tableRows($bookings))
            ->addSheet('Ringkasan', $recap)
            ->save($path);

        $name = 'e-statement-booking-' . $this->year . '-' . str_pad((string) $this->month, 2, '0', STR_PAD_LEFT) . '.xlsx';

        return response()->download($path, $name, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function syncSheets(): void
    {
        try {
            $result = app(GoogleSheetsService::class)->syncMonth($this->year, $this->month);
            $url = $result['url'];

            Notification::make()->title('Sinkronisasi berhasil')
                ->body('Tab ' . BookingReportService::monthName($this->month) . " di spreadsheet {$this->year} diperbarui ({$result['total']} baris)."
                    . ($result['kept'] ? " {$result['kept']} baris yang sudah dihapus dari web tetap disimpan di spreadsheet." : ''))
                ->actions([\Filament\Notifications\Actions\Action::make('open')->label('Buka Spreadsheet')->url($url, shouldOpenInNewTab: true)])
                ->success()->persistent()->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title('Sinkronisasi gagal')->body($e->getMessage())->danger()->persistent()->send();
        }
    }

    public function getRecapFiles(): array
    {
        if (! Storage::disk('local')->exists('recaps')) {
            return [];
        }

        $recaps = [];
        foreach (Storage::disk('local')->files('recaps') as $file) {
            $filename = basename($file);
            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'xml') {
                continue;
            }
            if (preg_match('/rekap_booking_(\d{4})_(\d+)/', $filename, $m)) {
                $recaps[] = [
                    'title' => 'Arsip ' . BookingReportService::monthName((int) $m[2]) . " {$m[1]}",
                    'path' => $file,
                    'size' => round(Storage::disk('local')->size($file) / 1024, 2) . ' KB',
                    'timestamp' => Storage::disk('local')->lastModified($file),
                ];
            }
        }
        usort($recaps, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $recaps;
    }

    public function downloadRecap($path)
    {
        if (str_starts_with($path, 'recaps/') && ! str_contains($path, '..') && Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }
    }
}
