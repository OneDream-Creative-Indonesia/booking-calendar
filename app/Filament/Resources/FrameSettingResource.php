<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FrameSettingResource\Pages;
use App\Models\FrameSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class FrameSettingResource extends Resource
{
    protected static ?string $model = FrameSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-viewfinder-circle';
    protected static ?string $navigationLabel = 'Frame Settings';
    protected static ?string $navigationGroup = 'Section Settings';
    protected static ?int $navigationSort = 5;   
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Frame')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Frame')
                            ->required()
                            ->maxLength(255),
                        Select::make('type_id')
                            ->label('Tipe Frame (Folder)')
                            ->relationship('type', 'name')
                            ->required(),
                        Select::make('orientation')
                            ->label('Orientasi')
                            ->options([
                                'portrait' => 'Portrait',
                                'landscape' => 'Landscape',
                            ])
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('framesSettings')
                            ->label('Upload Frame (Format PNG Transparan)')
                            ->collection('framesSettings')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Frame')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type.name')->label('Tipe Frame')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('orientation')->label('Orientasi'),
                SpatieMediaLibraryImageColumn::make('framesSettings')->label('Foto Frame')->collection('framesSettings'),
            ])
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFrameSettings::route('/'),
            'create' => Pages\CreateFrameSetting::route('/create'),
            'edit' => Pages\EditFrameSetting::route('/{record}/edit'),
        ];
    }
}