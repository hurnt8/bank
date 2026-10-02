<?php

namespace App\Http\Middleware;

use App\Models\KycVerification;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $kyc  = $user?->kycVerification;

        if (! $kyc || $kyc->status !== KycVerification::STATUS_APPROUVE) {
            return redirect()->route('client.app.kyc.show')
                ->with('error', __('kyc.required_notice'));
        }

        return $next($request);
    }
}
