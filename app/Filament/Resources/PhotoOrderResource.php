<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoOrderResource\Pages;
use App\Models\PhotoOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PhotoOrderResource extends Resource
{
    protected static ?string $model = PhotoOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

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
                Forms\Components\Select::make('frame_id')
                    ->label('Frame')
                    ->relationship('frame', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\Select::make('warna')
                    ->label('Warna')
                    ->options([
                        'white' => 'Putih',
                        'black' => 'Hitam',
                        'blue'  => 'Biru',
                    ])
                    ->required(),

                Forms\Components\Placeholder::make('frame_preview')
                    ->label('Preview Frame')
                    ->content(function ($record) {
                        if (!$record || !$record->frame) return '-';

                        $url = $record->frame->getFirstMediaUrl('frames');

                        if (!$url) return '-';

                        return new \Illuminate\Support\HtmlString(
                            "<img src='{$url}' style='height:200px;' />"
                        );
                    })
                    ->visible(fn ($record) => $record && $record->frame),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pending',
                        'processed' => 'Diproses',
                        'done'      => 'Selesai',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('layout')
                    ->collection('layout')
                    ->columnSpanFull(),
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
                
                 Tables\Columns\TextColumn::make('frame.name')
                    ->label('Frame')
                    ->badge()
                    ->color('primary'),

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
                Tables\Columns\ImageColumn::make('frame_image')
                    ->label('Frame Image')
                    ->getStateUsing(function ($record) {
                        return $record->frame?->getFirstMediaUrl('frames');
                    })
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
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('frame');
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
