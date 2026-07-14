<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\BookingConflictException;
use App\Services\Traits\SanitizesInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VehicleBookingService
{
    use SanitizesInput;
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function getFilteredBookings(
        ?string $search = null,
        ?string $status = null,
        ?string $vehicleId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $perPage = 15,
        bool $activeOnly = false,
        ?User $user = null,
        bool $ownOnly = false
    ): LengthAwarePaginator {
        $query = VehicleBooking::with(['vehicle', 'user', 'approver']);

        if ($ownOnly && $user) {
            $query->where('user_id', $user->id);
        }

        if ($activeOnly) {
            if (!$status) {
                $query->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved]);
            }
            if (!$dateFrom && !$dateTo) {
                $today = now()->format('Y-m-d');
                $driver = $query->getConnection()->getDriverName();
                if ($driver === 'sqlite') {
                    $query->whereRaw("DATE(booking_date, '+' || (duration - 1) || ' days') >= ?", [$today]);
                } elseif ($driver === 'pgsql') {
                    $query->whereRaw("booking_date + (duration - 1) * INTERVAL '1 day' >= ?", [$today]);
                } else {
                    $query->whereRaw("DATE_ADD(booking_date, INTERVAL duration - 1 DAY) >= ?", [$today]);
                }
            }
        }

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('guest_divisi', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('vehicle', fn ($v) => $v->where('name', 'like', "%{$search}%")
                      ->orWhere('plate_number', 'like', "%{$search}%"));
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        if ($dateFrom) {
            $query->where('booking_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('booking_date', '<=', $dateTo);
        }

        return $query->latest()->paginate($perPage);
    }

    public function checkConflict(int $vehicleId, string $bookingDate, int $duration, ?int $excludeBookingId = null): bool
    {
        $startDate = Carbon::parse($bookingDate);
        $endDate = $startDate->copy()->addDays($duration - 1);

        $query = VehicleBooking::where('vehicle_id', $vehicleId)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved])
            ->where('booking_date', '<=', $endDate->format('Y-m-d'));

        $driver = VehicleBooking::query()->getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            $query->whereRaw("DATE(booking_date, '+' || (duration - 1) || ' days') >= ?", [$startDate->format('Y-m-d')]);
        } elseif ($driver === 'pgsql') {
            $query->whereRaw("booking_date + (duration - 1) * INTERVAL '1 day' >= ?", [$startDate->format('Y-m-d')]);
        } else {
            $query->whereRaw("DATE_ADD(booking_date, INTERVAL duration - 1 DAY) >= ?", [$startDate->format('Y-m-d')]);
        }

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    public function getBookedDates(int $vehicleId, string $month, ?int $excludeBookingId = null): array
    {
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $query = VehicleBooking::where('vehicle_id', $vehicleId)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved])
            ->where('booking_date', '<=', $endOfMonth->format('Y-m-d'));

        $driver = VehicleBooking::query()->getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            $query->whereRaw("DATE(booking_date, '+' || (duration - 1) || ' days') >= ?", [$startOfMonth->format('Y-m-d')]);
        } elseif ($driver === 'pgsql') {
            $query->whereRaw("booking_date + (duration - 1) * INTERVAL '1 day' >= ?", [$startOfMonth->format('Y-m-d')]);
        } else {
            $query->whereRaw("DATE_ADD(booking_date, INTERVAL duration - 1 DAY) >= ?", [$startOfMonth->format('Y-m-d')]);
        }

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        $bookedRanges = $query->get(['booking_date', 'duration']);

        $bookedDates = [];
        foreach ($bookedRanges as $range) {
            $start = Carbon::parse($range->booking_date);
            $end = $start->copy()->addDays($range->duration - 1);
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                if ($date->between($startOfMonth, $endOfMonth)) {
                    $bookedDates[] = $date->format('Y-m-d');
                }
            }
        }

        return array_unique($bookedDates);
    }

    public function createBooking(array $data, ?User $user = null, ?int $excludeBookingId = null): VehicleBooking
    {
        $vehicleId = (int) $data['vehicle_id'];
        $lock = Cache::lock("vehicle-booking-create-{$vehicleId}", 10);

        try {
            $lock->block(5);

            $booking = DB::transaction(function () use ($data, $user, $excludeBookingId, $vehicleId) {
                if ($this->checkConflict($vehicleId, $data['booking_date'], $data['duration'] ?? 1, $excludeBookingId)) {
                    throw BookingConflictException::vehicle();
                }

                return VehicleBooking::create([
                    'booking_code' => VehicleBooking::generateBookingCode(),
                    'vehicle_id' => $vehicleId,
                    'user_id' => $user?->id,
                    'guest_name' => $user ? null : ($this->sanitize($data['guest_name'] ?? null)),
                    'guest_phone' => $user ? null : ($this->sanitize($data['guest_phone'] ?? null)),
                    'guest_divisi' => $user ? null : ($this->sanitize($data['guest_divisi'] ?? null)),
                    'guest_email' => $user ? null : ($this->sanitize($data['guest_email'] ?? null)),
                    'guest_ip' => $user ? null : ($data['guest_ip'] ?? null),
                    'booking_date' => $data['booking_date'],
                    'duration' => $data['duration'] ?? 1,
                    'destination' => $this->sanitize($data['destination']),
                    'notes' => $this->sanitize($data['notes'] ?? null),
                    'status' => BookingStatus::Pending,
                ]);
            });

            $this->notifyBookingCreated($booking);

            return $booking;
        } finally {
            $lock->release();
        }
    }

    public function updateBooking(VehicleBooking $booking, array $data): VehicleBooking
    {
        $vehicleId = (int) ($data['vehicle_id'] ?? $booking->vehicle_id);
        $lock = Cache::lock("vehicle-booking-update-{$vehicleId}", 10);

        try {
            $lock->block(5);

            return DB::transaction(function () use ($booking, $data, $vehicleId) {
                $bookingDate = $data['booking_date'] ?? $booking->booking_date->format('Y-m-d');
                $duration = $data['duration'] ?? $booking->duration;

                if ($this->checkConflict($vehicleId, $bookingDate, $duration, $booking->id)) {
                    throw BookingConflictException::vehicle();
                }

                $booking->update($data);
                return $booking->fresh();
            });
        } finally {
            $lock->release();
        }
    }

    public function approveBooking(VehicleBooking $booking, User $approver, ?string $driver = null, ?string $notes = null): VehicleBooking
    {
        $booking->update([
            'status' => BookingStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'driver' => $driver,
            'notes' => $notes ?? $booking->notes,
        ]);

        $booking->vehicle->update(['status' => VehicleStatus::InUse]);

        $this->notifyBookingApproved($booking);

        return $booking->fresh();
    }

    public function rejectBooking(VehicleBooking $booking, User $approver, ?string $notes = null): VehicleBooking
    {
        $booking->update([
            'status' => BookingStatus::Rejected,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'notes' => $notes ?? $booking->notes,
        ]);

        $this->notifyBookingRejected($booking);

        return $booking->fresh();
    }

    public function completeBooking(VehicleBooking $booking): VehicleBooking
    {
        $booking->update(['status' => BookingStatus::Completed]);
        $booking->vehicle->update(['status' => VehicleStatus::Available]);

        return $booking->fresh();
    }

    public function cancelBooking(VehicleBooking $booking): VehicleBooking
    {
        $wasApproved = $booking->status === BookingStatus::Approved;

        $booking->update(['status' => BookingStatus::Cancelled]);

        if ($wasApproved) {
            $booking->vehicle->update(['status' => VehicleStatus::Available]);
        }

        return $booking->fresh();
    }

    public function deleteBooking(VehicleBooking $booking): void
    {
        $booking->delete();
    }

    public function getDashboardStats(): array
    {
        $today = Carbon::today();

        return [
            'total_vehicles' => Vehicle::count(),
            'available_vehicles' => Vehicle::where('status', VehicleStatus::Available)->count(),
            'in_use_vehicles' => Vehicle::where('status', VehicleStatus::InUse)->count(),
            'maintenance_vehicles' => Vehicle::where('status', VehicleStatus::Maintenance)->count(),
            'pending_bookings' => VehicleBooking::where('status', BookingStatus::Pending)->count(),
            'approved_bookings' => VehicleBooking::where('status', BookingStatus::Approved)->count(),
            'today_bookings' => VehicleBooking::whereDate('booking_date', $today)
                ->whereIn('status', [BookingStatus::Approved, BookingStatus::Completed])
                ->count(),
            'total_bookings' => VehicleBooking::count(),
        ];
    }

    public function getBookingsByDateRange(?string $from = null, ?string $to = null): \Illuminate\Support\Collection
    {
        return VehicleBooking::with(['vehicle', 'user', 'approver'])
            ->when($from && $to, fn ($q) => $q->whereBetween('booking_date', [$from, $to]))
            ->when($from && !$to, fn ($q) => $q->where('booking_date', '>=', $from))
            ->when($to && !$from, fn ($q) => $q->where('booking_date', '<=', $to))
            ->orderBy('booking_date')
            ->get();
    }

    private function notifyBookingCreated(VehicleBooking $booking): void
    {
        $approvers = User::permission('vehicle_bookings_approve')->active()->get();

        foreach ($approvers as $approver) {
            $this->notificationService->send(
                $approver->id,
                'Booking Armada Baru',
                "Booking {$booking->booking_code} untuk {$booking->vehicle->name} ({$booking->vehicle->plate_number}) menunggu approval.",
                'vehicle_booking',
                'truck',
                route('bookings.armada.show', $booking),
                ['booking_id' => $booking->id, 'booking_code' => $booking->booking_code],
            );
        }
    }

    private function notifyBookingApproved(VehicleBooking $booking): void
    {
        $message = "Booking {$booking->booking_code} untuk {$booking->vehicle->name} telah disetujui.";

        if ($booking->user_id) {
            $this->notificationService->send(
                $booking->user_id,
                'Booking Armada Disetujui',
                $message,
                'vehicle_booking',
                'check-circle',
                route('bookings.armada.show', $booking),
                ['booking_id' => $booking->id, 'booking_code' => $booking->booking_code],
            );
        }
    }

    private function notifyBookingRejected(VehicleBooking $booking): void
    {
        if ($booking->user_id) {
            $this->notificationService->send(
                $booking->user_id,
                'Booking Armada Ditolak',
                "Booking {$booking->booking_code} untuk {$booking->vehicle->name} ditolak. {$booking->notes}",
                'vehicle_booking',
                'x-circle',
                route('bookings.armada.show', $booking),
                ['booking_id' => $booking->id, 'booking_code' => $booking->booking_code],
            );
        }
    }
}
