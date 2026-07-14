<?php

namespace App\Livewire\Bookings\Zoom;

use App\Enums\BookingStatus;
use App\Exceptions\BookingConflictException;
use App\Exports\ZoomBookingsExport;
use App\Livewire\Traits\HasNotification;
use App\Models\ZoomBooking;
use App\Services\ZoomBookingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ZoomBookingList extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public bool $filterChanged = false;

    public $showApproveModal = false;
    public $showRejectModal = false;
    public $selectedBookingId;
    public $approveMeetingLink;
    public $approveNotes;
    public $rejectNotes;

    public $showDeleteModal = false;
    public $deletingBookingId;
    public $deletingBookingCode;

    // Booking form fields
    public $showBookingModal = false;
    public $editMode = false;
    public $editingBookingId;
    public $topic;
    public $booking_form_date;
    public $start_time;
    public $end_time;
    public $platform = 'Zoom';
    public $notes;
    public $guest_name;
    public $guest_phone;
    public $guest_divisi;
    public $guest_email;

    public function mount()
    {
        $this->authorize('viewAny', ZoomBooking::class);

        if (Auth::check()) {
            $user = Auth::user();
            $this->guest_name = $user->name;
            $this->guest_phone = $user->phone ?? '';
            $this->guest_divisi = $user->position ?? '';
            $this->guest_email = $user->email;
        }
    }

    public function bookingRules(): array
    {
        $dateRule = $this->editMode ? 'required|date' : 'required|date|after_or_equal:today';

        return [
            'topic' => 'required|string|max:255',
            'booking_form_date' => $dateRule,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'platform' => 'required|string|in:Zoom,Google Meet,Microsoft Teams',
            'notes' => 'nullable|string|max:1000',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_divisi' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
        ];
    }

    public function getPlatformOptionsProperty(): array
    {
        return [
            ['value' => 'Zoom', 'label' => 'Zoom'],
            ['value' => 'Google Meet', 'label' => 'Google Meet'],
            ['value' => 'Microsoft Teams', 'label' => 'Microsoft Teams'],
        ];
    }

    public function openBookingModal()
    {
        $this->editMode = false;
        $this->showBookingModal = true;
    }

    public function editBooking($id)
    {
        $booking = ZoomBooking::findOrFail($id);
        $this->authorize('update', $booking);

        $this->editMode = true;
        $this->editingBookingId = $id;
        $this->topic = $booking->topic;
        $this->booking_form_date = $booking->booking_date->format('Y-m-d');
        $this->start_time = $booking->start_time instanceof \Carbon\Carbon ? $booking->start_time->format('H:i') : substr((string)$booking->start_time, 0, 5);
        $this->end_time = $booking->end_time instanceof \Carbon\Carbon ? $booking->end_time->format('H:i') : substr((string)$booking->end_time, 0, 5);
        $this->platform = $booking->platform;
        $this->notes = $booking->notes;

        if ($booking->isGuest()) {
            $this->guest_name = $booking->guest_name ?? '';
            $this->guest_phone = $booking->guest_phone ?? '';
            $this->guest_divisi = $booking->guest_divisi ?? '';
            $this->guest_email = $booking->guest_email ?? '';
        } elseif (Auth::check()) {
            $user = Auth::user();
            $this->guest_name = $user->name;
            $this->guest_phone = $user->phone ?? '';
            $this->guest_divisi = $user->position ?? '';
            $this->guest_email = $user->email;
        }

        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->editMode = false;
        $this->reset(['topic', 'booking_form_date', 'start_time', 'end_time', 'notes', 'editingBookingId']);
        $this->platform = 'Zoom';
        $this->resetValidation();

        if (Auth::check()) {
            $user = Auth::user();
            $this->guest_name = $user->name;
            $this->guest_phone = $user->phone ?? '';
            $this->guest_divisi = $user->position ?? '';
            $this->guest_email = $user->email;
        }
    }

    public function submitBooking(ZoomBookingService $service)
    {
        $this->validate($this->bookingRules());

        try {
            $user = Auth::user();

            if ($this->editMode) {
                $booking = ZoomBooking::findOrFail($this->editingBookingId);
                $this->authorize('update', $booking);

                $service->updateBooking($booking, [
                    'topic' => $this->topic,
                    'booking_date' => $this->booking_form_date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'platform' => $this->platform,
                    'notes' => $this->notes,
                ]);

                $this->notifySuccess('Booking meeting online berhasil diupdate! Kode booking: ' . $booking->booking_code);
            } else {
                $booking = $service->createBooking([
                    'topic' => $this->topic,
                    'booking_date' => $this->booking_form_date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'platform' => $this->platform,
                    'notes' => $this->notes,
                    'guest_name' => $this->guest_name,
                    'guest_phone' => $this->guest_phone,
                    'guest_divisi' => $this->guest_divisi,
                    'guest_email' => $this->guest_email,
                ], $user);

                $this->notifySuccess('Booking meeting online berhasil diajukan! Kode booking: ' . $booking->booking_code);
            }

            $this->closeBookingModal();
        } catch (BookingConflictException $e) {
            $this->notifyError($e->getMessage());
        } catch (LockTimeoutException $e) {
            $this->notifyError('Sistem sedang memproses booking lain. Silakan coba lagi.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function updatingSearch() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingStatusFilter() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingDateFrom() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingDateTo() { $this->resetPage(); $this->filterChanged = true; }

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function getStatusOptionsProperty(): array
    {
        return collect(BookingStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function confirmApprove($id)
    {
        $booking = ZoomBooking::findOrFail($id);
        $this->authorize('approve', $booking);
        $this->selectedBookingId = $id;
        $this->approveMeetingLink = $booking->meeting_link;
        $this->approveNotes = $booking->notes;
        $this->showApproveModal = true;
    }

    public function approve(ZoomBookingService $service)
    {
        try {
            $booking = ZoomBooking::findOrFail($this->selectedBookingId);
            $this->authorize('approve', $booking);
            $service->approveBooking($booking, auth()->user(), $this->approveMeetingLink, $this->approveNotes);
            $this->notifySuccess("Booking {$booking->booking_code} berhasil disetujui!");
            $this->closeApproveModal();
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function confirmReject($id)
    {
        $booking = ZoomBooking::findOrFail($id);
        $this->authorize('approve', $booking);
        $this->selectedBookingId = $id;
        $this->rejectNotes = '';
        $this->showRejectModal = true;
    }

    public function reject(ZoomBookingService $service)
    {
        try {
            $booking = ZoomBooking::findOrFail($this->selectedBookingId);
            $this->authorize('approve', $booking);
            $service->rejectBooking($booking, auth()->user(), $this->rejectNotes);
            $this->notifySuccess("Booking {$booking->booking_code} telah ditolak.");
            $this->closeRejectModal();
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function complete($id, ZoomBookingService $service)
    {
        try {
            $booking = ZoomBooking::findOrFail($id);
            $this->authorize('approve', $booking);
            $service->completeBooking($booking);
            $this->notifySuccess("Booking {$booking->booking_code} diselesaikan.");
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function confirmDelete($id)
    {
        $booking = ZoomBooking::findOrFail($id);
        $this->authorize('delete', $booking);
        $this->deletingBookingId = $id;
        $this->deletingBookingCode = $booking->booking_code;
        $this->showDeleteModal = true;
    }

    public function delete(ZoomBookingService $service)
    {
        try {
            $booking = ZoomBooking::findOrFail($this->deletingBookingId);
            $this->authorize('delete', $booking);
            $service->deleteBooking($booking);
            $this->notifySuccess("Booking {$booking->booking_code} berhasil dihapus.");
            $this->showDeleteModal = false;
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->reset(['selectedBookingId', 'approveMeetingLink', 'approveNotes']);
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->reset(['selectedBookingId', 'rejectNotes']);
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', ZoomBooking::class);
        return (new ZoomBookingsExport($this->search, $this->statusFilter, $this->dateFrom, $this->dateTo))
            ->download('booking-zoom-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(ZoomBookingService $service)
    {
        $this->authorize('exportPdf', ZoomBooking::class);
        $bookings = $service->getBookingsByDateRange(
            $this->dateFrom ?: now()->startOfMonth()->format('Y-m-d'),
            $this->dateTo ?: now()->endOfMonth()->format('Y-m-d')
        );
        $pdf = Pdf::loadView('exports.zoom-bookings-pdf', ['bookings' => $bookings]);
        $pdf->setPaper('a4', 'landscape');
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'booking-zoom-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(ZoomBookingService $service)
    {
        $user = Auth::user();
        $ownOnly = !$user->can('zoom_bookings_view') && $user->can('zoom_bookings_view_own');

        $bookings = $service->getFilteredBookings($this->search, $this->statusFilter, $this->dateFrom, $this->dateTo, 15, true, $user, $ownOnly);
        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$bookings->total()} data booking.");
            $this->filterChanged = false;
        }
        return view('livewire.bookings.zoom.booking-list', [
            'bookings' => $bookings,
            'platformOptions' => $this->platformOptions,
        ]);
    }
}
