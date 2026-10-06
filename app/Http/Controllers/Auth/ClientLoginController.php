<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClientLoginController extends Controller
{
    private const OTP_TTL = 600;

    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticated(Auth::user());
        }

        $remembered = null;
        $raw = request()->cookie('solberg_remembered');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (isset($decoded['name'], $decoded['email'])) {
                $remembered = $decoded;
            }
        }

        return view('auth.login-client', compact('remembered'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
            'password'   => 'required',
        ]);

        $identifier = $request->input('identifier');

        $user = User::where('email', $identifier)
                    ->orWhere('phone', $identifier)
                    ->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors(['identifier' => __('auth.failed')])
                ->onlyInput('identifier');
        }

        if ($user->type !== 'client') {
            return back()->withErrors(['identifier' => __('auth.portal_clients_only')]);
        }

        // Compte non activé : l'adresse e-mail doit d'abord être confirmée.
        if (! $user->email_verified_at) {
            return back()
                ->withErrors(['identifier' => __('onboarding.email_not_verified')])
                ->with('unverified_email', $user->email)
                ->onlyInput('identifier');
        }

        if ($user->is_blocked) {
            return back()
                ->withErrors(['identifier' => __('auth.account_blocked')])
                ->onlyInput('identifier');
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('otp_' . $user->id, Hash::make($otp), self::OTP_TTL);

        $request->session()->put('otp_user_id', $user->id);
        $request->session()->put('otp_remember', $request->boolean('remember'));

        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        } catch (\Throwable $e) {
            Log::error('ClientLoginController: échec envoi OTP', ['user_id' => $user->id, 'message' => $e->getMessage()]);
            return back()->withErrors(['identifier' => __('auth.otp_send_failed')])->onlyInput('identifier');
        }

        $cookie = Cookie::make(
            'solberg_remembered',
            json_encode(['name' => $user->name, 'email' => $user->email]),
            60 * 24 * 30
        );

        return redirect()->route('otp.show')->withCookie($cookie);
    }

    public function forgetAccount()
    {
        return redirect('/login')->withCookie(Cookie::forget('solberg_remembered'));
    }

    public function logout(Request $request)
    {
        $isStaff = Auth::check() && Auth::user()->type === 'staff';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($isStaff ? route('staff.login') : '/login');
    }

    private function redirectAuthenticated(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        // Clients → PWA (client.app.home)
        return redirect()->route('client.app.home');
    }
}
