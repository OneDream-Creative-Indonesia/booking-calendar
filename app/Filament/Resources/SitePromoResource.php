<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SitePromoResource\Pages;
use App\Models\SitePromo;
use App\Support\Cms;
use App\Support\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SitePromoResource extends Resource
{
    protected static ?string $model = SitePromo::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Website CMS';
    protected static ?string $navigationLabel = 'Promo Beranda';
    protected static ?string $modelLabel = 'Promo';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool { return Cms::allowed(); }
    public static function canCreate(): bool { return Cms::allowed(); }
    public static function canEdit($record): bool { return Cms::allowed(); }
    public static function canDelete($record): bool { return Cms::allowed(); }
    public static function canDeleteAny(): bool { return Cms::allowed(); }

    public static function form(Form $form): Form
    {
        Site::syncDefaultAssetsToPublicDisk();

        return $form->schema([
            Forms\Components\TextInput::make('title')->label('Judul Promo')->required()->maxLength(120),
            Forms\Components\TextInput::make('badge')->label('Label / Badge (cth: DISKON 20% / TERBATAS)')->maxLength(40),
            Forms\Components\Textarea::make('description')->label('Deskripsi Singkat Promo')->rows(3)->columnSpanFull(),
            Forms\Components\FileUpload::make('image')->label('Gambar Banner / Poster Promo (Opsional)')->image()->disk('public')->directory('site/promo')->imageEditor()->columnSpanFull(),
            Forms\Components\TextInput::make('link_label')->label('Teks Tombol')->placeholder('Booking Sekarang')->default('Booking Sekarang'),
            Forms\Components\TextInput::make('link_url')->label('Link Tombol')->placeholder('/booking atau https://...')->default('/booking'),
            Forms\Components\DatePicker::make('starts_at')->label('Tanggal Mulai Tayang')->helperText('Kosongkan = langsung tayang'),
            Forms\Components\DatePicker::make('ends_at')->label('Tanggal Berakhir')->helperText('Kosongkan = tampil terus tanpa batas tanggal'),
            Forms\Components\TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Tampilkan di Beranda')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        Site::ensureSeeded();

        return $table->columns([
            Tables\Columns\ImageColumn::make('image')
                ->label('Poster')
                ->getStateUsing(fn ($record) => Site::url($record->image))
                ->height(56)
                ->width(56)
                ->defaultImageUrl(asset('img/Pinpin-01.png'))
                ->circular(),
            Tables\Columns\TextColumn::make('title')->label('Judul Promo')->searchable()->weight('bold'),
            Tables\Columns\TextColumn::make('badge')->label('Badge')->badge()->color('danger'),
            Tables\Columns\TextColumn::make('ends_at')->label('Berakhir')->date('d M Y')->placeholder('Tanpa batas'),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->defaultSort('sort')->reorderable('sort')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageSitePromos::route('/')];
    }
}
