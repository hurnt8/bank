<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyAccountMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /** Page « consultez votre boîte mail » affichée juste après l'inscription. */
    public function notice(Request $request)
    {
        $email = $request->session()->get('verify_email');

        if (! $email) {
            return redirect()->route('login');
        }

        $request->session()->keep(['verify_email']);

        return view('auth.verify-notice', compact('email'));
    }

    /**
     * Lien d'activation (URL signée, valable 7 jours). Idempotent : un second clic sur un
     * compte déjà activé redirige simplement vers la connexion, sans erreur.
     */
    public function verify(Request $request, string $uuid, string $hash)
    {
        $user = User::where('uuid', $uuid)->first();

        if (! $user || ! hash_equals(sha1($user->email), $hash)) {
            return redirect()->route('login')->withErrors(['identifier' => __('onboarding.link_invalid')]);
        }

        // Déjà activé : pas d'erreur, même si le lien a entre-temps expiré.
        if ($user->email_verified_at) {
            return redirect()->route('login')->with('verified_status', __('onboarding.already_verified'));
        }

        if (! $request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['identifier' => __('onboarding.link_invalid')]);
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('login')->with('verified_status', __('onboarding.verified_success'));
    }

    /** Renvoi de l'e-mail d'activation (depuis la page de confirmation ou la page de connexion). */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->input('email'))->first();

        // Réponse identique que le compte existe ou non (pas d'énumération d'adresses).
        if ($user && $user->type === 'client' && ! $user->email_verified_at) {
            $sent = self::sendActivationMail($user);
            if (! $sent) {
                return back()->with('verify_email', $user->email)->with('error', __('onboarding.resend_failed'));
            }
        }

        return back()->with('verify_email', $request->input('email'))->with('resent', __('onboarding.resent'));
    }

    public static function activationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addDays(7),
            ['uuid' => $user->uuid, 'hash' => sha1($user->email)]
        );
    }

    public static function sendActivationMail(User $user): bool
    {
        try {
            Mail::to($user->email)->send(new VerifyAccountMail($user, self::activationUrl($user)));

            return true;
        } catch (\Throwable $e) {
            Log::error('EmailVerification: échec envoi activation', ['user_id' => $user->id, 'message' => $e->getMessage()]);

            return false;
        }
    }
}
