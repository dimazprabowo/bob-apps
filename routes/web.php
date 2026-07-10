<?php

use App\Livewire\Actions\Logout;
use App\Models\RoomBooking;
use App\Models\VehicleBooking;
use App\Models\ZoomBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::view('/', 'public.landing')->name('landing');

// Logout Route (must be authenticated)
Route::post('/logout', function (Request $request, Logout $logout) {
    $logout();
    return redirect('/');
})->middleware('auth')->name('logout');

// Public Booking Routes (hybrid — no auth required for submitting bookings)
Route::prefix('booking')->name('booking.')->middleware('throttle:60,1')->group(function () {
    Route::view('/armada', 'public.booking-armada')->name('armada.form');
    Route::view('/zoom', 'public.booking-zoom')->name('zoom.form');
    Route::view('/ruangan', 'public.booking-ruangan')->name('ruangan.form');
    Route::view('/cek-status', 'public.cek-status')->name('check-status');
});

// Authenticated Routes
Route::middleware(['auth', 'verified', 'active'])->group(function () {
    
    // Dashboard
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    // Profile
    Route::view('profile', 'profile')->name('profile');

    // Master Data Routes
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::view('/companies', 'master-data.companies')->middleware('can:companies_view')->name('companies');
        Route::view('/vehicles', 'master-data.vehicles')->middleware('can:vehicles_view')->name('vehicles');
        Route::view('/rooms', 'master-data.rooms')->middleware('can:rooms_view')->name('rooms');
    });

    // Booking Management Routes (authenticated — for admin/approver)
    Route::prefix('bookings')->name('bookings.')->group(function () {
        // Vehicle Bookings
        Route::view('/armada', 'bookings.armada')->middleware('can:vehicle_bookings_view')->name('armada.index');
        Route::get('/armada/{booking}', function (VehicleBooking $booking) {
            return view('bookings.armada-detail', ['booking' => $booking]);
        })->middleware('can:vehicle_bookings_view')->name('armada.show');

        // Zoom Bookings
        Route::view('/zoom', 'bookings.zoom')->middleware('can:zoom_bookings_view')->name('zoom.index');
        Route::get('/zoom/{booking}', function (ZoomBooking $booking) {
            return view('bookings.zoom-detail', ['booking' => $booking]);
        })->middleware('can:zoom_bookings_view')->name('zoom.show');

        // Room Bookings
        Route::view('/ruangan', 'bookings.ruangan')->middleware('can:room_bookings_view')->name('ruangan.index');
        Route::get('/ruangan/{booking}', function (RoomBooking $booking) {
            return view('bookings.ruangan-detail', ['booking' => $booking]);
        })->middleware('can:room_bookings_view')->name('ruangan.show');
    });

    // Reporting Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::view('/armada', 'reports.armada')->middleware('can:reports_view')->name('armada');
        Route::get('/armada/{booking}', function (VehicleBooking $booking) {
            return view('reports.armada-detail', ['booking' => $booking]);
        })->middleware('can:reports_view')->name('armada.show');

        Route::view('/zoom', 'reports.zoom')->middleware('can:reports_view')->name('zoom');
        Route::get('/zoom/{booking}', function (ZoomBooking $booking) {
            return view('reports.zoom-detail', ['booking' => $booking]);
        })->middleware('can:reports_view')->name('zoom.show');

        Route::view('/ruangan', 'reports.ruangan')->middleware('can:reports_view')->name('ruangan');
        Route::get('/ruangan/{booking}', function (RoomBooking $booking) {
            return view('reports.ruangan-detail', ['booking' => $booking]);
        })->middleware('can:reports_view')->name('ruangan.show');
    });

    // Notifications
    Route::view('/notifications', 'notifications.index')->middleware('can:notifications_view')->name('notifications.index');
    Route::view('/notifications/send', 'notifications.send')->middleware('can:notifications_send')->name('notifications.send');

    // Chat
    Route::view('/chat', 'chat.index')->middleware('can:chat_view')->name('chat.index');

    // Settings Routes - each route checks its own permission
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::view('/system', 'settings.system')->middleware('can:configuration_view')->name('system');
        Route::view('/users', 'settings.users')->middleware('can:users_view')->name('users');
        Route::view('/roles', 'settings.roles')->middleware('can:roles_view')->name('roles');
    });
});

require __DIR__.'/auth.php';
