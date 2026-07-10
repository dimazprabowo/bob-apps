<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\VehicleBooking;
use App\Models\ZoomBooking;
use App\Models\RoomBooking;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingRemindersCommand extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminders for upcoming approved bookings';

    public function handle(): int
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $count = 0;

        // Vehicle bookings
        $vehicleBookings = VehicleBooking::with(['vehicle', 'user'])
            ->where('status', BookingStatus::Approved)
            ->where('booking_date', $tomorrow)
            ->get();

        foreach ($vehicleBookings as $booking) {
            if ($booking->user_id) {
                NotificationService::send(
                    $booking->user_id,
                    'Pengingat Booking Armada',
                    "Booking {$booking->booking_code} untuk {$booking->vehicle?->name} besok ({$booking->booking_date->format('d M Y')}).",
                    'info',
                    null,
                    route('bookings.armada.show', $booking),
                );
            }
            $count++;
        }

        // Zoom bookings
        $zoomBookings = ZoomBooking::with(['user'])
            ->where('status', BookingStatus::Approved)
            ->where('booking_date', $tomorrow)
            ->get();

        foreach ($zoomBookings as $booking) {
            if ($booking->user_id) {
                NotificationService::send(
                    $booking->user_id,
                    'Pengingat Meeting Online',
                    "Meeting {$booking->topic} ({$booking->booking_code}) besok pada {$booking->start_time->format('H:i')}.",
                    'info',
                    null,
                    route('bookings.zoom.show', $booking),
                );
            }
            $count++;
        }

        // Room bookings
        $roomBookings = RoomBooking::with(['room', 'user'])
            ->where('status', BookingStatus::Approved)
            ->where('booking_date', $tomorrow)
            ->get();

        foreach ($roomBookings as $booking) {
            if ($booking->user_id) {
                NotificationService::send(
                    $booking->user_id,
                    'Pengingat Booking Ruangan',
                    "Booking {$booking->booking_code} untuk {$booking->room?->name} besok ({$booking->booking_date->format('d M Y')} {$booking->time_range}).",
                    'info',
                    null,
                    route('bookings.ruangan.show', $booking),
                );
            }
            $count++;
        }

        $this->info("Sent {$count} booking reminder(s).");
        return self::SUCCESS;
    }
}
