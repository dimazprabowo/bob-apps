<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Room;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rooms_view');
    }

    public function create(User $user): bool
    {
        return $user->can('rooms_create');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->can('rooms_update');
    }

    public function delete(User $user, Room $room): bool
    {
        if ($room->bookings()->active()->exists()) {
            return false;
        }

        return $user->can('rooms_delete');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('rooms_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('rooms_export_pdf');
    }
}
