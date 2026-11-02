<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketingResource\Pages;
use App\Models\Ticketing;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;

class TicketingResource extends Resource
{
    protected static ?string $model = Ticketing::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Photobooth';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;

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
                Select::make('event_id')
                    ->label('Pilih Event')
                    ->options(Event::pluck('nama_event', 'id'))
                    ->searchable()
                    ->placeholder('Antrean Tanpa Event'),
                
                TextInput::make('nama')
                    ->label('Nama')
                    ->required(),
                
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                
                TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->label('Jumlah Orang'),
                
                TextInput::make('cetak')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->label('Jumlah Cetak (Strip)'),

                TextInput::make('additional')
                    ->numeric()
                    ->default(0)
                    ->label('Jumlah Additional'),
                
                TextInput::make('telpon')
                    ->label('Nomor Telfon')
                    ->numeric()
                    ->placeholder('08xx-xxxx-xxxx')
                    ->required(),
                
                Select::make('transaction_type')
                    ->options([
                        'tunai' => 'Tunai',
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
                Tables\Columns\TextColumn::make('nama')->label('Nama'),
            ]);
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

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_ticketing') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_ticketing') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update_ticketing') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete_ticketing') ?? false;
    }
}