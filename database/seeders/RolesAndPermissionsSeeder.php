<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'view loans',
            'manage loans',
            'view users',
            'manage users',
            'manage roles',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $clientRole->syncPermissions(['view loans']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(['view loans', 'manage loans', 'view users']);

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions($permissions);

        // Les comptes par defaut ont change de domaine lors du passage a Mellenthin
        // Financial. Sans ce renommage, le firstOrCreate ci-dessous ne retrouverait pas
        // le compte existant et creerait un SECOND super-admin sur les installations
        // deja en service. Le mot de passe, lui, reste inchange.
        $legacyAccounts = [
            'support@aurenzafinancial.online' => 'contact@bank.expediva.online',
            'noreply@aurenzafinancial.online' => 'noreply@mellenthinfinancial.online',
        ];

        foreach ($legacyAccounts as $oldEmail => $newEmail) {
            if (User::where('email', $newEmail)->exists()) {
                continue;
            }

            $renamed = User::where('email', $oldEmail)->update(['email' => $newEmail]);

            if ($renamed) {
                $this->command?->warn("Compte renomme : {$oldEmail} -> {$newEmail} (mot de passe inchange)");
            }
        }

        // Default super-admin account
        $superAdmin = User::firstOrCreate(
            ['email' => 'contact@bank.expediva.online'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('ChangeMe@2025!'),
                'type'     => 'staff',
            ]
        );
        $superAdmin->syncRoles(['super-admin']);

        // Default admin account
        $admin = User::firstOrCreate(
            ['email' => 'noreply@mellenthinfinancial.online'],
            [
                'name'     => 'Admin Mellenthin Financial',
                'password' => Hash::make('Admin@2025!'),
                'type'     => 'staff',
            ]
        );
        $admin->syncRoles(['admin']);

        $this->command->info('Roles, permissions and default accounts created.');
        $this->command->table(
            ['Role', 'Email', 'Password (change immediately)'],
            [
                ['super-admin', 'contact@bank.expediva.online', 'ChangeMe@2025!'],
                ['admin',       'noreply@mellenthinfinancial.online',      'Admin@2025!'],
            ]
        );
    }
}
