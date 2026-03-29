<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $modelLabel = 'Proyek';
    protected static ?string $pluralModelLabel = 'Riwayat Proyek';
    protected static ?string $navigationLabel = 'Proyek Saya';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian Info Project
                Forms\Components\Section::make('Informasi Proyek')
                    ->schema([
                        Forms\Components\TextInput::make('project_code')
                            ->label('ID Proyek')
                            // Generate otomatis 6 karakter acak (kapital)
                            ->default(fn () => strtoupper(Str::random(6)))
                            ->readOnly() // Membuatnya tidak bisa diedit manual
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'PHOTOBOX' => 'Photobox',
                                'SELF PHOTO' => 'Self Photo',
                                'PAS PHOTO' => 'Pas Photo'
                             ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('expired_at')
                            ->label('Batas Waktu (Expired)'),
                    ])->columns(2)->hidden(fn (string $operation): bool => $operation === 'edit'),

                // Bagian Upload Foto menggunakan Bawaan Filament
                Forms\Components\Section::make('Daftar Foto')
                    ->description('Upload foto untuk proyek ini.')
                    ->schema([
                        // Menggunakan native FileUpload Filament
                        FileUpload::make('photos')
                            ->hiddenLabel()
                            ->directory('project-photos') // Folder di storage/app/public/
                            ->multiple() // Mengizinkan upload banyak file
                            ->reorderable() // Bisa geser-geser urutan
                            ->image()
                            // SAYA HAPUS imageEditor(): Karena foto Anda berukuran sangat besar (8.5MB), 
                            // fitur editor sering menyebabkan server PHP kehabisan Memory Limit / RAM.
                            ->maxSize(102400) // Izinkan upload maksimal 100MB
                            ->panelLayout('grid') // Tampilan kotak-kotak
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Membuat tabel menjadi grid
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\Layout\View::make('filament.components.project-card'),
            ])
            // Mematikan klik bawaan baris tabel agar tombol "Link" kita bisa berfungsi
            ->recordUrl(null)
            ->recordAction(null)
            ->filters([
                //
            ])
            ->paginated(false)
            ->actions([
                // Action ini di-render di dalam custom view kita
                Tables\Actions\EditAction::make()
                    ->label('') // Hilangkan teks
                    ->icon('heroicon-o-pencil')
                    ->color('gray')
                    ->button()
                    ->outlined()
                    ->extraAttributes(['style' => 'display: none !important;']),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->button()
                    ->outlined()
                    ->extraAttributes(['style' => 'display: none !important;']),
            ])
            // KOSONGKAN Bulk Actions agar "Checkbox" di kiri hilang (Sesuai desain Figma)
            ->bulkActions([])
            ->emptyStateHeading('Belum ada proyek');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            // 'create' => Pages\CreateProject::route('/create'),
            // 'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}