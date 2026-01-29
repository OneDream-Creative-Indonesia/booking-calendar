<?php

namespace App\Filament\Imports;

use App\Models\Voucher;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Carbon\Carbon;
class VoucherImporter extends Importer
{
    protected static ?string $model = Voucher::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code_voucher')
            ->requiredMapping(),

        ImportColumn::make('discount_percent')
            ->numeric()
            ->requiredMapping()
            ->rules(['required', 'min:0', 'max:100']),

        ImportColumn::make('is_active')
            ->boolean()
            ->rules(['nullable', 'boolean']),

       ImportColumn::make('start_date')
    ->castStateUsing(function ($state, $originalState) {
        return filled($state) ? Carbon::parse($state) : null;
    })
    ->rules(['nullable', 'date']),

ImportColumn::make('end_date')
    ->castStateUsing(function ($state, $originalState) {
        return filled($state) ? Carbon::parse($state) : null;
    })
    ->rules(['nullable', 'date', 'after_or_equal:start_date']),

        ImportColumn::make('usage_limit')
            ->numeric()
            ->rules(['nullable', 'integer', 'min:0']),
    ];
    }

    public function resolveRecord(): ?Voucher
    {
        return Voucher::firstOrNew([
            'code_voucher' => $this->data['code_voucher'],
        ]);

        return new Voucher();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your voucher import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
