<?php

namespace App\Livewire\Bookings;

use App\Models\VehicleBooking;
use App\Models\ZoomBooking;
use App\Models\RoomBooking;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class CheckStatus extends Component
{
    public $query = '';
    public $searched = false;

    public function search()
    {
        $this->validate(['query' => 'required|string|max:100']);

        // Rate limit: 5 searches per minute per IP
        $key = 'check-status:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('query', 'Terlalu banyak pencarian. Silakan coba lagi dalam beberapa menit.');
            return;
        }
        RateLimiter::hit($key, 60);

        $this->searched = true;
    }

    public function render()
    {
        $vehicleBookings = collect();
        $zoomBookings = collect();
        $roomBookings = collect();

        if ($this->searched && $this->query) {
            $search = trim($this->query);

            $vehicleBookings = VehicleBooking::with(['vehicle'])
                ->where(function ($q) use ($search) {
                    $q->where('booking_code', 'like', "%{$search}%")
                      ->orWhere('guest_phone', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('phone', 'like', "%{$search}%"));
                })
                ->latest()->limit(10)->get();

            $zoomBookings = ZoomBooking::where(function ($q) use ($search) {
                    $q->where('booking_code', 'like', "%{$search}%")
                      ->orWhere('guest_phone', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhere('topic', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('phone', 'like', "%{$search}%"));
                })
                ->latest()->limit(10)->get();

            $roomBookings = RoomBooking::with(['room'])
                ->where(function ($q) use ($search) {
                    $q->where('booking_code', 'like', "%{$search}%")
                      ->orWhere('guest_phone', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('phone', 'like', "%{$search}%"));
                })
                ->latest()->limit(10)->get();
        }

        return view('livewire.bookings.check-status', [
            'vehicleBookings' => $vehicleBookings,
            'zoomBookings' => $zoomBookings,
            'roomBookings' => $roomBookings,
        ]);
    }
}
