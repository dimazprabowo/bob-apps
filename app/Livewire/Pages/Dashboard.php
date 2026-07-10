<?php

namespace App\Livewire\Pages;

use App\Models\Company;
use App\Models\User;
use App\Services\VehicleBookingService;
use App\Services\ZoomBookingService;
use App\Services\RoomBookingService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Dashboard extends Component
{
    public function render(
        VehicleBookingService $vehicleService,
        ZoomBookingService $zoomService,
        RoomBookingService $roomService,
    ) {
        $user = auth()->user();
        $canViewStats = Gate::allows('viewStats');

        $data = [
            'authUser' => $user,
            'authUserRole' => $user->getRoleNames()->join(', ') ?: 'User',
            'canViewStats' => $canViewStats,
            'appJoinedAt' => $user->created_at,
        ];

        if ($canViewStats) {
            $data['totalUsers']     = User::count();
            $data['totalCompanies'] = Company::count();
            $data['totalRoles']     = Role::count();

            $vehicleStats = $vehicleService->getDashboardStats();
            $zoomStats = $zoomService->getDashboardStats();
            $roomStats = $roomService->getDashboardStats();

            $data['vehicleStats'] = $vehicleStats;
            $data['zoomStats'] = $zoomStats;
            $data['roomStats'] = $roomStats;
            $data['totalBookings'] = $vehicleStats['total_bookings'] + $zoomStats['total_bookings'] + $roomStats['total_bookings'];
            $data['pendingApprovals'] = $vehicleStats['pending_bookings'] + $zoomStats['pending_bookings'] + $roomStats['pending_bookings'];
        }

        return view('livewire.pages.dashboard', $data);
    }
}
