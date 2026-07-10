<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\Traits\SanitizesInput;

class RoomBookingService
{
    use SanitizesInput;
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function getFilteredBookings(
        ?string $search = null,
        ?string $status = null,
        ?string $roomId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $perPage = 15,
        bool $activeOnly = false
    ): LengthAwarePaginator {
        $query = RoomBooking::with(['room', 'user', 'approver']);

        if ($activeOnly) {
            if (!$status) {
                $query->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved]);
            }
            if (!$dateFrom && !$dateTo) {
                $query->where('booking_date', '>=', now()->format('Y-m-d'));
            }
        }

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_divisi', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        if ($dateFrom) {
            $query->where('booking_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('booking_date', '<=', $dateTo);
        }

        return $query->latest()->paginate($perPage);
    }

    public function checkConflict(int $roomId, string $bookingDate, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $query = RoomBooking::where('room_id', $roomId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved])
            ->where(function (Builder $q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    public function getBookedSlots(int $roomId, string $date): array
    {
        $slots = RoomBooking::where('room_id', $roomId)
            ->where('booking_date', $date)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved])
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        return $slots->map(fn ($s) => [
            'start' => $s->start_time instanceof \Carbon\Carbon ? $s->start_time->format('H:i') : substr((string)$s->start_time, 0, 5),
            'end' => $s->end_time instanceof \Carbon\Carbon ? $s->end_time->format('H:i') : substr((string)$s->end_time, 0, 5),
        ])->toArray();
    }

    public function getBookedDates(int $roomId, string $month): array
    {
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $dates = RoomBooking::where('room_id', $roomId)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved])
            ->whereBetween('booking_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->distinct()
            ->pluck('booking_date');

        return $dates->map(fn ($d) => $d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : substr((string)$d, 0, 10))->unique()->toArray();
    }

    public function createBooking(array $data, ?User $user = null): RoomBooking
    {
        $booking = RoomBooking::create([
            'booking_code' => RoomBooking::generateBookingCode(),
            'room_id' => $data['room_id'],
            'user_id' => $user?->id,
            'guest_name' => $user ? null : ($this->sanitize($data['guest_name'] ?? null)),
            'guest_phone' => $user ? null : ($this->sanitize($data['guest_phone'] ?? null)),
            'guest_divisi' => $user ? null : ($this->sanitize($data['guest_divisi'] ?? null)),
            'guest_email' => $user ? null : ($this->sanitize($data['guest_email'] ?? null)),
            'guest_ip' => $user ? null : ($data['guest_ip'] ?? null),
            'booking_date' => $data['booking_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'purpose' => $this->sanitize($data['purpose']),
            'participants' => $data['participants'] ?? 1,
            'notes' => $this->sanitize($data['notes'] ?? null),
            'status' => BookingStatus::Pending,
        ]);

        $this->notifyBookingCreated($booking);

        return $booking;
    }

    public function updateBooking(RoomBooking $booking, array $data): RoomBooking
    {
        $booking->update($data);
        return $booking->fresh();
    }

    public function approveBooking(RoomBooking $booking, User $approver, ?string $notes = null): RoomBooking
    {
        $booking->update([
            'status' => BookingStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'notes' => $notes ?? $booking->notes,
        ]);

        $this->notifyBookingApproved($booking);

        return $booking->fresh();
    }

    public function rejectBooking(RoomBooking $booking, User $approver, ?string $notes = null): RoomBooking
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

    public function completeBooking(RoomBooking $booking): RoomBooking
    {
        $booking->update(['status' => BookingStatus::Completed]);
        return $booking->fresh();
    }

    public function cancelBooking(RoomBooking $booking): RoomBooking
    {
        $booking->update(['status' => BookingStatus::Cancelled]);
        return $booking->fresh();
    }

    public function deleteBooking(RoomBooking $booking): void
    {
        $booking->delete();
    }

    public function getDashboardStats(): array
    {
        $today = Carbon::today();

        return [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', RoomStatus::Available)->count(),
            'pending_bookings' => RoomBooking::where('status', BookingStatus::Pending)->count(),
            'approved_bookings' => RoomBooking::where('status', BookingStatus::Approved)->count(),
            'today_bookings' => RoomBooking::whereDate('booking_date', $today)
                ->whereIn('status', [BookingStatus::Approved, BookingStatus::Completed])
                ->count(),
            'total_bookings' => RoomBooking::count(),
        ];
    }

    public function getBookingsByDateRange(?string $from = null, ?string $to = null): \Illuminate\Support\Collection
    {
        return RoomBooking::with(['room', 'user', 'approver'])
            ->when($from && $to, fn ($q) => $q->whereBetween('booking_date', [$from, $to]))
            ->when($from && !$to, fn ($q) => $q->where('booking_date', '>=', $from))
            ->when($to && !$from, fn ($q) => $q->where('booking_date', '<=', $to))
            ->orderBy('booking_date')
            ->get();
    }

    private function notifyBookingCreated(RoomBooking $booking): void
    {
        $approvers = User::permission('room_bookings_approve')->active()->get();

        foreach ($approvers as $approver) {
            $this->notificationService->send(
                $approver->id,
                'Booking Ruangan Baru',
                "Booking {$booking->booking_code} untuk {$booking->room->name} menunggu approval.",
                'room_booking',
                'building',
                route('bookings.ruangan.show', $booking),
                ['booking_id' => $booking->id, 'booking_code' => $booking->booking_code],
            );
        }
    }

    private function notifyBookingApproved(RoomBooking $booking): void
    {
        if ($booking->user_id) {
            $this->notificationService->send(
                $booking->user_id,
                'Booking Ruangan Disetujui',
                "Booking {$booking->booking_code} untuk {$booking->room->name} telah disetujui.",
                'room_booking',
                'check-circle',
                route('bookings.ruangan.show', $booking),
                ['booking_id' => $booking->id, 'booking_code' => $booking->booking_code],
            );
        }
    }

    private function notifyBookingRejected(RoomBooking $booking): void
    {
        if ($booking->user_id) {
            $this->notificationService->send(
                $booking->user_id,
                'Booking Ruangan Ditolak',
                "Booking {$booking->booking_code} untuk {$booking->room->name} ditolak. {$booking->notes}",
                'room_booking',
                'x-circle',
                route('bookings.ruangan.show', $booking),
                ['booking_id' => $booking->id, 'booking_code' => $booking->booking_code],
            );
        }
    }
}
