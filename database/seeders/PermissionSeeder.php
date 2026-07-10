<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Single source of truth untuk semua permissions.
     *
     * Idempotent — aman dijalankan berulang kali di production:
     *   php artisan db:seed --class=PermissionSeeder
     *
     * Konvensi penamaan:
     *   {entity}_{action}
     *   entity : dashboard, companies, configuration, users, roles, notifications, chat
     *   action : view, create, update, delete, export_excel, export_pdf, send
     *
     * Format ini memudahkan grouping otomatis di UI berdasarkan entity prefix.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard_view',

            // Master Data — Perusahaan
            'companies_view',
            'companies_create',
            'companies_update',
            'companies_delete',
            'companies_export_excel',
            'companies_export_pdf',

            // Master Data — Armada (Vehicles)
            'vehicles_view',
            'vehicles_create',
            'vehicles_update',
            'vehicles_delete',
            'vehicles_export_excel',
            'vehicles_export_pdf',

            // Master Data — Ruangan (Rooms)
            'rooms_view',
            'rooms_create',
            'rooms_update',
            'rooms_delete',
            'rooms_export_excel',
            'rooms_export_pdf',

            // Booking Armada (Vehicle Bookings)
            'vehicle_bookings_view',
            'vehicle_bookings_create',
            'vehicle_bookings_update',
            'vehicle_bookings_delete',
            'vehicle_bookings_approve',
            'vehicle_bookings_export_excel',
            'vehicle_bookings_export_pdf',

            // Booking Zoom (Zoom Bookings)
            'zoom_bookings_view',
            'zoom_bookings_create',
            'zoom_bookings_update',
            'zoom_bookings_delete',
            'zoom_bookings_approve',
            'zoom_bookings_export_excel',
            'zoom_bookings_export_pdf',

            // Booking Ruangan (Room Bookings)
            'room_bookings_view',
            'room_bookings_create',
            'room_bookings_update',
            'room_bookings_delete',
            'room_bookings_approve',
            'room_bookings_export_excel',
            'room_bookings_export_pdf',

            // Reporting
            'reports_view',
            'reports_export_excel',
            'reports_export_pdf',

            // Konfigurasi System
            'configuration_view',
            'configuration_update',
            'configuration_export_excel',
            'configuration_export_pdf',

            // Manajemen User
            'users_view',
            'users_create',
            'users_update',
            'users_delete',
            'users_export_excel',
            'users_export_pdf',
            'users_impersonate',

            // Roles & Permissions
            'roles_view',
            'roles_create',
            'roles_update',
            'roles_delete',
            'roles_export_excel',
            'roles_export_pdf',

            // Notifikasi
            'notifications_view',
            'notifications_send',

            // Chat / Pesan
            'chat_view',
            'chat_create',
            'chat_delete',

            // Profile - Company Management
            'manage_own_company',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
