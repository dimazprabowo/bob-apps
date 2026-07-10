<?php

namespace App\Livewire\Bookings\Zoom;

use App\Livewire\Traits\HasGuestBooking;
use App\Livewire\Traits\HasNotification;
use App\Models\ZoomBooking;
use App\Services\ZoomBookingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ZoomBookingForm extends Component
{
    use HasGuestBooking, HasNotification;

    public $topic;
    public $booking_date;
    public $start_time;
    public $end_time;
    public $platform = 'Zoom';
    public $notes;

    public $showSuccess = false;
    public $successBookingCode;

    public function mount(): void
    {
        $this->mountGuestBooking();
    }

    public function rules(): array
    {
        return array_merge([
            'topic' => 'required|string|max:255',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'platform' => 'required|string|in:Zoom,Google Meet,Microsoft Teams',
            'notes' => 'nullable|string|max:1000',
        ], $this->guestRules());
    }

    public function validationAttributes(): array
    {
        return array_merge([
            'topic' => 'topik meeting',
            'booking_date' => 'tanggal booking',
            'start_time' => 'jam mulai',
            'end_time' => 'jam selesai',
            'platform' => 'platform',
            'notes' => 'catatan',
        ], $this->guestValidationAttributes());
    }

    public function getPlatformOptionsProperty(): array
    {
        return [
            ['value' => 'Zoom', 'label' => 'Zoom'],
            ['value' => 'Google Meet', 'label' => 'Google Meet'],
            ['value' => 'Microsoft Teams', 'label' => 'Microsoft Teams'],
        ];
    }

    protected $listeners = ['date-selected' => 'setDateFromCalendar'];

    public function setDateFromCalendar(string $date): void
    {
        $this->booking_date = $date;
    }

    public function updatedStartTime(): void
    {
        $this->dispatch('time-updated', startTime: $this->start_time, endTime: $this->end_time);
    }

    public function updatedEndTime(): void
    {
        $this->dispatch('time-updated', startTime: $this->start_time, endTime: $this->end_time);
    }

    public function submit(ZoomBookingService $service)
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        if ($this->checkHoneypot('ZB')) return;
        if ($this->checkRateLimit('zoom-booking')) return;

        try {
            $user = Auth::user();

            if ($service->checkConflict($this->booking_date, $this->start_time, $this->end_time)) {
                $this->notifyError('Sudah ada booking meeting pada waktu tersebut. Silakan pilih waktu lain.');
                return;
            }

            $booking = $service->createBooking(array_merge([
                'topic' => $this->topic,
                'booking_date' => $this->booking_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'platform' => $this->platform,
                'notes' => $this->notes,
            ], $this->guestBookingData()), $user);

            $this->successBookingCode = $booking->booking_code;
            $this->showSuccess = true;
            $this->reset(['topic', 'booking_date', 'start_time', 'end_time', 'platform', 'notes']);
            $this->resetGuestFields();
            $this->platform = 'Zoom';
            $this->notifySuccess('Booking meeting online berhasil diajukan! Kode booking: ' . $booking->booking_code);
        } catch (\Throwable $e) {
            $this->logBookingError('Zoom', $e);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.bookings.zoom.booking-form', [
            'platformOptions' => $this->platformOptions,
        ]);
    }
}
