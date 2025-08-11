<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Package;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BlockedDate;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BlockedDaysResource\Pages;
use App\Filament\Resources\BlockedDaysResource\RelationManagers;


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

                    TimePicker::make('start_time')
                            ->label('Mulai')
                            ->seconds(false)
                            ->required(),

                    TimePicker::make('end_time')
                            ->label('Selesai')
                            ->seconds(false)
                            ->required(),
                    Select::make('package_ids')
                        ->label('Paket yang Diblokir')
                        ->multiple()
                        ->options(Package::pluck('title', 'id')->toArray())
                        ->required()
                        ->placeholder('Pilih paket'),
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
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y')),

            TextColumn::make('start_time')
                ->label('Mulai')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i')),

            TextColumn::make('end_time')
                ->label('Selesai')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i')),
            TextColumn::make('package_ids')
                    ->label('Paket')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';

                        if (is_string($state) && str_starts_with($state, '[')) {
                            $ids = json_decode($state, true) ?: [];
                        } else {
                            $ids = (array) $state;
                        }

                        return \App\Models\Package::whereIn('id', $ids)->pluck('title')->implode(', ');
                    }),
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
