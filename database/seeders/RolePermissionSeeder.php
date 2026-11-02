<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DAFTAR PERMISSION YANG DIPERLUKAN
        $permissions = [
            // Photobooth / Register
            'view_any_ticketing',
            'view_ticketing',
            'create_ticketing',
            'update_ticketing',
            'delete_ticketing',
            'delete_any_ticketing',

            // Laporan E-Statement (Hanya Super Admin / Finance)
            'view_monthly_recap',

            // Studio: Booking
            'view_any_booking',
            'view_booking',
            'create_booking',
            'update_booking',
            'delete_booking',
            'delete_any_booking',

            // Studio: Voucher
            'view_any_voucher',
            'view_voucher',
            'create_voucher',
            'update_voucher',
            'delete_voucher',

            // Operational: Packages
            'view_any_package',
            'view_package',
            'create_package',
            'update_package',
            'delete_package',

            // Operational: Backgrounds
            'view_any_background',
            'view_background',
            'create_background',
            'update_background',
            'delete_background',

            // Operational: Addons
            'view_any_addon',
            'view_addon',
            'create_addon',
            'update_addon',
            'delete_addon',

            // Operational: Operating Hours
            'view_any_operational::settings',
            'view_operational::settings',
            'create_operational::settings',
            'update_operational::settings',
            'delete_operational::settings',

            // Operational: Unavailable Days
            'view_any_blocked::days',
            'view_blocked::days',
            'create_blocked::days',
            'update_blocked::days',
            'delete_blocked::days',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 2. ROLE STUDIO
        // Akses Studio & Operational, TIDAK BISA akses Photobooth dan TIDAK BISA akses E-Statement
        $studioRole = Role::firstOrCreate(['name' => 'studio', 'guard_name' => 'web']);
        $studioPermissions = [
            'view_any_booking', 'view_booking', 'create_booking', 'update_booking', 'delete_booking', 'delete_any_booking',
            'view_any_voucher', 'view_voucher', 'create_voucher', 'update_voucher', 'delete_voucher',
            'view_any_package', 'view_package', 'create_package', 'update_package', 'delete_package',
            'view_any_background', 'view_background', 'create_background', 'update_background', 'delete_background',
            'view_any_addon', 'view_addon', 'create_addon', 'update_addon', 'delete_addon',
            'view_any_operational::settings', 'view_operational::settings', 'create_operational::settings', 'update_operational::settings', 'delete_operational::settings',
            'view_any_blocked::days', 'view_blocked::days', 'create_blocked::days', 'update_blocked::days', 'delete_blocked::days',
        ];
        $studioRole->syncPermissions($studioPermissions);

        // 3. ROLE EVENT (Bisa Mengubah & Menambahkan di Register, Waiting List, Frame Manage)
        // HANYA Photobooth / Register, Waiting List, Frame Manage. TIDAK BISA akses Studio/Operational
        $eventRole = Role::firstOrCreate(['name' => 'event', 'guard_name' => 'web']);
        $eventPermissions = [
            'view_any_ticketing',
            'view_ticketing',
            'create_ticketing',
            'update_ticketing',
            'delete_ticketing',
            'delete_any_ticketing',
        ];
        $eventRole->syncPermissions($eventPermissions);

        // 4. ROLE EVENT VIEWER (Read-only di Register, tapi bisa ceklis Foto, Waiting List, Frame Manage)
        $eventViewerRole = Role::firstOrCreate(['name' => 'event_viewer', 'guard_name' => 'web']);
        $eventViewerPermissions = [
            'view_any_ticketing',
            'view_ticketing',
        ];
        $eventViewerRole->syncPermissions($eventViewerPermissions);

        // 5. ROLE SUPER ADMIN (Akses Semua)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());
    }
}
