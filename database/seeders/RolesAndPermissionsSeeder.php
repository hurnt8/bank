<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Rôles, permissions et comptes staff par défaut.
 *
 * Idempotent : peut être rejoué sans risque.
 *  - les permissions et les rôles sont créés s'ils manquent, puis resynchronisés ;
 *  - les comptes par défaut ne sont créés QUE si aucun utilisateur ne porte déjà le rôle
 *    (jamais de second super-admin sur une installation en service) ;
 *  - aucun mot de passe n'est écrit dans le code : il vient de l'environnement
 *    (SEED_SUPERADMIN_PASSWORD / SEED_ADMIN_PASSWORD) ou est généré aléatoirement et
 *    affiché une seule fois.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /** Permissions métier de base. */
    public const BASE_PERMISSIONS = [
        'view users',
        'manage users',
        'manage roles',
    ];

    /** Permissions supprimées avec la fonctionnalité de prêt : retirées de la base si elles existent. */
    public const OBSOLETE_PERMISSIONS = [
        'view loans', 'manage loans', 'manage-loan-settings', 'manage-notification-templates',
    ];

    /** Permissions par rôle (le super-admin reçoit toutes les permissions). */
    public const ROLE_PERMISSIONS = [
        'client'      => [],
        'admin'       => ['view users'],
        'super-admin' => '*',
    ];

    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();

        // ── Permissions : base + permissions « exceptionnelles » accordables à un admin ──
        $all = array_merge(self::BASE_PERMISSIONS, array_keys(ExceptionalPermissionsSeeder::PERMISSIONS));
        foreach ($all as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Permission::whereIn('name', self::OBSOLETE_PERMISSIONS)->delete();

        // ── Rôles ──
        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions === '*' ? $all : $permissions);
        }

        $registrar->forgetCachedPermissions();

        // ── Comptes staff par défaut (uniquement si le rôle n'a encore aucun titulaire) ──
        $domain  = $this->domain();
        $created = [];

        $created[] = $this->seedStaff(
            role:     'super-admin',
            name:     'Super Admin',
            email:    env('SEED_SUPERADMIN_EMAIL', 'contact@' . $domain),
            password: env('SEED_SUPERADMIN_PASSWORD'),
        );

        $created[] = $this->seedStaff(
            role:     'admin',
            name:     'Admin',
            email:    env('SEED_ADMIN_EMAIL', 'admin@' . $domain),
            password: env('SEED_ADMIN_PASSWORD'),
        );

        $created = array_values(array_filter($created));

        $this->command?->info('Rôles et permissions synchronisés (' . count($all) . ' permissions).');

        if ($created) {
            $this->command?->warn('Comptes créés — notez les identifiants, le mot de passe n\'est affiché qu\'une fois :');
            $this->command?->table(['Rôle', 'E-mail', 'Mot de passe'], $created);
        } else {
            $this->command?->info('Comptes staff déjà présents : aucun compte créé.');
        }
    }

    /**
     * Crée le compte du rôle s'il n'existe aucun titulaire ; renvoie la ligne à afficher, ou null.
     */
    private function seedStaff(string $role, string $name, string $email, ?string $password): ?array
    {
        if (User::role($role)->exists()) {
            return null;
        }

        $generated = $password === null || $password === '';
        $plain     = $generated ? Str::password(16) : $password;

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($plain), 'type' => 'staff']
        );

        // Un compte existant avec cet e-mail garde son mot de passe : on ne l'affiche pas.
        $isNew = $user->wasRecentlyCreated;

        $user->syncRoles([$role]);

        return [$role, $email, $isNew ? ($generated ? $plain : '(défini via .env)') : '(compte existant, inchangé)'];
    }

    /** Domaine des adresses par défaut, déduit de l'e-mail expéditeur configuré. */
    private function domain(): string
    {
        $from = (string) config('mail.from.address', '');

        return str_contains($from, '@') ? Str::after($from, '@') : 'example.com';
    }
}
