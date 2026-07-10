<?php

namespace App\Livewire\Bookings\Vehicle;

use App\Livewire\Traits\HasNotification;
use App\Models\VehicleBooking;
use App\Services\VehicleBookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class VehicleBookingDetail extends Component
{
    use AuthorizesRequests, HasNotification;

    public VehicleBooking $booking;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $approveDriver;
    public $approveNotes;
    public $rejectNotes;

    public function mount(VehicleBooking $booking)
    {
        $this->authorize('viewAny', VehicleBooking::class);
        $this->booking = $booking->load(['vehicle', 'user', 'approver']);
    }

    public function confirmApprove()
    {
        $this->authorize('approve', $this->booking);
        $this->approveDriver = $this->booking->driver;
        $this->approveNotes = $this->booking->notes;
        $this->showApproveModal = true;
    }

    public function approve(VehicleBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->approveBooking($this->booking, auth()->user(), $this->approveDriver, $this->approveNotes);
            $this->notifySuccess("Booking {$this->booking->booking_code} berhasil disetujui!");
            $this->showApproveModal = false;
            $this->booking = $this->booking->fresh(['vehicle', 'user', 'approver']);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function confirmReject()
    {
        $this->authorize('approve', $this->booking);
        $this->rejectNotes = '';
        $this->showRejectModal = true;
    }

    public function reject(VehicleBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->rejectBooking($this->booking, auth()->user(), $this->rejectNotes);
            $this->notifySuccess("Booking {$this->booking->booking_code} telah ditolak.");
            $this->showRejectModal = false;
            $this->booking = $this->booking->fresh(['vehicle', 'user', 'approver']);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->reset(['approveDriver', 'approveNotes']);
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->reset(['rejectNotes']);
    }

    public function complete(VehicleBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->completeBooking($this->booking);
            $this->notifySuccess("Booking {$this->booking->booking_code} diselesaikan.");
            $this->booking = $this->booking->fresh(['vehicle', 'user', 'approver']);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function goBack()
    {
        return $this->redirect(route('bookings.armada.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.bookings.vehicle.booking-detail', [
            'booking' => $this->booking,
        ]);
    }
}
