<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BackgroundResource\Pages;
use App\Filament\Resources\BackgroundResource\RelationManagers;
use App\Models\Background;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
class BackgroundResource extends Resource
{
    protected static ?string $model = Background::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Section::make()
                ->schema([
                   Forms\Components\TextInput::make('name')
                ->label('Nama Background')
                ->required()
                ->maxLength(255),

                Forms\Components\FileUpload::make('image')
                ->label('Gambar Background')
                ->image()
                ->directory('backgrounds')
                ->imagePreviewHeight('150')
                ->imageEditor()
                ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\ImageColumn::make('image')
                ->label('Preview')
                ->disk('public')
                ->height(60)
                ->width(80),

            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable(),
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
            'index' => Pages\ListBackgrounds::route('/'),
            'create' => Pages\CreateBackground::route('/create'),
            'edit' => Pages\EditBackground::route('/{record}/edit'),
        ];
    }
}
