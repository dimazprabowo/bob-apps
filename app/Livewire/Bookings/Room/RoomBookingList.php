<?php

namespace App\Livewire\Bookings\Room;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Exports\RoomBookingsExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Services\RoomBookingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RoomBookingList extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $statusFilter = '';
    public $roomFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public bool $filterChanged = false;

    public $showApproveModal = false;
    public $showRejectModal = false;
    public $selectedBookingId;
    public $approveNotes;
    public $rejectNotes;

    public $showDeleteModal = false;
    public $deletingBookingId;
    public $deletingBookingCode;

    // Booking form fields
    public $showBookingModal = false;
    public $editMode = false;
    public $editingBookingId;
    public $room_id;
    public $booking_form_date;
    public $start_time;
    public $end_time;
    public $purpose;
    public $participants = 1;
    public $notes;
    public $guest_name;
    public $guest_phone;
    public $guest_divisi;
    public $guest_email;

    public function mount()
    {
        $this->authorize('viewAny', RoomBooking::class);

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
            'room_id' => 'required|exists:rooms,id',
            'booking_form_date' => $dateRule,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:255',
            'participants' => 'required|integer|min:1|max:200',
            'notes' => 'nullable|string|max:1000',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_divisi' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
        ];
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

    public function openBookingModal()
    {
        $this->editMode = false;
        $this->showBookingModal = true;
    }

    public function editBooking($id)
    {
        $booking = RoomBooking::findOrFail($id);
        $this->authorize('update', $booking);

        $this->editMode = true;
        $this->editingBookingId = $id;
        $this->room_id = $booking->room_id;
        $this->booking_form_date = $booking->booking_date->format('Y-m-d');
        $this->start_time = $booking->start_time instanceof \Carbon\Carbon ? $booking->start_time->format('H:i') : substr((string)$booking->start_time, 0, 5);
        $this->end_time = $booking->end_time instanceof \Carbon\Carbon ? $booking->end_time->format('H:i') : substr((string)$booking->end_time, 0, 5);
        $this->purpose = $booking->purpose;
        $this->participants = $booking->participants;
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
        $this->reset(['room_id', 'booking_form_date', 'start_time', 'end_time', 'purpose', 'notes', 'editingBookingId']);
        $this->participants = 1;
        $this->resetValidation();

        if (Auth::check()) {
            $user = Auth::user();
            $this->guest_name = $user->name;
            $this->guest_phone = $user->phone ?? '';
            $this->guest_divisi = $user->position ?? '';
            $this->guest_email = $user->email;
        }
    }

    public function submitBooking(RoomBookingService $service)
    {
        $this->validate($this->bookingRules());

        try {
            $user = Auth::user();

            if ($this->editMode) {
                $booking = RoomBooking::findOrFail($this->editingBookingId);
                $this->authorize('update', $booking);

                if ($service->checkConflict($this->room_id, $this->booking_form_date, $this->start_time, $this->end_time, $booking->id)) {
                    $this->notifyError('Ruangan sudah dibooking pada waktu tersebut. Silakan pilih waktu atau ruangan lain.');
                    return;
                }

                $service->updateBooking($booking, [
                    'room_id' => $this->room_id,
                    'booking_date' => $this->booking_form_date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'purpose' => $this->purpose,
                    'participants' => $this->participants,
                    'notes' => $this->notes,
                ]);

                $this->notifySuccess('Booking ruangan berhasil diupdate! Kode booking: ' . $booking->booking_code);
            } else {
                if ($service->checkConflict($this->room_id, $this->booking_form_date, $this->start_time, $this->end_time)) {
                    $this->notifyError('Ruangan sudah dibooking pada waktu tersebut. Silakan pilih waktu atau ruangan lain.');
                    return;
                }

                $booking = $service->createBooking([
                    'room_id' => $this->room_id,
                    'booking_date' => $this->booking_form_date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'purpose' => $this->purpose,
                    'participants' => $this->participants,
                    'notes' => $this->notes,
                    'guest_name' => $this->guest_name,
                    'guest_phone' => $this->guest_phone,
                    'guest_divisi' => $this->guest_divisi,
                    'guest_email' => $this->guest_email,
                ], $user);

                $this->notifySuccess('Booking ruangan berhasil diajukan! Kode booking: ' . $booking->booking_code);
            }

            $this->closeBookingModal();
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
    public function updatingRoomFilter() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingDateFrom() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingDateTo() { $this->resetPage(); $this->filterChanged = true; }

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->roomFilter = '';
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

    public function getRoomOptionsProperty(): array
    {
        return Room::orderBy('name')->get()->map(fn ($r) => [
            'value' => $r->id,
            'label' => $r->name,
        ])->toArray();
    }

    public function confirmApprove($id)
    {
        $booking = RoomBooking::findOrFail($id);
        $this->authorize('approve', $booking);
        $this->selectedBookingId = $id;
        $this->approveNotes = $booking->notes;
        $this->showApproveModal = true;
    }

    public function approve(RoomBookingService $service)
    {
        try {
            $booking = RoomBooking::findOrFail($this->selectedBookingId);
            $this->authorize('approve', $booking);
            $service->approveBooking($booking, auth()->user(), $this->approveNotes);
            $this->notifySuccess("Booking {$booking->booking_code} berhasil disetujui!");
            $this->closeApproveModal();
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function confirmReject($id)
    {
        $booking = RoomBooking::findOrFail($id);
        $this->authorize('approve', $booking);
        $this->selectedBookingId = $id;
        $this->rejectNotes = '';
        $this->showRejectModal = true;
    }

    public function reject(RoomBookingService $service)
    {
        try {
            $booking = RoomBooking::findOrFail($this->selectedBookingId);
            $this->authorize('approve', $booking);
            $service->rejectBooking($booking, auth()->user(), $this->rejectNotes);
            $this->notifySuccess("Booking {$booking->booking_code} telah ditolak.");
            $this->closeRejectModal();
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function complete($id, RoomBookingService $service)
    {
        try {
            $booking = RoomBooking::findOrFail($id);
            $this->authorize('approve', $booking);
            $service->completeBooking($booking);
            $this->notifySuccess("Booking {$booking->booking_code} diselesaikan.");
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function confirmDelete($id)
    {
        $booking = RoomBooking::findOrFail($id);
        $this->authorize('delete', $booking);
        $this->deletingBookingId = $id;
        $this->deletingBookingCode = $booking->booking_code;
        $this->showDeleteModal = true;
    }

    public function delete(RoomBookingService $service)
    {
        try {
            $booking = RoomBooking::findOrFail($this->deletingBookingId);
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
        $this->reset(['selectedBookingId', 'approveNotes']);
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->reset(['selectedBookingId', 'rejectNotes']);
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', RoomBooking::class);
        return (new RoomBookingsExport($this->search, $this->statusFilter, $this->dateFrom, $this->dateTo))
            ->download('booking-ruangan-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(RoomBookingService $service)
    {
        $this->authorize('exportPdf', RoomBooking::class);
        $bookings = $service->getBookingsByDateRange(
            $this->dateFrom ?: now()->startOfMonth()->format('Y-m-d'),
            $this->dateTo ?: now()->endOfMonth()->format('Y-m-d')
        );
        $pdf = Pdf::loadView('exports.room-bookings-pdf', ['bookings' => $bookings]);
        $pdf->setPaper('a4', 'landscape');
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'booking-ruangan-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(RoomBookingService $service)
    {
        $bookings = $service->getFilteredBookings($this->search, $this->statusFilter, $this->roomFilter, $this->dateFrom, $this->dateTo, 15, true);
        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$bookings->total()} data booking.");
            $this->filterChanged = false;
        }
        return view('livewire.bookings.room.booking-list', [
            'bookings' => $bookings,
            'rooms' => $this->rooms,
        ]);
    }
}
