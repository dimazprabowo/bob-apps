<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('vehicles_view');
    }

    public function create(User $user): bool
    {
        return $user->can('vehicles_create');
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->can('vehicles_update');
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        if ($vehicle->bookings()->active()->exists()) {
            return false;
        }

        return $user->can('vehicles_delete');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('vehicles_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('vehicles_export_pdf');
    }
}
