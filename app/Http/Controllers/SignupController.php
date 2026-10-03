<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewAccountMail;
use App\Mail\UserInvitationMail;
use App\Models\AdminNotification;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SignupController extends Controller
{
    private const SUPPORTED_LOCALES = ['fr', 'en', 'pl', 'es', 'bg', 'hu', 'it', 'de', 'lt', 'ro', 'lv', 'nl', 'pt', 'hr'];

    public function create(string $locale)
    {
        return view('signup', ['locale' => $locale]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|string|max:50',
            'address'      => 'nullable|string|max:500',
            'country'      => 'required|string|max:100',
            'birth_date'   => 'nullable|date',
            'id_type'      => 'nullable|string|max:30',
            'id_number'    => 'nullable|string|max:60',
            'date_delivre' => 'nullable|date',
            'tax_number'   => 'nullable|string|max:60',
            'activity'     => 'nullable|string|max:255',
            'currency'     => 'nullable|string|max:10',
        ]);

        $locale = $request->input('locale', 'fr');
        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'fr';
        }

        $token = Str::random(64);

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make(Str::random(32)),
            'type'             => 'client',
            'gender'           => 'N',
            'invitation_token' => $token,
            'phone'            => $data['phone'],
            'address'          => $data['address'] ?? null,
            'country'          => $data['country'],
            'birth_date'       => $data['birth_date'] ?? null,
            'id_type'          => $data['id_type'] ?? null,
            'id_number'        => $data['id_number'] ?? null,
            'date_delivre'     => $data['date_delivre'] ?? null,
            'tax_number'       => $data['tax_number'] ?? null,
            'activity'         => $data['activity'] ?? null,
            'currency'         => $data['currency'] ?? Currency::default(),
            'locale'           => $locale,
        ]);

        $user->assignRole('client');

        $activationUrl = route('invitation.activate', ['token' => $token]);

        try {
            Mail::to($user->email)->send(new UserInvitationMail($user, $activationUrl));
        } catch (\Exception) {
            // L'inscription reste valide même si l'email échoue ; l'admin peut renvoyer l'invitation.
        }

        $this->notifyAdmins($user);

        return back()->with('success', __('signup.success'));
    }

    /**
     * Auto-inscription publique : pas de created_by (aucun admin assigné) —
     * on notifie donc tous les super-admins, à la différence de
     * AdminNotification::recipientAdminIds() utilisé pour les clients déjà rattachés.
     */
    private function notifyAdmins(User $client): void
    {
        $adminIds = User::role('super-admin')->pluck('id');

        foreach ($adminIds as $adminId) {
            AdminNotification::forAdmin(
                $adminId,
                'account',
                'Nouveau compte créé — ' . $client->name,
                $client->name . ' (' . $client->email . ') vient de créer un compte via le site public.',
                ['client_id' => $client->id]
            );

            $admin = User::find($adminId);
            if ($admin) {
                try {
                    Mail::to($admin->email)->send(new AdminNewAccountMail($client, $admin->locale ?? 'fr'));
                } catch (\Exception) {
                    // La notification en cloche (AdminNotification) reste créée même si l'email échoue.
                }
            }
        }
    }
}
