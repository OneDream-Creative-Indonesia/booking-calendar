<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockedDaysResource\Pages;
use App\Filament\Resources\BlockedDaysResource\RelationManagers;
use App\Models\BlockedDate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;


class BlockedDaysResource extends Resource
{
    protected static ?string $model = BlockedDate::class;

    protected static ?string $navigationIcon = 'heroicon-o-x-mark';
    protected static ?string $navigationLabel = 'Studio Tidak Tersedia';
    protected static ?string $pluralModelLabel = 'Studio Tidak Tersedia';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
             Section::make('Tandai Studio Tidak Tersedia')
                ->schema([
                    DatePicker::make('date')
                        ->label('Tanggal')
                        ->required(),

                    Select::make('reason')
                        ->label('Alasan')
                        ->required()
                        ->options([
                            'holiday' => 'Libur Studio',
                            'bookingall' => 'Disewa Seharian',
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat('d F Y')),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'holiday' => 'Libur Studio',
                        'bookingall' => 'Disewa Seharian',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'holiday' => 'danger',
                        'bookingall' => 'warning',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
            ])
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
            'index' => Pages\ListBlockedDays::route('/'),
            'create' => Pages\CreateBlockedDays::route('/create'),
            'edit' => Pages\EditBlockedDays::route('/{record}/edit'),
        ];
    }
}
