<?php

namespace App\Livewire\Traits;

use App\Models\Chat;
use App\Models\Company;
use App\Models\Notification;
use App\Models\Room;
use App\Models\SystemConfiguration;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

trait HasMenuItems
{
    public function getMenuItems(): array
    {
        $user = Auth::user();

        // Cache per request — Sidebar and Navigation both call this, avoid double DB hit
        static $cache = [];
        $cacheKey = 'menu_' . $user->id;
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        // All checks go through Gate → policies (respects Gate::before super-admin bypass)
        $perms = [
            'dashboard_view'     => Gate::allows('viewStats'),
            'companies_view'     => Gate::allows('viewAny', Company::class),
            'vehicles_view'      => Gate::allows('viewAny', Vehicle::class),
            'rooms_view'         => Gate::allows('viewAny', Room::class),
            'vehicle_bookings_view' => $user->can('vehicle_bookings_view') || $user->can('vehicle_bookings_view_own'),
            'zoom_bookings_view'    => $user->can('zoom_bookings_view') || $user->can('zoom_bookings_view_own'),
            'room_bookings_view'    => $user->can('room_bookings_view') || $user->can('room_bookings_view_own'),
            'reports_view'          => $user->can('reports_view'),
            'notifications_view' => Gate::allows('viewAny', Notification::class),
            'notifications_send' => Gate::allows('send', Notification::class),
            'chat_view'          => Gate::allows('viewAny', Chat::class),
            'configuration_view' => Gate::allows('viewAny', SystemConfiguration::class),
            'users_view'         => Gate::allows('viewAny', User::class),
            'roles_view'         => Gate::allows('viewAny', Role::class),
        ];

        $req   = request();
        $items = [];

        // Dashboard — all roles
        $items[] = [
            'name'   => 'Dashboard',
            'route'  => 'dashboard',
            'icon'   => 'home',
            'active' => $req->routeIs('dashboard'),
        ];

        // Master Data
        $masterDataChildren = [];
        if ($perms['companies_view']) {
            $masterDataChildren[] = [
                'name'   => 'Perusahaan',
                'route'  => 'master-data.companies',
                'active' => $req->routeIs('master-data.companies'),
            ];
        }
        if ($perms['vehicles_view']) {
            $masterDataChildren[] = [
                'name'   => 'Armada',
                'route'  => 'master-data.vehicles',
                'active' => $req->routeIs('master-data.vehicles'),
            ];
        }
        if ($perms['rooms_view']) {
            $masterDataChildren[] = [
                'name'   => 'Ruangan',
                'route'  => 'master-data.rooms',
                'active' => $req->routeIs('master-data.rooms'),
            ];
        }
        if (!empty($masterDataChildren)) {
            $items[] = [
                'name'     => 'Master Data',
                'icon'     => 'database',
                'active'   => $req->routeIs('master-data.*'),
                'children' => $masterDataChildren,
            ];
        }

        // Booking Management
        $bookingChildren = [];
        if ($perms['vehicle_bookings_view']) {
            $bookingChildren[] = [
                'name'   => 'Booking Armada',
                'route'  => 'bookings.armada.index',
                'active' => $req->routeIs('bookings.armada.*'),
            ];
        }
        if ($perms['zoom_bookings_view']) {
            $bookingChildren[] = [
                'name'   => 'Booking Zoom',
                'route'  => 'bookings.zoom.index',
                'active' => $req->routeIs('bookings.zoom.*'),
            ];
        }
        if ($perms['room_bookings_view']) {
            $bookingChildren[] = [
                'name'   => 'Booking Ruangan',
                'route'  => 'bookings.ruangan.index',
                'active' => $req->routeIs('bookings.ruangan.*'),
            ];
        }
        if (!empty($bookingChildren)) {
            $items[] = [
                'name'     => 'Booking',
                'icon'     => 'clipboard-check',
                'active'   => $req->routeIs('bookings.*'),
                'children' => $bookingChildren,
            ];
        }

        // Reporting
        if ($perms['reports_view']) {
            $reportChildren = [
                [
                    'name'   => 'Laporan Armada',
                    'route'  => 'reports.armada',
                    'active' => $req->routeIs('reports.armada', 'reports.armada.show'),
                ],
                [
                    'name'   => 'Laporan Zoom',
                    'route'  => 'reports.zoom',
                    'active' => $req->routeIs('reports.zoom', 'reports.zoom.show'),
                ],
                [
                    'name'   => 'Laporan Ruangan',
                    'route'  => 'reports.ruangan',
                    'active' => $req->routeIs('reports.ruangan', 'reports.ruangan.show'),
                ],
            ];
            $items[] = [
                'name'     => 'Reporting',
                'icon'     => 'chart-bar',
                'active'   => $req->routeIs('reports.*'),
                'children' => $reportChildren,
            ];
        }

        // Notifikasi
        $canView = $perms['notifications_view'];
        $canSend = $perms['notifications_send'];

        if ($canView || $canSend) {
            $notifChildren = [];
            if ($canView) {
                $notifChildren[] = [
                    'name'   => 'Kotak Masuk',
                    'route'  => 'notifications.index',
                    'active' => $req->routeIs('notifications.index'),
                ];
            }
            if ($canSend) {
                $notifChildren[] = [
                    'name'   => 'Kirim Notifikasi',
                    'route'  => 'notifications.send',
                    'active' => $req->routeIs('notifications.send'),
                ];
            }

            if ($canView && $canSend) {
                $items[] = [
                    'name'     => 'Notifikasi',
                    'icon'     => 'bell',
                    'active'   => $req->routeIs('notifications.*'),
                    'children' => $notifChildren,
                ];
            } else {
                $items[] = [
                    'name'   => 'Notifikasi',
                    'route'  => $canView ? 'notifications.index' : 'notifications.send',
                    'icon'   => 'bell',
                    'active' => $req->routeIs('notifications.*'),
                ];
            }
        }

        // Chat
        if ($perms['chat_view']) {
            $items[] = [
                'name'   => 'Chat',
                'route'  => 'chat.index',
                'icon'   => 'chat',
                'active' => $req->routeIs('chat.*'),
            ];
        }

        // Pengaturan
        $settingsChildren = [];
        if ($perms['configuration_view']) {
            $settingsChildren[] = [
                'name'   => 'Konfigurasi System',
                'route'  => 'settings.system',
                'active' => $req->routeIs('settings.system'),
            ];
        }
        if ($perms['users_view']) {
            $settingsChildren[] = [
                'name'   => 'Manajemen User',
                'route'  => 'settings.users',
                'active' => $req->routeIs('settings.users'),
            ];
        }
        if ($perms['roles_view']) {
            $settingsChildren[] = [
                'name'   => 'Roles & Permissions',
                'route'  => 'settings.roles',
                'active' => $req->routeIs('settings.roles'),
            ];
        }
        if (!empty($settingsChildren)) {
            $items[] = [
                'name'     => 'Pengaturan',
                'icon'     => 'cog',
                'active'   => $req->routeIs('settings.*'),
                'children' => $settingsChildren,
            ];
        }

        $result = array_filter($items);
        $cache[$cacheKey] = $result;

        return $result;
    }
}
