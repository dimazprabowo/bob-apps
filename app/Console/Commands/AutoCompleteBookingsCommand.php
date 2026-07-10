<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\VehicleBooking;
use App\Models\ZoomBooking;
use App\Models\RoomBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCompleteBookingsCommand extends Command
{
    protected $signature = 'bookings:auto-complete';
    protected $description = 'Auto-complete approved bookings whose end date has passed';

    public function handle(): int
    {
        $today = Carbon::today();
        $count = 0;

        // Vehicle bookings — end_date = booking_date + duration - 1
        $vehicleBookings = VehicleBooking::where('status', BookingStatus::Approved)->get();
        foreach ($vehicleBookings as $booking) {
            if ($booking->end_date < $today) {
                $booking->update(['status' => BookingStatus::Completed]);
                $count++;
            }
        }

        // Zoom bookings — end date/time passed
        $zoomBookings = ZoomBooking::where('status', BookingStatus::Approved)
            ->where(function ($q) use ($today) {
                $q->where('booking_date', '<', $today->format('Y-m-d'))
                  ->orWhere(function ($q2) use ($today) {
                      $q2->where('booking_date', $today->format('Y-m-d'))
                         ->where('end_time', '<', Carbon::now()->format('H:i:s'));
                  });
            })->get();

        foreach ($zoomBookings as $booking) {
            $booking->update(['status' => BookingStatus::Completed]);
            $count++;
        }

        // Room bookings — end date/time passed
        $roomBookings = RoomBooking::where('status', BookingStatus::Approved)
            ->where(function ($q) use ($today) {
                $q->where('booking_date', '<', $today->format('Y-m-d'))
                  ->orWhere(function ($q2) use ($today) {
                      $q2->where('booking_date', $today->format('Y-m-d'))
                         ->where('end_time', '<', Carbon::now()->format('H:i:s'));
                  });
            })->get();

        foreach ($roomBookings as $booking) {
            $booking->update(['status' => BookingStatus::Completed]);
            $count++;
        }

        $this->info("Auto-completed {$count} booking(s).");
        return self::SUCCESS;
    }
}
