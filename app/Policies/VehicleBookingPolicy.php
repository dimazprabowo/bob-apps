<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleBooking;

class VehicleBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('vehicle_bookings_view') || $user->can('vehicle_bookings_view_own');
    }

    public function view(User $user, VehicleBooking $booking): bool
    {
        if ($user->can('vehicle_bookings_show')) {
            return true;
        }

        if ($user->can('vehicle_bookings_show_own')) {
            return $booking->user_id === $user->id;
        }

        return false;
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

        if ($user->can('vehicle_bookings_update_own')) {
            return $booking->user_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, VehicleBooking $booking): bool
    {
        if ($user->can('vehicle_bookings_delete')) {
            return true;
        }

        if ($user->can('vehicle_bookings_delete_own')) {
            return $booking->user_id === $user->id;
        }

        return false;
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
