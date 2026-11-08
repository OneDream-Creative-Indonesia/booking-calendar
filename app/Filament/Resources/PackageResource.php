<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Paket Settings';
    protected static ?string $navigationGroup = 'Operational';
    protected static ?int $navigationSort = 4;
    public static function form(Form $form): Form
    {
        Package::ensureExtraColumns();

        return $form->schema([
            TextInput::make('title')
                ->label('Nama Paket')
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('duration_minutes')
                ->label('Durasi (menit)')
                ->required()
                ->numeric(),
            TextInput::make('price')
                ->label('Harga (Rp)')
                ->required()
                ->numeric(),
            TextInput::make('downpayment')
                ->label('DP (Rp)')
                ->required()
                ->numeric(),

            TextInput::make('included_people')
                ->label('Untuk Berapa Orang (Kuota Paket)')
                ->numeric()
                ->default(1)
                ->placeholder('Contoh: 1 atau 2'),

            TextInput::make('max_people')
                ->label('Maksimal Orang')
                ->numeric()
                ->placeholder('Contoh: 5 atau 8'),

            TextInput::make('extras_people')
                ->label('Harga per Orang Tambahan (Rp)')
                ->numeric()
                ->placeholder('Masukkan harga per orang jika lebih dari kuota'),

            TextInput::make('extras_duration')
                ->label('Harga Tambah Durasi per 3 Menit (Rp)')
                ->numeric()
                ->placeholder('Contoh: 10000 atau 15000'),

            Select::make('backgrounds')
                ->label('Pilih Background')
                ->multiple()
                ->relationship('backgrounds', 'name')
                ->preload()
                ->searchable(),
            SpatieMediaLibraryFileUpload::make('image')
                ->collection('image')
                ->label('Foto Sampul Paket (Opsional)')
                ->image(),
            Textarea::make('description')
                ->label('Deskripsi (1 baris = 1 poin)')
                ->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        Package::ensureExtraColumns();

        return $table->columns([
            SpatieMediaLibraryImageColumn::make('image')
                ->label('Foto')
                ->collection('image')
                ->height(50)
                ->width(70),
            Tables\Columns\TextColumn::make('title')->searchable()->label('Nama Paket'),
            Tables\Columns\TextColumn::make('price')  ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))->label('Harga'),
             Tables\Columns\TextColumn::make('downpayment')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->label('DP'),
            Tables\Columns\TextColumn::make('extras_people')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->label('Harga per Orang Tambahan'),
            Tables\Columns\TextColumn::make('duration_minutes')->label('Durasi (menit)'),
        ]) ->filters([
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
