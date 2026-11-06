<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteFaqResource\Pages;
use App\Models\SiteFaq;
use App\Support\Cms;
use App\Support\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteFaqResource extends Resource
{
    protected static ?string $model = SiteFaq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Website CMS';
    protected static ?string $navigationLabel = 'FAQ';
    protected static ?string $modelLabel = 'FAQ';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool { return Cms::allowed(); }
    public static function canCreate(): bool { return Cms::allowed(); }
    public static function canEdit($record): bool { return Cms::allowed(); }
    public static function canDelete($record): bool { return Cms::allowed(); }
    public static function canDeleteAny(): bool { return Cms::allowed(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('question')->label('Pertanyaan')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\Textarea::make('answer')->label('Jawaban')->required()->rows(4)->columnSpanFull(),
            Forms\Components\TextInput::make('sort')->label('Urutan Tampil')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Tampilkan di Website')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        Site::ensureSeeded();

        return $table->columns([
            Tables\Columns\TextColumn::make('sort')->label('#')->sortable()->width(50),
            Tables\Columns\TextColumn::make('question')->label('Pertanyaan')->searchable()->weight('bold')->wrap(),
            Tables\Columns\TextColumn::make('answer')->label('Jawaban')->limit(75)->wrap(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->defaultSort('sort')->reorderable('sort')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageSiteFaqs::route('/')];
    }
}
