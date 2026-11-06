<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SirkelRegistrationResource\Pages;
use App\Models\SirkelRegistration;
use App\Support\Cms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SirkelRegistrationResource extends Resource
{
    protected static ?string $model = SirkelRegistration::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Website CMS';
    protected static ?string $navigationLabel = 'Pendaftar Sirkel';
    protected static ?string $modelLabel = 'Pendaftar Sirkel';
    protected static ?int $navigationSort = 5;

    private const STATUS = ['baru' => 'Baru', 'dihubungi' => 'Dihubungi', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'];

    public static function canViewAny(): bool { return Cms::allowed(); }
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return Cms::allowed(); }
    public static function canDelete($record): bool { return Cms::allowed(); }
    public static function canDeleteAny(): bool { return Cms::allowed(); }

    public static function getNavigationBadge(): ?string
    {
        try {
            $n = SirkelRegistration::where('status', 'baru')->count();
        } catch (\Throwable $e) {
            return null;
        }

        return $n ? (string) $n : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama')->disabled(),
            Forms\Components\TextInput::make('age')->label('Usia')->disabled(),
            Forms\Components\TextInput::make('city')->label('Domisili')->disabled(),
            Forms\Components\TextInput::make('whatsapp')->label('WhatsApp')->disabled(),
            Forms\Components\TextInput::make('instagram')->label('Instagram')->disabled(),
            Forms\Components\TextInput::make('tiktok')->label('TikTok')->disabled(),
            Forms\Components\Textarea::make('reason')->label('Alasan gabung')->disabled()->columnSpanFull(),
            Forms\Components\Select::make('status')->label('Status')->options(self::STATUS)->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        \App\Support\Site::ensureSeeded();

        return $table->columns([
            Tables\Columns\TextColumn::make('created_at')->label('Daftar')->dateTime('d M Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('age')->label('Usia'),
            Tables\Columns\TextColumn::make('city')->label('Domisili')->searchable(),
            Tables\Columns\TextColumn::make('whatsapp')->label('WhatsApp')->searchable()
                ->url(fn ($record) => 'https://wa.me/' . preg_replace('/\D+/', '', preg_replace('/^0/', '62', trim($record->whatsapp))), true),
            Tables\Columns\TextColumn::make('instagram')->label('IG')->toggleable(),
            Tables\Columns\TextColumn::make('tiktok')->label('TikTok')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\SelectColumn::make('status')->label('Status')->options(self::STATUS),
        ])->defaultSort('created_at', 'desc')
            ->filters([Tables\Filters\SelectFilter::make('status')->options(self::STATUS)])
            ->actions([Tables\Actions\EditAction::make()->label('Detail'), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageSirkelRegistrations::route('/')];
    }
}
