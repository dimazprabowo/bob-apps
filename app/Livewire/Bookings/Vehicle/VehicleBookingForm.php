<?php

namespace App\Livewire\Bookings\Vehicle;

use App\Enums\VehicleStatus;
use App\Livewire\Traits\HasGuestBooking;
use App\Livewire\Traits\HasNotification;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Services\VehicleBookingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class VehicleBookingForm extends Component
{
    use HasGuestBooking, HasNotification, WithFileUploads;

    public $vehicle_id;
    public $booking_date;
    public $duration = 1;
    public $destination;
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
            'vehicle_id' => 'required|exists:vehicles,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'duration' => 'required|integer|min:1|max:30',
            'destination' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], $this->guestRules());
    }

    public function validationAttributes(): array
    {
        return array_merge([
            'vehicle_id' => 'kendaraan',
            'booking_date' => 'tanggal booking',
            'duration' => 'durasi',
            'destination' => 'tujuan',
            'notes' => 'catatan',
        ], $this->guestValidationAttributes());
    }

    public function getVehiclesProperty()
    {
        return Vehicle::where('status', VehicleStatus::Available)
            ->orderBy('name')
            ->get()
            ->map(fn ($v) => [
                'value' => $v->id,
                'label' => "{$v->name} ({$v->plate_number})",
            ]);
    }

    protected $listeners = ['date-selected' => 'setDateFromCalendar'];

    public function setDateFromCalendar(string $date): void
    {
        $this->booking_date = $date;
    }

    public function updatedVehicleId($value): void
    {
        if ($value && $this->booking_date) {
            if (app(VehicleBookingService::class)->checkConflict((int) $value, $this->booking_date, (int) $this->duration)) {
                $this->notifyError("Kendaraan ini sudah dibooking pada rentang tanggal tersebut. Silakan pilih tanggal lain.");
            }
        }
    }

    public function updatedBookingDate($value): void
    {
        $this->dispatch('booking-date-updated', date: $value);
    }

    public function updatedDuration($value): void
    {
        $this->dispatch('duration-updated', duration: (int) $value);
    }

    public function submit(VehicleBookingService $service)
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        if ($this->checkHoneypot('VB')) return;
        if ($this->checkRateLimit('vehicle-booking')) return;

        try {
            $user = Auth::user();

            if ($service->checkConflict($this->vehicle_id, $this->booking_date, $this->duration)) {
                $this->notifyError('Kendaraan ini sudah dibooking pada rentang tanggal tersebut. Silakan pilih tanggal atau kendaraan lain.');
                return;
            }

            $booking = $service->createBooking(array_merge([
                'vehicle_id' => $this->vehicle_id,
                'booking_date' => $this->booking_date,
                'duration' => $this->duration,
                'destination' => $this->destination,
                'notes' => $this->notes,
            ], $this->guestBookingData()), $user);

            $this->successBookingCode = $booking->booking_code;
            $this->showSuccess = true;
            $this->reset(['vehicle_id', 'booking_date', 'duration', 'destination', 'notes']);
            $this->resetGuestFields();
            $this->notifySuccess('Booking armada berhasil diajukan! Kode booking: ' . $booking->booking_code);
        } catch (\Throwable $e) {
            $this->logBookingError('Vehicle', $e);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function resetForm(): void
    {
        $this->showSuccess = false;
        $this->successBookingCode = null;
    }

    public function render()
    {
        return view('livewire.bookings.vehicle.booking-form', [
            'vehicles' => $this->vehicles,
        ]);
    }
}
