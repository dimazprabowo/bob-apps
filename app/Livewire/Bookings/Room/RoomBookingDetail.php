<?php

namespace App\Livewire\Bookings\Room;

use App\Livewire\Traits\HasNotification;
use App\Models\RoomBooking;
use App\Services\RoomBookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class RoomBookingDetail extends Component
{
    use AuthorizesRequests, HasNotification;

    public RoomBooking $booking;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $approveNotes;
    public $rejectNotes;

    public function mount(RoomBooking $booking)
    {
        $this->authorize('view', $booking);
        $this->booking = $booking->load(['room', 'user', 'approver']);
    }

    public function confirmApprove()
    {
        $this->authorize('approve', $this->booking);
        $this->approveNotes = $this->booking->notes;
        $this->showApproveModal = true;
    }

    public function approve(RoomBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->approveBooking($this->booking, auth()->user(), $this->approveNotes);
            $this->notifySuccess("Booking {$this->booking->booking_code} berhasil disetujui!");
            $this->showApproveModal = false;
            $this->booking = $this->booking->fresh(['room', 'user', 'approver']);
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

    public function reject(RoomBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->rejectBooking($this->booking, auth()->user(), $this->rejectNotes);
            $this->notifySuccess("Booking {$this->booking->booking_code} telah ditolak.");
            $this->showRejectModal = false;
            $this->booking = $this->booking->fresh(['room', 'user', 'approver']);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->reset(['approveNotes']);
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->reset(['rejectNotes']);
    }

    public function complete(RoomBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->completeBooking($this->booking);
            $this->notifySuccess("Booking {$this->booking->booking_code} diselesaikan.");
            $this->booking = $this->booking->fresh(['room', 'user', 'approver']);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function goBack()
    {
        return $this->redirect(route('bookings.ruangan.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.bookings.room.booking-detail', ['booking' => $this->booking]);
    }
}
