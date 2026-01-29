<?php
    namespace App\Filament\Resources;

    use App\Filament\Resources\BookingResource\Pages;
    use App\Models\Booking;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Columns\BooleanColumn;
    use Filament\Tables\Filters\DateRangeFilter;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Actions\DeleteAction;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Filters\Filter;
    use Illuminate\Database\Eloquent\Builder;
    use Filament\Forms\Components\DatePicker;
    class BookingResource extends Resource
    {
        protected static ?string $model = Booking::class;

        protected static ?string $navigationIcon = 'heroicon-o-camera';
        protected static ?string $navigationLabel = 'List Booking';
        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->required()
                    ->maxLength(15),

                Forms\Components\TextInput::make('people_count')
                    ->label('Jumlah Orang')
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),

                Forms\Components\TimePicker::make('time')
                    ->label('Waktu')
                    ->required(),

                Forms\Components\Select::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'title')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('voucher_id')
                    ->label('Code Voucher')
                    ->relationship('voucher', 'code_voucher')
                    ->searchable(),

                Forms\Components\Select::make('background_id')
                    ->label('Nama Background')
                    ->relationship('background', 'name')
                    ->searchable(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Total Harga Booking')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->required(),
                     Forms\Components\TextInput::make('price_notes')
                    ->label('Catatan Tambahan Harga')
                    ->placeholder('Contoh: Rp 50.000')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Textarea::make('keychain_notes')
                    ->label('Catatan Keychain')
                    ->placeholder('Contoh: Tambah 2 keychain')
                    ->rows(3),
                Forms\Components\Toggle::make('confirmation')
                    ->label('Confirmed'),
            ])
            ->columns(2);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make('name')->sortable()->searchable()->label('Name'),
                    TextColumn::make('whatsapp')->sortable()->searchable()->label('WhatsApp'),
                    TextColumn::make('people_count')->sortable()->label('Jumlah Orang'),
                    TextColumn::make('voucher.code_voucher')->sortable()->searchable()->label('Kode Voucher')->getStateUsing(fn ($record) => $record->voucher?->code_voucher ?? '-'),
                    TextColumn::make('background.name')->sortable()->searchable()->label('Nama Background')->getStateUsing(fn ($record) => $record->background?->name ?? '-'),
                    TextColumn::make('date')->sortable()->label('Tanggal')->date(),
                    TextColumn::make('time')->sortable()->label('Waktu')->Time(),
                    TextColumn::make('package.title')->label('Package')->sortable()->searchable(),
                    TextColumn::make('email')->sortable()->searchable()->label('Email'),
                    TextColumn::make('status')->sortable()->searchable()->label('Status'),
                    TextColumn::make('keychain_notes')->sortable()->searchable()->label('Tambahan Keychain'),
                    TextColumn::make('price_notes')->sortable()->searchable()->label('Tambahan Harga')->getStateUsing(fn ($record) => 'Rp' . number_format($record->price_notes, 0, ',', '.')),
                    TextColumn::make('price')->sortable()->searchable()->label('Total Harga')->getStateUsing(fn ($record) => 'Rp' . number_format($record->price + $record->price_notes, 0, ',', '.')),
                    ])
                ->filters([
                            // SelectFilter::make('package_id')
                            //     ->label('Paket')
                            //     ->options(\App\Models\Package::pluck('title', 'id'))
                            //     ->searchable(),

                            // SelectFilter::make('confirmation')
                            //     ->label('Konfirmasi')
                            //     ->options([
                            //         '1' => 'Confirmed',
                            //         '0' => 'Not Confirmed',
                            //     ]),

                            // SelectFilter::make('status')
                            //     ->label('Status')
                            //     ->options([
                            //         'pending' => 'Pending',
                            //         'confirmed' => 'Confirmed',
                            //         'canceled' => 'Canceled',
                            //     ]),

                            //     Filter::make('date_range')
                            //     ->label('Rentang Tanggal')
                            //     ->form([
                            //         DatePicker::make('start_date')->label('Dari'),
                            //         DatePicker::make('end_date')->label('Sampai'),
                            //     ])
                            //     ->query(fn (Builder $query, array $data) =>
                            //         $query->when(
                            //             $data['start_date'] && $data['end_date'],
                            //             fn ($query) => $query->whereBetween('date', [$data['start_date'], $data['end_date']])
                            //         )
                            //     ),
                ])
                ->actions([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                ->bulkActions([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ]);
        }

        public static function getRelations(): array
        {
            return [
                // Define any relations if needed
            ];
        }

        public static function getPages(): array
        {
            return [
                'index' => Pages\ListBookings::route('/'),
                'edit' => Pages\EditBooking::route('/{record}/edit'),
            ];
        }
    }
