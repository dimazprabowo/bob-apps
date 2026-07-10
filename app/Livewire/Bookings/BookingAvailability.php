<?php

namespace App\Livewire\Bookings;

use App\Services\RoomBookingService;
use App\Services\VehicleBookingService;
use App\Services\ZoomBookingService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class BookingAvailability extends Component
{
    public string $type = 'vehicle';
    public ?int $resourceId = null;
    public int $duration = 1;
    public string $selectedDate = '';
    public string $currentMonth = '';
    public array $bookedDates = [];
    public array $bookedSlots = [];
    public ?string $startTime = null;
    public ?string $endTime = null;

    protected $listeners = ['time-updated' => 'syncTimes', 'duration-updated' => 'syncDuration', 'booking-date-updated' => 'setDateFromInput'];

    public function syncDuration(int $duration): void
    {
        $this->duration = max(1, $duration);
    }

    public function syncTimes(?string $startTime = null, ?string $endTime = null): void
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->checkConflict();
    }

    public function mount(string $type, ?int $resourceId = null, int $duration = 1, ?string $bookingDate = null): void
    {
        $this->type = $type;
        $this->resourceId = $resourceId;
        $this->duration = max(1, $duration);

        if ($bookingDate) {
            $this->selectedDate = $bookingDate;
            $this->currentMonth = \Carbon\Carbon::parse($bookingDate)->format('Y-m');
        } else {
            $this->currentMonth = now()->format('Y-m');
        }

        $this->loadBookedDates();

        if ($this->selectedDate) {
            $this->loadBookedSlots();
        }
    }

    public function updatedResourceId(): void
    {
        $this->bookedSlots = [];
        $this->loadBookedDates();

        if ($this->selectedDate) {
            $this->loadBookedSlots();
        }
    }

    public function updatedCurrentMonth(): void
    {
        $this->loadBookedDates();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadBookedSlots();
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->loadBookedSlots();
        $this->dispatch('date-selected', date: $date);
    }

    public function setDateFromInput(string $date): void
    {
        if (!$date) {
            $this->selectedDate = '';
            $this->bookedSlots = [];
            return;
        }

        $this->selectedDate = $date;

        $targetMonth = \Carbon\Carbon::parse($date)->format('Y-m');
        if ($this->currentMonth !== $targetMonth) {
            $this->currentMonth = $targetMonth;
            $this->loadBookedDates();
        }

        $this->loadBookedSlots();
    }

    public function previousMonth(): void
    {
        $this->currentMonth = \Carbon\Carbon::parse($this->currentMonth . '-01')->subMonth()->format('Y-m');
        $this->loadBookedDates();
    }

    public function nextMonth(): void
    {
        $this->currentMonth = \Carbon\Carbon::parse($this->currentMonth . '-01')->addMonth()->format('Y-m');
        $this->loadBookedDates();
    }

    protected function loadBookedDates(): void
    {
        if ($this->type === 'vehicle' && !$this->resourceId) {
            $this->bookedDates = [];
            return;
        }

        if ($this->type === 'zoom') {
            $this->bookedDates = app(ZoomBookingService::class)->getBookedDates($this->currentMonth);
            return;
        }

        if (!$this->resourceId) {
            $this->bookedDates = [];
            return;
        }

        $this->bookedDates = match ($this->type) {
            'vehicle' => app(VehicleBookingService::class)->getBookedDates($this->resourceId, $this->currentMonth),
            'room' => app(RoomBookingService::class)->getBookedDates($this->resourceId, $this->currentMonth),
            default => [],
        };
    }

    protected function loadBookedSlots(): void
    {
        if (!$this->selectedDate) {
            $this->bookedSlots = [];
            return;
        }

        $key = 'availability-check:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 30)) {
            $this->bookedSlots = [];
            return;
        }
        RateLimiter::hit($key, 60);

        if ($this->type === 'zoom') {
            $this->bookedSlots = app(ZoomBookingService::class)->getBookedSlots($this->selectedDate);
            return;
        }

        if (!$this->resourceId) {
            $this->bookedSlots = [];
            return;
        }

        $this->bookedSlots = match ($this->type) {
            'vehicle' => [],
            'room' => app(RoomBookingService::class)->getBookedSlots($this->resourceId, $this->selectedDate),
            default => [],
        };
    }

    public function checkConflict(): void
    {
        if (!$this->selectedDate || !$this->startTime || !$this->endTime) {
            $this->dispatch('conflict-status', status: null);
            return;
        }

        if ($this->type === 'vehicle') {
            $this->dispatch('conflict-status', status: null);
            return;
        }

        $hasConflict = false;
        foreach ($this->bookedSlots as $slot) {
            if ($this->startTime < $slot['end'] && $this->endTime > $slot['start']) {
                $hasConflict = true;
                break;
            }
        }

        $this->dispatch('conflict-status', status: $hasConflict ? 'conflict' : 'available');
    }

    public function getCalendarProperty(): array
    {
        $date = \Carbon\Carbon::parse($this->currentMonth . '-01');
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $startOfWeek = $startOfMonth->copy()->startOfWeek();
        $endOfWeek = $endOfMonth->copy()->endOfWeek();

        // Calculate selected range for vehicle (duration-based)
        $selectedRange = [];
        if ($this->selectedDate && $this->type === 'vehicle' && $this->duration > 1) {
            $rangeStart = \Carbon\Carbon::parse($this->selectedDate);
            $rangeEnd = $rangeStart->copy()->addDays($this->duration - 1);
            $current = $rangeStart->copy();
            while ($current->lte($rangeEnd)) {
                $selectedRange[] = $current->format('Y-m-d');
                $current->addDay();
            }
        } elseif ($this->selectedDate) {
            $selectedRange[] = $this->selectedDate;
        }

        $weeks = [];
        $current = $startOfWeek->copy();
        $week = [];

        while ($current->lte($endOfWeek)) {
            $dateStr = $current->format('Y-m-d');
            $week[] = [
                'date' => $dateStr,
                'day' => $current->day,
                'inMonth' => $current->month === $date->month,
                'isToday' => $dateStr === now()->format('Y-m-d'),
                'isPast' => $dateStr < now()->format('Y-m-d'),
                'isBooked' => $this->type === 'vehicle' && in_array($dateStr, $this->bookedDates),
                'isPartialBooked' => in_array($this->type, ['room', 'zoom']) && in_array($dateStr, $this->bookedDates),
                'isSelected' => in_array($dateStr, $selectedRange),
                'isSelectionStart' => $dateStr === $this->selectedDate,
                'isSelectionEnd' => $this->type === 'vehicle' && $this->duration > 1 && $dateStr === \Carbon\Carbon::parse($this->selectedDate)->addDays($this->duration - 1)->format('Y-m-d'),
                'hasConflictWithSelection' => $this->type === 'vehicle' && in_array($dateStr, $selectedRange) && in_array($dateStr, $this->bookedDates),
            ];

            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }

            $current->addDay();
        }

        return [
            'weeks' => $weeks,
            'monthName' => $date->translatedFormat('F Y'),
            'hasBookedDates' => count($this->bookedDates) > 0,
            'selectedRange' => $selectedRange,
        ];
    }

    public function render()
    {
        return view('livewire.bookings.booking-availability');
    }
}
