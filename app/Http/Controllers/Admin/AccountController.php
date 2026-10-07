<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountMovement;
use App\Models\ClientNotification;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $auth    = Auth::user();
        $isSuperAdmin = $auth->hasRole('super-admin');

        $query = User::where('type', 'client')->with('bankAccount');

        if (! $isSuperAdmin) {
            $adminId = $auth->id;
            $query->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId);
            });
        }

        // Recherche et tri passent cote serveur : ils etaient faits en JavaScript sur
        // les lignes presentes dans le DOM, ce qui ne couvrirait plus que la page
        // courante une fois la pagination en place.
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like);
            });
        }

        // Liste blanche : $sort part de l URL et finit dans un ORDER BY.
        $sort = $request->query('sort') === 'balance' ? 'balance' : 'name';
        $dir  = $request->query('dir')  === 'desc'    ? 'desc'    : 'asc';

        // Les cartes de synthese doivent porter sur l ENSEMBLE des comptes, pas sur la
        // page affichee : on agrege donc avant de paginer, sinon le solde total et les
        // compteurs ne refleteraient que les 20 lignes courantes.
        $totalAccounts = (clone $query)->count();
        $totalBalance  = (clone $query)->sum('balance');
        $positiveCount = (clone $query)->where('balance', '>', 0)->count();
        $negativeCount = (clone $query)->where('balance', '<', 0)->count();

        // Etait un ->get() : la vue chargeait la totalite des clients, sans pagination
        // ni limite. appends() preserve les filtres de recherche entre les pages.
        $clients = $query->orderBy($sort, $dir)->paginate(20)->appends($request->query());

        return view('admin.accounts.index', compact(
            'clients', 'isSuperAdmin', 'totalAccounts', 'totalBalance', 'positiveCount', 'negativeCount', 'search', 'sort', 'dir'
        ));
    }

    public function show(User $account)
    {
        $this->authorizeAccount($account);

        $movements = AccountMovement::where('user_id', $account->id)
            ->with('admin:id,name')
            ->latest()
            ->paginate(20);

        return view('admin.accounts.show', compact('account', 'movements'));
    }

    public function credit(Request $request, User $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:9999999',
            'note'   => 'nullable|string|max:255',
        ]);

        $cur = $account->currency ?? Currency::default();

        DB::transaction(function () use ($account, $validated, $cur) {
            $before = (float) $account->balance;
            $account->increment('balance', $validated['amount']);

            AccountMovement::create([
                'user_id'        => $account->id,
                'admin_id'       => Auth::id(),
                'type'           => 'credit',
                'amount'         => $validated['amount'],
                'currency'       => $cur,
                'balance_before' => $before,
                'balance_after'  => $before + $validated['amount'],
                'note'           => $validated['note'] ?? null,
            ]);

            $locale = $account->locale ?? 'fr';
            $body   = __('app.notif_account_credited_body', ['amount' => number_format($validated['amount'], 2, ',', ' '), 'currency' => $cur], $locale);
            if ($validated['note']) $body .= ' — ' . $validated['note'];
            ClientNotification::forUser(
                $account->id,
                'system',
                __('app.notif_account_credited', [], $locale),
                $body,
                ['amount' => $validated['amount'], 'currency' => $cur]
            );
        });

        return back()->with('success', 'Compte crédité de ' . number_format($validated['amount'], 2, ',', ' ') . ' ' . $cur . '.');
    }

    public function debit(Request $request, User $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:9999999',
            'note'   => 'nullable|string|max:255',
        ]);

        $cur    = $account->currency ?? Currency::default();
        $before = (float) $account->balance;

        DB::transaction(function () use ($account, $validated, $before, $cur) {
            $account->decrement('balance', $validated['amount']);

            AccountMovement::create([
                'user_id'        => $account->id,
                'admin_id'       => Auth::id(),
                'type'           => 'debit',
                'amount'         => $validated['amount'],
                'currency'       => $cur,
                'balance_before' => $before,
                'balance_after'  => $before - $validated['amount'],
                'note'           => $validated['note'] ?? null,
            ]);

            $locale = $account->locale ?? 'fr';
            $body   = __('app.notif_account_debited_body', ['amount' => number_format($validated['amount'], 2, ',', ' '), 'currency' => $cur], $locale);
            if ($validated['note']) $body .= ' — ' . $validated['note'];
            ClientNotification::forUser(
                $account->id,
                'system',
                __('app.notif_account_debited', [], $locale),
                $body,
                ['amount' => $validated['amount'], 'currency' => $cur]
            );
        });

        return back()->with('success', 'Compte débité de ' . number_format($validated['amount'], 2, ',', ' ') . ' ' . ($account->currency ?? Currency::default()) . '.');
    }

    private function authorizeAccount(User $client): void
    {
        $auth = Auth::user();
        if ($auth->hasRole('super-admin')) return;

        $adminId   = $auth->id;
        $isManaged = $client->created_by === $adminId;

        abort_unless($isManaged, 403, 'Accès non autorisé à ce compte.');
    }
}
