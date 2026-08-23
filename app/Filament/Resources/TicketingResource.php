<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketingResource\Pages;
use App\Models\Ticketing;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use App\Models\Event;
class TicketingResource extends Resource
{
    protected static ?string $model = Ticketing::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Photobooth';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Register');
    }

    public function getTitle(): string|Htmlable
    {
        return self::getNavigationLabel();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TAMBAHKAN BLOK INI DI PALING ATAS
                Select::make('event_id')
                    ->label('Pilih Event')
                    ->options(Event::pluck('nama_event', 'id'))
                    ->searchable()
                    ->placeholder('Antrean Tanpa Event'),
                
                // KODE LAMA KAMU TETAP DI BAWAHNYA
                TextInput::make('nama')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->label("Jumlah Orang"),
                TextInput::make('cetak')
                    ->required()
                    ->numeric()
                    ->label('Jumlah Cetak'),
                TextInput::make('telpon')
                    ->label('Nomor Telfon')
                    ->numeric()
                    ->placeholder('08xx-xxxx-xxxx')
                    ->required(),
                Select::make('transaction_type')
                    ->options([
                        'tunai' => "Tunai",
                        'qris' => 'Qris'
                    ])
                    ->label('Jenis Pembayaran')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Orang')
                    ->summarize(Sum::make()->label(''))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cetak')
                    ->sortable()
                    ->summarize(Sum::make()->label(''))
                    ->searchable()
                    ->label('Cetak'),
                Tables\Columns\TextColumn::make('telpon')
                    ->label('No. Hp')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Pembayaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('queue_number')
                    ->label('Antrian')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\CheckboxColumn::make('is_foto')
                    ->label('Foto')
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('is_export')
                    ->label('Export')
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('is_print')
                    ->label('Print')
                    ->sortable(),
            ])
            // Grouping Berdasarkan Event & Tanggal (Sesuai Desainmu)
            ->groups([
    Group::make('event_id')
        ->label('Event')
        ->getKeyFromRecordUsing(function (Model $record) {
            return $record->event_id !== null
                ? (string) $record->event_id
                : 'no-event';
        })
        ->getTitleFromRecordUsing(function (Model $record) {
            if (!$record->event) {
                return 'Antrean Tanpa Event';
            }
            $tanggal = Carbon::parse($record->event->tanggal_event)->translatedFormat('d F Y');
            return $record->event->nama_event . ' - ' . $tanggal;
        })
        ->collapsible(),
])
            ->defaultGroup('event_id')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketings::route('/'),
            'edit' => Pages\EditTicketing::route('/{record}/edit'),
        ];
    }

    public static function getPluralLabel(): string
    {
        return 'Ticketing';
    }
}