<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FrameTypeResource\Pages;
use App\Filament\Resources\FrameTypeResource\RelationManagers;
use App\Models\FrameType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FrameTypeResource extends Resource
{
    protected static ?string $model = FrameType::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationLabel = 'Frame Types';
    protected static ?string $navigationGroup = 'Section Settings';
    protected static ?int $navigationSort = 6;   
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Judul Tipe Frame')
                ->placeholder('Contoh: Classic 4R (4x6)'),

            Forms\Components\TextInput::make('type')
                ->required()
                ->label('Type Frame')
                ->placeholder('Contoh: 4R'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Judul Tipe Frame')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Tipe Frame')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFrameTypes::route('/'),
            'create' => Pages\CreateFrameType::route('/create'),
            'edit' => Pages\EditFrameType::route('/{record}/edit'),
        ];
    }
}
