<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    /**
     * Tables dans lesquelles une devise peut être référencée par des données existantes.
     */
    private const REFERENCING_TABLES = [
        'users', 'transfers', 'invoices', 'account_movements',
    ];

    public function index(Request $request)
    {
        $currencies = Currency::orderBy('sort_order')->paginate(20)->appends($request->query());

        return view('admin.currencies.index', compact('currencies'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'currencies'                    => 'required|array',
            'currencies.*.name'             => 'required|string|max:100',
            'currencies.*.symbol'           => 'required|string|max:10',
            'currencies.*.exchange_rate'    => 'required|numeric|min:0.000001',
            'currencies.*.sort_order'       => 'nullable|integer|min:0',
            'default_id'                    => 'required|exists:currencies,id',
        ]);

        $rows = $request->input('currencies', []);

        $activeCount = 0;
        foreach (array_keys($rows) as $id) {
            if ($request->boolean("currencies.$id.is_active")) {
                $activeCount++;
            }
        }

        if ($activeCount === 0) {
            return back()
                ->withErrors(['currencies' => 'Au moins une devise doit rester active.'])
                ->withInput();
        }

        if (! $request->boolean("currencies.{$request->input('default_id')}.is_active")) {
            return back()
                ->withErrors(['default_id' => 'La devise par défaut doit rester active.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $rows) {
            foreach ($rows as $id => $data) {
                Currency::whereKey($id)->update([
                    'name'          => $data['name'],
                    'symbol'        => $data['symbol'],
                    'exchange_rate' => $data['exchange_rate'],
                    'sort_order'    => $data['sort_order'] ?? 0,
                    'is_active'     => $request->boolean("currencies.$id.is_active"),
                ]);
            }

            Currency::query()->update(['is_default' => false]);
            Currency::whereKey($request->input('default_id'))->update(['is_default' => true]);
        });

        return redirect()->route('admin.currencies.index')
                         ->with('success', 'Devises mises à jour.');
    }

    public function store(Request $request)
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $validated = $request->validate([
            'code'          => 'required|regex:/^[A-Z]{3}$/|unique:currencies,code',
            'name'          => 'required|string|max:100',
            'symbol'        => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
        ], [
            'code.regex' => 'Le code doit contenir exactement 3 lettres (ex : USD, EUR).',
        ]);

        Currency::create([
            'code'          => $validated['code'],
            'name'          => $validated['name'],
            'symbol'        => $validated['symbol'],
            'exchange_rate' => $validated['exchange_rate'],
            'is_default'    => false,
            'is_active'     => true,
            'sort_order'    => (Currency::max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->route('admin.currencies.index')
                         ->with('success', 'Devise ajoutée.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->withErrors(['currency' => 'Impossible de supprimer la devise par défaut. Choisissez-en une autre au préalable.']);
        }

        foreach (self::REFERENCING_TABLES as $table) {
            if (DB::table($table)->where('currency', $currency->code)->exists()) {
                return back()->withErrors(['currency' => "Cette devise est utilisée par des données existantes ({$table}) et ne peut pas être supprimée."]);
            }
        }

        $currency->delete();

        return back()->with('success', 'Devise supprimée.');
    }
}
