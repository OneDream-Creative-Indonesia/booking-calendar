<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteGalleryItemResource\Pages;
use App\Models\SiteGalleryItem;
use App\Support\Cms;
use App\Support\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteGalleryItemResource extends Resource
{
    protected static ?string $model = SiteGalleryItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Website CMS';
    protected static ?string $navigationLabel = 'Galeri Foto';
    protected static ?string $modelLabel = 'Foto Galeri';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool { return Cms::allowed(); }
    public static function canCreate(): bool { return Cms::allowed(); }
    public static function canEdit($record): bool { return Cms::allowed(); }
    public static function canDelete($record): bool { return Cms::allowed(); }
    public static function canDeleteAny(): bool { return Cms::allowed(); }

    public static function form(Form $form): Form
    {
        Site::syncDefaultAssetsToPublicDisk();

        return $form->schema([
            Forms\Components\Select::make('group')->label('Tampil di Halaman')->options([
                'studio' => 'Snap Fun Studio (Foto Customer)',
                'booth' => 'Snap Fun Booth (Dokumentasi Event)',
            ])->required()->default('studio'),
            Forms\Components\TextInput::make('sort')->label('Urutan Tampil')->numeric()->default(0),
            Forms\Components\FileUpload::make('image')->label('Foto Galeri')->image()->required()->disk('public')
                ->directory('site/gallery')->imageEditor()->maxSize(15360)->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->label('Tampilkan di Website')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        Site::ensureSeeded();

        return $table->columns([
            Tables\Columns\ImageColumn::make('image')
                ->label('Foto')
                ->getStateUsing(fn ($record) => Site::url($record->image))
                ->height(85)
                ->width(110)
                ->extraImgAttributes(['style' => 'object-fit:cover;border-radius:10px']),
            Tables\Columns\TextColumn::make('group')->label('Kategori Halaman')->badge()
                ->color(fn ($state) => $state === 'booth' ? 'warning' : 'info')
                ->formatStateUsing(fn ($state) => $state === 'booth' ? 'Snap Fun Booth (Event)' : 'Snap Fun Studio'),
            Tables\Columns\TextColumn::make('sort')->label('Urutan')->sortable(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->defaultSort('sort')->reorderable('sort')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Kategori Halaman')
                    ->options(['studio' => 'Snap Fun Studio', 'booth' => 'Snap Fun Booth']),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageSiteGalleryItems::route('/')];
    }
}
