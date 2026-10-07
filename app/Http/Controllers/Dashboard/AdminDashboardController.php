<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $adminId = Auth::id();

        $clients = User::where('type', 'client')->where('created_by', $adminId);

        $stats = [
            'my_clients'      => (clone $clients)->count(),
            'verified'        => (clone $clients)->whereHas('kycVerification', fn ($q) => $q->where('status', 'approuve'))->count(),
            'pending_kyc'     => (clone $clients)->whereHas('kycVerification', fn ($q) => $q->where('status', 'en_attente'))->count(),
            'new_this_month'  => (clone $clients)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        $recentClients = (clone $clients)->latest()->take(6)->get();

        return view('dashboard.admin.index', compact('stats', 'recentClients'));
    }
}
