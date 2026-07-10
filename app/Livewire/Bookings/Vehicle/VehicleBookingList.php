<?php

namespace App\Livewire\Bookings\Vehicle;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Exports\VehicleBookingsExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Services\VehicleBookingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleBookingList extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $statusFilter = '';
    public $vehicleFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public bool $filterChanged = false;

    // Approve/Reject modal
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $selectedBookingId;
    public $approveDriver;
    public $approveNotes;
    public $rejectNotes;

    // Delete modal
    public $showDeleteModal = false;
    public $deletingBookingId;
    public $deletingBookingCode;

    // Booking form fields
    public $showBookingModal = false;
    public $editMode = false;
    public $editingBookingId;
    public $vehicle_id;
    public $booking_form_date;
    public $duration = 1;
    public $destination;
    public $notes;
    public $guest_name;
    public $guest_phone;
    public $guest_divisi;
    public $guest_email;

    public function mount()
    {
        $this->authorize('viewAny', VehicleBooking::class);

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
            'vehicle_id' => 'required|exists:vehicles,id',
            'booking_form_date' => $dateRule,
            'duration' => 'required|integer|min:1|max:30',
            'destination' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_divisi' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
        ];
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

    public function openBookingModal()
    {
        $this->editMode = false;
        $this->showBookingModal = true;
    }

    public function editBooking($id)
    {
        $booking = VehicleBooking::findOrFail($id);
        $this->authorize('update', $booking);

        $this->editMode = true;
        $this->editingBookingId = $id;
        $this->vehicle_id = $booking->vehicle_id;
        $this->booking_form_date = $booking->booking_date->format('Y-m-d');
        $this->duration = $booking->duration;
        $this->destination = $booking->destination;
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
        $this->reset(['vehicle_id', 'booking_form_date', 'destination', 'notes', 'editingBookingId']);
        $this->duration = 1;
        $this->resetValidation();

        if (Auth::check()) {
            $user = Auth::user();
            $this->guest_name = $user->name;
            $this->guest_phone = $user->phone ?? '';
            $this->guest_divisi = $user->position ?? '';
            $this->guest_email = $user->email;
        }
    }

    public function submitBooking(VehicleBookingService $service)
    {
        $this->validate($this->bookingRules());

        try {
            $user = Auth::user();

            if ($this->editMode) {
                $booking = VehicleBooking::findOrFail($this->editingBookingId);
                $this->authorize('update', $booking);

                if ($service->checkConflict($this->vehicle_id, $this->booking_form_date, $this->duration, $booking->id)) {
                    $this->notifyError('Kendaraan ini sudah dibooking pada rentang tanggal tersebut. Silakan pilih tanggal atau kendaraan lain.');
                    return;
                }

                $service->updateBooking($booking, [
                    'vehicle_id' => $this->vehicle_id,
                    'booking_date' => $this->booking_form_date,
                    'duration' => $this->duration,
                    'destination' => $this->destination,
                    'notes' => $this->notes,
                ]);

                $this->notifySuccess('Booking armada berhasil diupdate! Kode booking: ' . $booking->booking_code);
            } else {
                if ($service->checkConflict($this->vehicle_id, $this->booking_form_date, $this->duration)) {
                    $this->notifyError('Kendaraan ini sudah dibooking pada rentang tanggal tersebut. Silakan pilih tanggal atau kendaraan lain.');
                    return;
                }

                $booking = $service->createBooking([
                    'vehicle_id' => $this->vehicle_id,
                    'booking_date' => $this->booking_form_date,
                    'duration' => $this->duration,
                    'destination' => $this->destination,
                    'notes' => $this->notes,
                    'guest_name' => $this->guest_name,
                    'guest_phone' => $this->guest_phone,
                    'guest_divisi' => $this->guest_divisi,
                    'guest_email' => $this->guest_email,
                ], $user);

                $this->notifySuccess('Booking armada berhasil diajukan! Kode booking: ' . $booking->booking_code);
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

    public function updatingSearch()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingVehicleFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingDateTo()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->vehicleFilter = '';
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

    public function getVehicleOptionsProperty(): array
    {
        return Vehicle::orderBy('name')->get()->map(fn ($v) => [
            'value' => $v->id,
            'label' => $v->name,
        ])->toArray();
    }

    public function confirmApprove($id)
    {
        $booking = VehicleBooking::findOrFail($id);
        $this->authorize('approve', $booking);

        $this->selectedBookingId = $id;
        $this->approveDriver = $booking->driver;
        $this->approveNotes = $booking->notes;
        $this->showApproveModal = true;
    }

    public function approve(VehicleBookingService $service)
    {
        try {
            $booking = VehicleBooking::findOrFail($this->selectedBookingId);
            $this->authorize('approve', $booking);

            $service->approveBooking($booking, auth()->user(), $this->approveDriver, $this->approveNotes);
            $this->notifySuccess("Booking {$booking->booking_code} berhasil disetujui!");
            $this->closeApproveModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk menyetujui booking ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmReject($id)
    {
        $booking = VehicleBooking::findOrFail($id);
        $this->authorize('approve', $booking);

        $this->selectedBookingId = $id;
        $this->rejectNotes = '';
        $this->showRejectModal = true;
    }

    public function reject(VehicleBookingService $service)
    {
        try {
            $booking = VehicleBooking::findOrFail($this->selectedBookingId);
            $this->authorize('approve', $booking);

            $service->rejectBooking($booking, auth()->user(), $this->rejectNotes);
            $this->notifySuccess("Booking {$booking->booking_code} telah ditolak.");
            $this->closeRejectModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk menolak booking ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function complete($id, VehicleBookingService $service)
    {
        try {
            $booking = VehicleBooking::findOrFail($id);
            $this->authorize('approve', $booking);

            $service->completeBooking($booking);
            $this->notifySuccess("Booking {$booking->booking_code} diselesaikan.");
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function confirmDelete($id)
    {
        $booking = VehicleBooking::findOrFail($id);
        $this->authorize('delete', $booking);

        $this->deletingBookingId = $id;
        $this->deletingBookingCode = $booking->booking_code;
        $this->showDeleteModal = true;
    }

    public function delete(VehicleBookingService $service)
    {
        try {
            $booking = VehicleBooking::findOrFail($this->deletingBookingId);
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
        $this->reset(['selectedBookingId', 'approveDriver', 'approveNotes']);
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->reset(['selectedBookingId', 'rejectNotes']);
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', VehicleBooking::class);

        return (new VehicleBookingsExport($this->search, $this->statusFilter, $this->dateFrom, $this->dateTo))
            ->download('booking-armada-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(VehicleBookingService $service)
    {
        $this->authorize('exportPdf', VehicleBooking::class);

        $bookings = $service->getBookingsByDateRange(
            $this->dateFrom ?: now()->startOfMonth()->format('Y-m-d'),
            $this->dateTo ?: now()->endOfMonth()->format('Y-m-d')
        );

        $pdf = Pdf::loadView('exports.vehicle-bookings-pdf', ['bookings' => $bookings]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'booking-armada-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(VehicleBookingService $service)
    {
        $bookings = $service->getFilteredBookings(
            $this->search,
            $this->statusFilter,
            $this->vehicleFilter,
            $this->dateFrom,
            $this->dateTo,
            15,
            true
        );

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$bookings->total()} data booking.");
            $this->filterChanged = false;
        }

        return view('livewire.bookings.vehicle.booking-list', [
            'bookings' => $bookings,
            'vehicles' => $this->vehicles,
        ]);
    }
}
