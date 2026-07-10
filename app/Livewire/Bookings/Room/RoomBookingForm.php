<?php

namespace App\Livewire\Bookings\Room;

use App\Enums\RoomStatus;
use App\Livewire\Traits\HasGuestBooking;
use App\Livewire\Traits\HasNotification;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Services\RoomBookingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RoomBookingForm extends Component
{
    use HasGuestBooking, HasNotification;

    public $room_id;
    public $booking_date;
    public $start_time;
    public $end_time;
    public $purpose;
    public $participants = 1;
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
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:255',
            'participants' => 'required|integer|min:1|max:200',
            'notes' => 'nullable|string|max:1000',
        ], $this->guestRules());
    }

    public function validationAttributes(): array
    {
        return array_merge([
            'room_id' => 'ruangan',
            'booking_date' => 'tanggal booking',
            'start_time' => 'jam mulai',
            'end_time' => 'jam selesai',
            'purpose' => 'tujuan',
            'participants' => 'jumlah peserta',
            'notes' => 'catatan',
        ], $this->guestValidationAttributes());
    }

    public function getRoomsProperty()
    {
        return Room::where('status', RoomStatus::Available)
            ->orderBy('name')
            ->get()
            ->map(fn ($r) => [
                'value' => $r->id,
                'label' => "{$r->name} ({$r->location}) — Kapasitas {$r->capacity}",
            ]);
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

    public function submit(RoomBookingService $service)
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        if ($this->checkHoneypot('RB')) return;
        if ($this->checkRateLimit('room-booking')) return;

        try {
            $user = Auth::user();

            if ($service->checkConflict($this->room_id, $this->booking_date, $this->start_time, $this->end_time)) {
                $this->notifyError('Ruangan sudah dibooking pada waktu tersebut. Silakan pilih waktu atau ruangan lain.');
                return;
            }

            $booking = $service->createBooking(array_merge([
                'room_id' => $this->room_id,
                'booking_date' => $this->booking_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'purpose' => $this->purpose,
                'participants' => $this->participants,
                'notes' => $this->notes,
            ], $this->guestBookingData()), $user);

            $this->successBookingCode = $booking->booking_code;
            $this->showSuccess = true;
            $this->reset(['room_id', 'booking_date', 'start_time', 'end_time', 'purpose', 'participants', 'notes']);
            $this->resetGuestFields();
            $this->participants = 1;
            $this->notifySuccess('Booking ruangan berhasil diajukan! Kode booking: ' . $booking->booking_code);
        } catch (\Throwable $e) {
            $this->logBookingError('Room', $e);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.bookings.room.booking-form', [
            'rooms' => $this->rooms,
        ]);
    }
}
