<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\SiteContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StaffLoginController extends Controller
{
    private const OTP_TTL = 600;

    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticated(Auth::user());
        }
        return view('auth.login-staff');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->type !== 'staff') {
                Auth::logout();
                return back()->withErrors(['email' => 'Accès refusé. Ce portail est réservé au personnel autorisé.']);
            }

            if (!$user->hasAnyRole(['admin', 'super-admin'])) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte ne dispose pas des droits nécessaires.']);
            }

            // OTP désactivé pour le personnel : la session ouverte par Auth::attempt() est conservée.
            if (! SiteContact::current()->otp_staff_enabled) {
                $request->session()->regenerate();

                return $this->redirectAuthenticated($user);
            }

            $remember = $request->boolean('remember');
            // Auth::attempt() vient de connecter la session — on la referme aussitôt
            // pour exiger la vérification OTP (2FA) avant d'accorder l'accès réel.
            Auth::logout();

            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Cache::put('otp_' . $user->id, Hash::make($otp), self::OTP_TTL);

            $request->session()->put('otp_user_id', $user->id);
            $request->session()->put('otp_remember', $remember);
            $request->session()->put('otp_flow', 'staff');

            try {
                Mail::to($user->email)->locale($user->locale ?: 'fr')->send(new OtpMail($otp, $user));
            } catch (\Throwable $e) {
                Log::error('StaffLoginController: échec envoi OTP', ['user_id' => $user->id, 'message' => $e->getMessage()]);
                return back()->withErrors(['email' => 'Impossible d\'envoyer le code de vérification.']);
            }

            return redirect()->route('otp.show');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('staff.login');
    }

    private function redirectAuthenticated($user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
}
