<?php

namespace App\Livewire\Bookings\Zoom;

use App\Livewire\Traits\HasNotification;
use App\Models\ZoomBooking;
use App\Services\ZoomBookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ZoomBookingDetail extends Component
{
    use AuthorizesRequests, HasNotification;

    public ZoomBooking $booking;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $approveMeetingLink;
    public $approveNotes;
    public $rejectNotes;

    public function mount(ZoomBooking $booking)
    {
        $this->authorize('view', $booking);
        $this->booking = $booking->load(['user', 'approver']);
    }

    public function confirmApprove()
    {
        $this->authorize('approve', $this->booking);
        $this->approveMeetingLink = $this->booking->meeting_link;
        $this->approveNotes = $this->booking->notes;
        $this->showApproveModal = true;
    }

    public function approve(ZoomBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->approveBooking($this->booking, auth()->user(), $this->approveMeetingLink, $this->approveNotes);
            $this->notifySuccess("Booking {$this->booking->booking_code} berhasil disetujui!");
            $this->showApproveModal = false;
            $this->booking = $this->booking->fresh(['user', 'approver']);
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

    public function reject(ZoomBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->rejectBooking($this->booking, auth()->user(), $this->rejectNotes);
            $this->notifySuccess("Booking {$this->booking->booking_code} telah ditolak.");
            $this->showRejectModal = false;
            $this->booking = $this->booking->fresh(['user', 'approver']);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->reset(['approveMeetingLink', 'approveNotes']);
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->reset(['rejectNotes']);
    }

    public function complete(ZoomBookingService $service)
    {
        try {
            $this->authorize('approve', $this->booking);
            $service->completeBooking($this->booking);
            $this->notifySuccess("Booking {$this->booking->booking_code} diselesaikan.");
            $this->booking = $this->booking->fresh(['user', 'approver']);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function goBack()
    {
        return $this->redirect(route('bookings.zoom.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.bookings.zoom.booking-detail', ['booking' => $this->booking]);
    }
}
