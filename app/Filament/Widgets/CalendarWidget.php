<?php
namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Booking::class;

    protected string $calendarView = 'resourceTimeGridWeek';

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
                return [
                    'id'      => $booking->id,
                    'title'   => $booking->name,
                    'start'   => $booking->date . ' ' . $booking->time,
                    'end'     => $booking->date . ' ' . $booking->time,
                    'status'  => $booking->status,
                ];
            })
            ->toArray();
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
                    'booking' => Booking::find($this->record->id),
                ]);
            });
    }
}
