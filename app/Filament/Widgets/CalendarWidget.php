<?php
namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Booking::class;

    protected function headerActions(): array
    {
        return [];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Booking::where('date', '>=', $fetchInfo['start'])
            ->where('date', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Booking $booking) {
                $formattedTime = date('H:i', strtotime($booking->time));
                return [
                    'id' => $booking->id,
                    'title'   => "{$formattedTime} | {$booking->name}",
                    'start'   => $booking->date,
                    'end'     => $booking->date,
                ];
            })
            ->toArray();
    }
    public function config(): array
    {
        return [
            'eventDidMount' => \Saade\FilamentFullCalendar\JS::from(<<<'JS'
                        function(info) {
                            info.el.style.cursor = 'pointer';
                        }
                    JS),
        ];
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function viewAction(): Action
    {
        return Action::make('view')
            ->label('View Booking')
            ->button()
            ->color('primary')
            ->modalHeading('Booking Details')
            ->modalWidth('lg')
            ->modalSubmitAction(false)
            ->modalContent(function () {
                if (!$this->record) {
                    return '<p>Booking not found.</p>';
                }

                return view('filament.widgets.booking-details', [
                    'booking' => Booking::with(['voucher', 'package', 'background'])->find($this->record->id),
                ]);
            });
    }
}
