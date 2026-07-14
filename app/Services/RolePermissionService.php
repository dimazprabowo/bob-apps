<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionService
{
    public function getAllRolesWithPermissions(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function getRolePermissions(int $roleId): array
    {
        $role = Role::with('permissions')->find($roleId);

        return $role ? $role->permissions->pluck('name')->toArray() : [];
    }

    public function createRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function updateRole(Role $role, string $name, array $permissions = []): Role
    {
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
    }

    public function togglePermission(Role $role, string $permission): string
    {
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);

            return 'revoked';
        }

        $role->givePermissionTo($permission);

        return 'granted';
    }

    public function roleHasUsers(Role $role): bool
    {
        return $role->users()->count() > 0;
    }

    /**
     * Build permission groups dynamically from database.
     * Maps permissions to groups based on naming convention.
     * Any unmatched permissions go to 'Lainnya' group.
     *
     * Returns: ['Group Name' => [['name' => 'permission_key', 'label' => 'Human Label'], ...]]
     */
    public function buildPermissionGroups(): array
    {
        $groupMapping = [
            'Dashboard' => [
                ['name' => 'dashboard_view', 'label' => 'Lihat Dashboard'],
            ],
            'Master Data — Perusahaan' => [
                ['name' => 'companies_view',         'label' => 'Lihat Perusahaan'],
                ['name' => 'companies_create',       'label' => 'Tambah Perusahaan'],
                ['name' => 'companies_update',       'label' => 'Edit Perusahaan'],
                ['name' => 'companies_delete',       'label' => 'Hapus Perusahaan'],
                ['name' => 'companies_export_excel', 'label' => 'Export Excel Perusahaan'],
                ['name' => 'companies_export_pdf',   'label' => 'Export PDF Perusahaan'],
            ],
            'Master Data — Armada' => [
                ['name' => 'vehicles_view',         'label' => 'Lihat Armada'],
                ['name' => 'vehicles_create',       'label' => 'Tambah Armada'],
                ['name' => 'vehicles_update',       'label' => 'Edit Armada'],
                ['name' => 'vehicles_delete',       'label' => 'Hapus Armada'],
                ['name' => 'vehicles_export_excel', 'label' => 'Export Excel Armada'],
                ['name' => 'vehicles_export_pdf',   'label' => 'Export PDF Armada'],
            ],
            'Master Data — Ruangan' => [
                ['name' => 'rooms_view',         'label' => 'Lihat Ruangan'],
                ['name' => 'rooms_create',       'label' => 'Tambah Ruangan'],
                ['name' => 'rooms_update',       'label' => 'Edit Ruangan'],
                ['name' => 'rooms_delete',       'label' => 'Hapus Ruangan'],
                ['name' => 'rooms_export_excel', 'label' => 'Export Excel Ruangan'],
                ['name' => 'rooms_export_pdf',   'label' => 'Export PDF Ruangan'],
            ],
            'Booking — Armada' => [
                ['name' => 'vehicle_bookings_view',         'label' => 'Lihat List Booking Armada (semua)'],
                ['name' => 'vehicle_bookings_view_own',     'label' => 'Lihat List Booking Armada (sendiri)'],
                ['name' => 'vehicle_bookings_show',         'label' => 'Lihat Detail Booking Armada (semua)'],
                ['name' => 'vehicle_bookings_show_own',     'label' => 'Lihat Detail Booking Armada (sendiri)'],
                ['name' => 'vehicle_bookings_create',       'label' => 'Ajukan Booking Armada'],
                ['name' => 'vehicle_bookings_update',       'label' => 'Edit Booking Armada (semua)'],
                ['name' => 'vehicle_bookings_update_own',   'label' => 'Edit Booking Armada (sendiri)'],
                ['name' => 'vehicle_bookings_delete',       'label' => 'Hapus Booking Armada (semua)'],
                ['name' => 'vehicle_bookings_delete_own',   'label' => 'Hapus Booking Armada (sendiri)'],
                ['name' => 'vehicle_bookings_approve',      'label' => 'Approve/Reject Booking Armada'],
                ['name' => 'vehicle_bookings_export_excel', 'label' => 'Export Excel Booking Armada'],
                ['name' => 'vehicle_bookings_export_pdf',   'label' => 'Export PDF Booking Armada'],
            ],
            'Booking — Zoom' => [
                ['name' => 'zoom_bookings_view',         'label' => 'Lihat List Booking Zoom (semua)'],
                ['name' => 'zoom_bookings_view_own',     'label' => 'Lihat List Booking Zoom (sendiri)'],
                ['name' => 'zoom_bookings_show',         'label' => 'Lihat Detail Booking Zoom (semua)'],
                ['name' => 'zoom_bookings_show_own',     'label' => 'Lihat Detail Booking Zoom (sendiri)'],
                ['name' => 'zoom_bookings_create',       'label' => 'Ajukan Booking Zoom'],
                ['name' => 'zoom_bookings_update',       'label' => 'Edit Booking Zoom (semua)'],
                ['name' => 'zoom_bookings_update_own',   'label' => 'Edit Booking Zoom (sendiri)'],
                ['name' => 'zoom_bookings_delete',       'label' => 'Hapus Booking Zoom (semua)'],
                ['name' => 'zoom_bookings_delete_own',   'label' => 'Hapus Booking Zoom (sendiri)'],
                ['name' => 'zoom_bookings_approve',      'label' => 'Approve/Reject Booking Zoom'],
                ['name' => 'zoom_bookings_export_excel', 'label' => 'Export Excel Booking Zoom'],
                ['name' => 'zoom_bookings_export_pdf',   'label' => 'Export PDF Booking Zoom'],
            ],
            'Booking — Ruangan' => [
                ['name' => 'room_bookings_view',         'label' => 'Lihat List Booking Ruangan (semua)'],
                ['name' => 'room_bookings_view_own',     'label' => 'Lihat List Booking Ruangan (sendiri)'],
                ['name' => 'room_bookings_show',         'label' => 'Lihat Detail Booking Ruangan (semua)'],
                ['name' => 'room_bookings_show_own',     'label' => 'Lihat Detail Booking Ruangan (sendiri)'],
                ['name' => 'room_bookings_create',       'label' => 'Ajukan Booking Ruangan'],
                ['name' => 'room_bookings_update',       'label' => 'Edit Booking Ruangan (semua)'],
                ['name' => 'room_bookings_update_own',   'label' => 'Edit Booking Ruangan (sendiri)'],
                ['name' => 'room_bookings_delete',       'label' => 'Hapus Booking Ruangan (semua)'],
                ['name' => 'room_bookings_delete_own',   'label' => 'Hapus Booking Ruangan (sendiri)'],
                ['name' => 'room_bookings_approve',      'label' => 'Approve/Reject Booking Ruangan'],
                ['name' => 'room_bookings_export_excel', 'label' => 'Export Excel Booking Ruangan'],
                ['name' => 'room_bookings_export_pdf',   'label' => 'Export PDF Booking Ruangan'],
            ],
            'Reporting' => [
                ['name' => 'reports_view',         'label' => 'Lihat Reporting'],
                ['name' => 'reports_export_excel', 'label' => 'Export Excel Report'],
                ['name' => 'reports_export_pdf',   'label' => 'Export PDF Report'],
            ],
            'Konfigurasi System' => [
                ['name' => 'configuration_view',         'label' => 'Lihat Konfigurasi'],
                ['name' => 'configuration_update',       'label' => 'Edit Konfigurasi'],
                ['name' => 'configuration_export_excel', 'label' => 'Export Excel Konfigurasi'],
                ['name' => 'configuration_export_pdf',   'label' => 'Export PDF Konfigurasi'],
            ],
            'Manajemen User' => [
                ['name' => 'users_view',         'label' => 'Lihat User'],
                ['name' => 'users_create',       'label' => 'Tambah User'],
                ['name' => 'users_update',       'label' => 'Edit User'],
                ['name' => 'users_delete',       'label' => 'Hapus User'],
                ['name' => 'users_export_excel', 'label' => 'Export Excel User'],
                ['name' => 'users_export_pdf',   'label' => 'Export PDF User'],
                ['name' => 'users_impersonate',  'label' => 'Impersonate User'],
            ],
            'Roles & Permissions' => [
                ['name' => 'roles_view',         'label' => 'Lihat Roles'],
                ['name' => 'roles_create',       'label' => 'Tambah Role'],
                ['name' => 'roles_update',       'label' => 'Edit Role'],
                ['name' => 'roles_delete',       'label' => 'Hapus Role'],
                ['name' => 'roles_export_excel', 'label' => 'Export Excel Roles'],
                ['name' => 'roles_export_pdf',   'label' => 'Export PDF Roles'],
            ],
            'Notifikasi' => [
                ['name' => 'notifications_view', 'label' => 'Lihat Notifikasi'],
                ['name' => 'notifications_send', 'label' => 'Kirim Notifikasi'],
            ],
            'Chat' => [
                ['name' => 'chat_view',   'label' => 'Lihat Chat'],
                ['name' => 'chat_create', 'label' => 'Buat Chat'],
                ['name' => 'chat_delete', 'label' => 'Hapus Chat'],
            ],
        ];

        $allPermissions = Permission::pluck('name')->toArray();
        $mapped = collect($groupMapping)->flatten(1)->pluck('name')->toArray();
        $unmapped = array_diff($allPermissions, $mapped);

        $groups = $groupMapping;

        if (!empty($unmapped)) {
            $groups['Lainnya'] = array_values(array_map(
                fn($p) => ['name' => $p, 'label' => ucwords(str_replace('_', ' ', $p))],
                $unmapped
            ));
        }

        return $groups;
    }
}
