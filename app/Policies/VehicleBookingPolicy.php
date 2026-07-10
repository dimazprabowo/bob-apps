<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleBooking;

class VehicleBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('vehicle_bookings_view');
    }

    public function create(User $user): bool
    {
        return $user->can('vehicle_bookings_create');
    }

    public function update(User $user, VehicleBooking $booking): bool
    {
        if ($user->can('vehicle_bookings_update')) {
            return true;
        }

        return $booking->user_id === $user->id;
    }

    public function delete(User $user, VehicleBooking $booking): bool
    {
        if ($user->can('vehicle_bookings_delete')) {
            return true;
        }

        return $booking->user_id === $user->id;
    }

    public function approve(User $user, VehicleBooking $booking): bool
    {
        return $user->can('vehicle_bookings_approve');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('vehicle_bookings_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('vehicle_bookings_export_pdf');
    }
}
