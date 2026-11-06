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
        Site::ensureSeeded();

        return $form->schema([
            Forms\Components\FileUpload::make('image')
                ->label('Gambar Background Slide Promo')
                ->helperText('Gambar ini akan menjadi background banner pada slider promo otomatis di Beranda. Anda bisa mengisi teks di bawah untuk ditampilkan di atas background, atau kosongkan judul/deskripsi jika gambar poster sudah memuat tulisan lengkap.')
                ->image()
                ->disk('web_public')
                ->directory('img/site/promo')
                ->imageEditor()
                ->deletable(true)
                ->openable()
                ->downloadable()
                ->maxSize(15360)
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('title')
                ->label('Judul Promo (Opsional)')
                ->placeholder('Kosongkan jika desain gambar sudah memuat judul')
                ->maxLength(120)
                ->dehydrateStateUsing(fn ($state): string => (string) ($state ?? '')),
            Forms\Components\TextInput::make('badge')
                ->label('Label / Badge (Opsional, cth: DISKON 20% / TERBATAS)')
                ->maxLength(40),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi Singkat Promo (Opsional)')
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('link_label')
                ->label('Teks Tombol CTA')
                ->placeholder('Booking Sekarang')
                ->default('Booking Sekarang'),
            Forms\Components\TextInput::make('link_url')
                ->label('Link Tujuan Saat diklik')
                ->placeholder('/booking atau https://...')
                ->default('/booking'),
            Forms\Components\DatePicker::make('starts_at')->label('Tanggal Mulai Tayang')->helperText('Kosongkan = langsung tayang'),
            Forms\Components\DatePicker::make('ends_at')->label('Tanggal Berakhir')->helperText('Kosongkan = tampil terus tanpa batas tanggal'),
            Forms\Components\TextInput::make('sort')->label('Urutan Slide')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Tampilkan di Slider Beranda')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        Site::ensureSeeded();

        return $table->columns([
            Tables\Columns\ImageColumn::make('image')
                ->label('Background Slide')
                ->getStateUsing(fn ($record) => Site::url($record->image))
                ->height(68)
                ->width(120)
                ->extraImgAttributes(['style' => 'object-fit:cover;border-radius:10px'])
                ->defaultImageUrl(asset('img/site/why-photo.jpg')),
            Tables\Columns\TextColumn::make('title')
                ->label('Judul Promo')
                ->placeholder('(Hanya Gambar Background)')
                ->searchable()
                ->weight('bold'),
            Tables\Columns\TextColumn::make('badge')->label('Badge')->badge()->color('danger'),
            Tables\Columns\TextColumn::make('ends_at')->label('Berakhir')->date('d M Y')->placeholder('Tanpa batas'),
            Tables\Columns\TextColumn::make('sort')->label('Urutan')->sortable(),
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
