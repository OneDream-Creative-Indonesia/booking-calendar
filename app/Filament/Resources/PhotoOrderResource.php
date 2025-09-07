<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoOrderResource\Pages;
use App\Models\PhotoOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PhotoOrderResource extends Resource
{
    protected static ?string $model = PhotoOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('type')
                    ->label('Tipe')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('warna')
                    ->label('Warna')
                    ->options([
                        'white' => 'Putih',
                        'black' => 'Hitam',
                        'blue'  => 'Biru',
                    ])
                    ->required(),

                Forms\Components\SpatieMediaLibraryFileUpload::make('layout')
                    ->collection('layout')
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pending',
                        'processed' => 'Diproses',
                        'done'      => 'Selesai',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->sortable(),

                Tables\Columns\TextColumn::make('warna')
                    ->label('Warna')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'processed',
                        'success' => 'done',
                    ]),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('layout')
                    ->collection('layout')
                    ->label('Layout')
                    ->size(200),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPhotoOrders::route('/'),
            'create' => Pages\CreatePhotoOrder::route('/create'),
            'edit'   => Pages\EditPhotoOrder::route('/{record}/edit'),
        ];
    }
}
