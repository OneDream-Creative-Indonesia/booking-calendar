<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketingResource\Pages;
use App\Filament\Resources\TicketingResource\RelationManagers;
use App\Models\Ticketing;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketingResource extends Resource
{
    protected static ?string $model = Ticketing::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?int $navigationSort = 4;
    public static function getNavigationLabel(): string
    {
        return __('Ticketing');
    }

    public function getTitle(): string|Htmlable
    {
        return self::getNavigationLabel();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('jumlah')
                    ->required()
                    ->label("Jumlah Orang"),
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
                    // ->live()
                    // ->reactive()
                    // ->afterStateUpdated(fn ($state) => $this->transaction_type = $state)
                    ->label('Jenis Pembayaran')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Orang')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('telpon')
                    ->label('Nomor Handpone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Jenis Pembayaran')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // SelectFilter::make('no_photo')
                // ->label('Filter Nomor Photo')
                // ->options([
                //     'with_photo' => 'Nomor Photo Terisi',
                //     'without_photo' => 'Nomor Photo Belum Terisi',
                // ])
                // ->query(function (Builder $query, array $data) {
                //     if ($data['value'] === 'with_photo') {
                //         return $query->whereNotNull('no_photo')->where('no_photo', '!=', '');
                //     }
                //     if ($data['value'] === 'without_photo') {
                //         return $query->whereNull('no_photo')->orWhere('no_photo', '');
                //     }
                // }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
