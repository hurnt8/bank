<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserInvitationMail;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /** Roles an admin (non-super-admin) is allowed to assign */
    private const ADMIN_ALLOWED_ROLES = ['client', 'admin'];

    public function index(Request $request)
    {
        $authUser     = Auth::user();
        $isSuperAdmin = $authUser->hasRole('super-admin');

        $query = User::with('roles')->latest();

        // Les admins voient les clients qu'ils ont créés eux-mêmes, ainsi que
        // les comptes auto-inscrits via le site public (sans created_by) —
        // ces derniers n'appartiennent à aucun admin en particulier, donc
        // visibles par tous jusqu'à ce qu'un admin se charge du dossier.
        if (! $isSuperAdmin) {
            $query->where('type', 'client')
                  ->where(fn($q) => $q->where('created_by', $authUser->id)->orWhereNull('created_by'));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        if ($isSuperAdmin && $request->filled('type')) {
            $query->where('type', $request->type);
        }

        $users = $query->paginate(15)->appends($request->query());
        $roles = $isSuperAdmin
            ? Role::all()
            : Role::whereIn('name', self::ADMIN_ALLOWED_ROLES)->get();

        $ownOrUnassigned = fn($q) => $q->where(fn($q2) => $q2->where('created_by', $authUser->id)->orWhereNull('created_by'));

        $stats = [
            'total'  => User::when(! $isSuperAdmin, fn($q) => $q->where('type', 'client')->tap($ownOrUnassigned))->count(),
            'client' => User::where('type', 'client')
                            ->when(! $isSuperAdmin, $ownOrUnassigned)
                            ->count(),
            'staff'  => $isSuperAdmin ? User::where('type', 'staff')->count() : 0,
        ];

        $admins = $isSuperAdmin
            ? User::where('type', 'staff')->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))->orderBy('name')->get()
            : collect();

        return view('admin.users.index', compact('users', 'roles', 'stats', 'isSuperAdmin', 'admins'));
    }

    public function show(User $user)
    {
        $this->authorizeUser($user);

        $authUser     = Auth::user();
        $isSuperAdmin = $authUser->hasRole('super-admin');

        $user->load(['kycVerification', 'bankAccount', 'card']);

        $roles = $isSuperAdmin
            ? \Spatie\Permission\Models\Role::all()
            : \Spatie\Permission\Models\Role::whereIn('name', self::ADMIN_ALLOWED_ROLES)->get();

        return view('admin.users.show', compact('user', 'roles', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $authUser     = Auth::user();
        $isSuperAdmin = $authUser->hasRole('super-admin');

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'type'       => 'required|in:client,staff',
            'gender'     => 'nullable|in:M,F,N',
            'role'       => 'required|string|exists:roles,name',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'id_type'    => 'nullable|string|max:30',
            'id_number'  => 'nullable|string|max:60',
            'date_delivre' => 'nullable|date',
            'tax_number' => 'nullable|string|max:60',
            'activity'   => 'nullable|string|max:255',
            'currency'   => 'nullable|string|max:10',
            'locale'     => 'nullable|in:fr,en,pl,es,bg,hu,it,de,lt,ro,lv,nl,pt,hr,sk,sl,mt',
        ]);

        // Non-super-admins cannot assign the super-admin role
        if (! $isSuperAdmin && ! in_array($data['role'], self::ADMIN_ALLOWED_ROLES)) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à assigner ce rôle.');
        }

        $token = Str::random(64);

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make(Str::random(32)),
            'type'             => $data['type'],
            'gender'           => $data['gender'] ?? 'N',
            'created_by'       => $authUser->id,
            'invitation_token' => $token,
            'phone'            => $data['phone'] ?? null,
            'address'          => $data['address'] ?? null,
            'birth_date'       => $data['birth_date'] ?? null,
            'id_type'          => $data['id_type'] ?? null,
            'id_number'        => $data['id_number'] ?? null,
            'date_delivre'     => $data['date_delivre'] ?? null,
            'tax_number'       => $data['tax_number'] ?? null,
            'activity'         => $data['activity'] ?? null,
            'currency'         => $data['currency'] ?? Currency::default(),
            'locale'           => $data['locale'] ?? (in_array(app()->getLocale(), ['fr','en','pl','es','bg','hu','it','de','lt','ro','lv','nl','pt','hr','sk','sl','mt'], true) ? app()->getLocale() : 'fr'),
        ]);

        $user->assignRole($data['role']);

        $activationUrl = route('invitation.activate', ['token' => $token]);

        try {
            Mail::to($user->email)->locale($user->locale ?: 'fr')->send(new UserInvitationMail($user, $activationUrl));
            return back()->with('success', "Invitation envoyée à {$user->email}. {$user->name} recevra un email pour activer son compte.");
        } catch (\Exception) {
            return back()->with('success', "Client {$user->name} créé. L'email d'invitation n'a pas pu être envoyé (vérifiez la config mail). Lien d'activation : {$activationUrl}");
        }
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUser($user);

        $isSuperAdmin = Auth::user()->hasRole('super-admin');

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => "required|email|unique:users,email,{$user->id}",
            'type'       => 'required|in:client,staff',
            'role'       => 'required|string|exists:roles,name',
            'gender'     => 'nullable|in:M,F,N',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'id_type'    => 'nullable|string|max:30',
            'id_number'  => 'nullable|string|max:60',
            'date_delivre' => 'nullable|date',
            'tax_number' => 'nullable|string|max:60',
            'activity'   => 'nullable|string|max:255',
            'currency'   => 'nullable|string|max:10',
            'locale'     => 'nullable|in:fr,en,pl,es,bg,hu,it,de,lt,ro,lv,nl,pt,hr,sk,sl,mt',
        ]);

        if (! $isSuperAdmin && ! in_array($data['role'], self::ADMIN_ALLOWED_ROLES)) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à assigner ce rôle.');
        }

        // Mémoriser les anciennes valeurs avant mise à jour
        $oldEmail = $user->email;
        $oldName  = $user->name;

        $user->update([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'type'       => $data['type'],
            'gender'     => $data['gender'] ?? $user->gender ?? 'N',
            'phone'      => $data['phone'] ?? $user->phone,
            'address'    => $data['address'] ?? $user->address,
            'birth_date' => $data['birth_date'] ?? $user->birth_date,
            'id_type'    => $data['id_type'] ?? $user->id_type,
            'id_number'  => $data['id_number'] ?? $user->id_number,
            'date_delivre' => $data['date_delivre'] ?? $user->date_delivre,
            'tax_number' => $data['tax_number'] ?? $user->tax_number,
            'activity'   => $data['activity'] ?? $user->activity,
            'currency'   => $data['currency'] ?? $user->currency,
            'locale'     => $data['locale'] ?? $user->locale,
        ]);

        $user->syncRoles([$data['role']]);

        return back()->with('success', "Utilisateur {$user->name} mis à jour.");
    }

    public function resendInvitation(User $user)
    {
        $this->authorizeUser($user);

        if (! $user->invitation_token) {
            return back()->with('error', 'Ce compte est déjà activé.');
        }

        $activationUrl = route('invitation.activate', ['token' => $user->invitation_token]);

        try {
            Mail::to($user->email)->locale($user->locale ?: 'fr')->send(new UserInvitationMail($user, $activationUrl));
            return back()->with('success', "Invitation renvoyée à {$user->email}.");
        } catch (\Exception $e) {
            return back()->with('error', "Erreur d'envoi : " . $e->getMessage());
        }
    }

    public function assignAdmin(Request $request, User $user)
    {
        abort_unless(Auth::user()->hasRole('super-admin'), 403);
        abort_unless($user->hasRole('client'), 422, 'Seuls les clients peuvent être réaffectés à un admin.');

        $data = $request->validate([
            'admin_id' => 'required|exists:users,id',
        ]);

        $admin = User::findOrFail($data['admin_id']);
        abort_unless(
            $admin->hasRole('admin') || $admin->hasRole('super-admin'),
            422,
            'L\'utilisateur sélectionné n\'est pas un administrateur.'
        );

        $user->update(['created_by' => $admin->id]);

        return back()->with('success', "Client {$user->name} réaffecté à {$admin->name} avec succès.");
    }

    public function destroy(User $user)
    {
        $this->authorizeUser($user);

        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Impossible de supprimer un super-administrateur.');
        }
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }

    /**
     * Vérifie qu'un admin classique gère bien cet utilisateur (créateur
     * du compte) avant de le consulter/modifier.
     * Bypass pour le super-admin.
     */
    private function authorizeUser(User $user): void
    {
        $authUser = Auth::user();
        if ($authUser->hasRole('super-admin')) return;

        $hasAccess = $user->created_by === $authUser->id;
        abort_unless($hasAccess, 403, 'Accès non autorisé.');
    }
}
