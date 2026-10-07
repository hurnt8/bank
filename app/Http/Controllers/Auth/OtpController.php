<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountBlockedMail;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    private const OTP_TTL      = 600;  // 10 minutes
    private const MAX_ATTEMPTS = 4;    // blocked after 4 wrong codes
    private const MAX_RESENDS  = 3;
    private const UNBLOCK_TTL  = 172800; // 48 hours in seconds

    public function show(Request $request)
    {
        $isStaff  = $request->session()->get('otp_flow') === 'staff';
        $loginUrl = $isStaff ? route('staff.login') : '/login';

        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return redirect($loginUrl);
        }

        $user = User::find($userId);
        if (!$user) {
            $request->session()->forget(['otp_user_id', 'otp_remember', 'otp_flow']);
            return redirect($loginUrl);
        }

        $masked = $this->maskEmail($user->email);
        $view   = $isStaff ? 'auth.otp-verify-staff' : 'auth.otp-verify';

        return view($view, ['masked' => $masked, 'backUrl' => $loginUrl]);
    }

    public function verify(Request $request)
    {
        $isAjax   = $request->expectsJson();
        $flow     = $request->session()->get('otp_flow', 'client');
        $loginUrl = $flow === 'staff' ? route('staff.login') : '/login';

        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return $isAjax
                ? response()->json(['status' => 'redirect', 'url' => $loginUrl])
                : redirect($loginUrl);
        }

        $request->validate(['code' => 'required|string|digits:6']);

        $user = User::find($userId);
        if (!$user) {
            return $isAjax
                ? response()->json(['status' => 'redirect', 'url' => $loginUrl])
                : redirect($loginUrl);
        }

        if ($user->is_blocked) {
            $request->session()->forget(['otp_user_id', 'otp_remember', 'otp_flow']);
            $msg = __('auth.account_blocked');
            return $isAjax
                ? response()->json(['status' => 'blocked', 'url' => $loginUrl, 'message' => $msg])
                : redirect($loginUrl)->withErrors(['identifier' => $msg]);
        }

        $attemptKey = 'otp_attempts_' . $userId;
        $cached     = Cache::get('otp_' . $userId);

        if (!$cached || !Hash::check($request->input('code'), $cached)) {
            RateLimiter::hit($attemptKey, self::OTP_TTL);
            $attempts  = RateLimiter::attempts($attemptKey);
            $remaining = self::MAX_ATTEMPTS - $attempts;

            if ($remaining <= 0) {
                return $this->blockAccount($user, $request);
            }

            $msg = __('auth.otp_invalid', ['remaining' => $remaining]);
            return $isAjax
                ? response()->json(['status' => 'error', 'message' => $msg, 'remaining' => $remaining])
                : back()->withErrors(['code' => $msg]);
        }

        // Valid — clean up all OTP data
        Cache::forget('otp_' . $userId);
        RateLimiter::clear($attemptKey);
        RateLimiter::clear('otp_resend_' . $userId);

        $remember = (bool) $request->session()->pull('otp_remember', false);
        $request->session()->forget(['otp_user_id', 'otp_flow']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $url = $flow === 'staff'
            ? ($user->hasRole('super-admin') ? route('super-admin.dashboard') : route('admin.dashboard'))
            : route('client.app.home');

        return $isAjax
            ? response()->json(['status' => 'success', 'url' => $url])
            : redirect($url);
    }

    public function resend(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return response()->json(['error' => __('auth.otp_session_expired')], 422);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => __('auth.otp_session_expired')], 422);
        }

        if ($user->is_blocked) {
            return response()->json(['error' => __('auth.account_blocked')], 403);
        }

        $resendKey = 'otp_resend_' . $userId;
        if (RateLimiter::tooManyAttempts($resendKey, self::MAX_RESENDS)) {
            return response()->json(['error' => __('auth.otp_resend_limit')], 429);
        }
        RateLimiter::hit($resendKey, self::OTP_TTL);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('otp_' . $userId, Hash::make($otp), self::OTP_TTL);
        RateLimiter::clear('otp_attempts_' . $userId);

        try {
            Mail::to($user->email)->locale($user->locale ?: 'fr')->send(new OtpMail($otp, $user));
        } catch (\Throwable $e) {
            Log::error('OtpController::resend: échec envoi OTP', ['user_id' => $user->id, 'message' => $e->getMessage()]);
            return response()->json(['error' => __('auth.otp_send_failed')], 500);
        }

        return response()->json(['message' => __('auth.otp_resend_success'), 'expires_in' => 120]);
    }

    public function unblock(Request $request, string $token)
    {
        $user = User::where('unblock_token', $token)
                    ->where('is_blocked', true)
                    ->where('unblock_token_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return redirect('/login')->withErrors(['identifier' => __('auth.unblock_invalid')]);
        }

        $user->update([
            'is_blocked'               => false,
            'unblock_token'            => null,
            'unblock_token_expires_at' => null,
        ]);

        $loginUrl = $user->type === 'staff' ? route('staff.login') : '/login';

        return redirect($loginUrl)->with('unblock_success', __('auth.account_unblocked', [], $user->locale ?? 'fr'));
    }

    private function blockAccount(User $user, Request $request)
    {
        $token = Str::random(48);

        $user->update([
            'is_blocked'               => true,
            'unblock_token'            => $token,
            'unblock_token_expires_at' => now()->addSeconds(self::UNBLOCK_TTL),
        ]);

        Cache::forget('otp_' . $user->id);
        RateLimiter::clear('otp_attempts_' . $user->id);
        $loginUrl = $request->session()->get('otp_flow') === 'staff' ? route('staff.login') : '/login';
        $request->session()->forget(['otp_user_id', 'otp_remember', 'otp_flow']);

        try {
            Mail::to($user->email)->locale($user->locale ?: 'fr')->send(new AccountBlockedMail($user, $token));
        } catch (\Throwable $e) {
            Log::error('OtpController::blockAccount: échec envoi email de blocage', ['user_id' => $user->id, 'message' => $e->getMessage()]);
        }

        $msg = __('auth.account_blocked_notified', [], $user->locale ?? 'fr');

        return $request->expectsJson()
            ? response()->json(['status' => 'blocked', 'url' => $loginUrl, 'message' => $msg])
            : redirect($loginUrl)->withErrors(['identifier' => $msg]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visible = min(3, strlen($local));
        return substr($local, 0, $visible) . str_repeat('*', max(0, strlen($local) - $visible)) . '@' . $domain;
    }
}
