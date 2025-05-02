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

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(!auth()->user()->hasRole('admin')),  // Hanya admin yang bisa edit

                Forms\Components\TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->required()
                    ->maxLength(15)
                    ->disabled(!auth()->user()->hasRole('admin')),  // Hanya admin yang bisa edit

                Forms\Components\TextInput::make('people_count')
                    ->label('Jumlah Orang')
                    ->numeric()
                    ->required()
                    ->disabled(!auth()->user()->hasRole('admin')),  // Hanya admin yang bisa edit

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->disabled(!auth()->user()->hasRole('admin')),  // Hanya admin yang bisa edit

                Forms\Components\TimePicker::make('time')
                    ->label('Waktu')
                    ->required()
                    ->disabled(!auth()->user()->hasRole('admin')),  // Hanya admin yang bisa edit

                Forms\Components\Select::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'title')
                    ->searchable()
                    ->required()
                    ->disabled(!auth()->user()->hasRole('admin')),  // Hanya admin yang bisa edit

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->disabled(!auth()->user()->hasRole('admin')),  // Hanya admin yang bisa edit

                Forms\Components\Toggle::make('confirmation')
                    ->label('Confirmed')
                    ->disabled(!auth()->user()->hasRole('admin')),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'canceled' => 'Canceled',
                ])
                ->required(),
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make('name')->sortable()->searchable()->label('Name'),
                    TextColumn::make('whatsapp')->sortable()->searchable()->label('WhatsApp'),
                    TextColumn::make('people_count')->sortable()->label('Jumlah Orang'),
                    TextColumn::make('date')->sortable()->label('Tanggal')->date(),
                    TextColumn::make('time')->sortable()->label('Waktu')->Time(),
                    TextColumn::make('package.id')->sortable()->label('Package')->searchable()->getStateUsing(fn ($record) => \App\Models\Package::where('id', $record->package_id)->value('title')),
                    TextColumn::make('email')->sortable()->searchable()->label('Email'),
                    TextColumn::make('status')->sortable()->searchable()->label('Status'),
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
                    // BulkActionGroup::make([
                    //     DeleteBulkAction::make(),
                    // ]),
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
