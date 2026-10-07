<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\KycStatusMail;
use App\Models\ClientNotification;
use App\Models\KycReview;
use App\Models\KycVerification;
use App\Models\User;
use App\Services\BankingProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $query = KycVerification::with('user:id,name,email');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', KycVerification::STATUS_NON_SOUMIS);
        }

        $verifications = $query->latest('submitted_at')->paginate(20)->appends($request->query());

        $stats = [
            'en_attente' => KycVerification::where('status', KycVerification::STATUS_EN_ATTENTE)->count(),
            'approuve'   => KycVerification::where('status', KycVerification::STATUS_APPROUVE)->count(),
            'rejete'     => KycVerification::where('status', KycVerification::STATUS_REJETE)->count(),
        ];

        return view('admin.kyc.index', compact('verifications', 'stats'));
    }

    public function show(KycVerification $kyc)
    {
        $kyc->load(['user', 'reviews.reviewer']);

        return view('admin.kyc.show', compact('kyc'));
    }

    /** Sert le document/selfie en streaming — jamais via une URL publique. */
    public function document(KycVerification $kyc, string $type)
    {
        $path = match ($type) {
            'front'  => $kyc->id_document_front_path,
            'back'   => $kyc->id_document_back_path,
            'selfie' => $kyc->selfie_path,
            default  => null,
        };

        abort_if(! $path || ! Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    public function approve(KycVerification $kyc)
    {
        abort_unless($kyc->status === KycVerification::STATUS_EN_ATTENTE, 422, 'Statut invalide.');

        DB::transaction(function () use ($kyc) {
            $kyc->update([
                'status'      => KycVerification::STATUS_APPROUVE,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'rejection_reason' => null,
            ]);

            KycReview::create([
                'kyc_verification_id' => $kyc->id,
                'reviewed_by'         => Auth::id(),
                'action'              => KycReview::ACTION_APPROVED,
            ]);
        });

        $this->notifyClient($kyc, 'approved');
        $generated = $this->provisionBanking($kyc->user);

        return back()->with('success', 'Identité approuvée.' . $generated);
    }

    /**
     * Valide le compte d'un client sans qu'il ait envoyé ses documents (aucune demande, demande
     * rejetée ou en attente) : la vérification passe à « approuvée » et les coordonnées
     * bancaires sont générées comme pour une approbation normale.
     */
    public function forceApprove(User $user)
    {
        abort_unless($user->hasRole('client'), 404);

        $kyc = $user->kycVerification;

        if (! $kyc || $kyc->status !== KycVerification::STATUS_APPROUVE) {
            DB::transaction(function () use ($user, &$kyc) {
                $kyc = $user->kycVerification()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'status'           => KycVerification::STATUS_APPROUVE,
                        'submitted_at'     => $user->kycVerification?->submitted_at ?? now(),
                        'reviewed_at'      => now(),
                        'reviewed_by'      => Auth::id(),
                        'rejection_reason' => null,
                    ]
                );

                KycReview::create([
                    'kyc_verification_id' => $kyc->id,
                    'reviewed_by'         => Auth::id(),
                    'action'              => KycReview::ACTION_APPROVED,
                    'reason'              => 'Validé par un administrateur sans documents.',
                ]);
            });

            $this->notifyClient($kyc->fresh(['user']), 'approved');
        }

        $generated = $this->provisionBanking($user);

        return back()->with('success', 'Compte validé.' . $generated);
    }

    /** Génère IBAN + carte manquants et prévient le client ; renvoie un court texte de résumé. */
    private function provisionBanking(User $client): string
    {
        $provisioner = app(BankingProvisioner::class);
        $created     = $provisioner->provision($client, Auth::id());

        if (! $created['bank']) {
            return '';
        }

        $provisioner->notifyAssigned($client);

        return ' IBAN généré automatiquement (la carte sera émise sur demande du client).';
    }

    public function reject(Request $request, KycVerification $kyc)
    {
        abort_unless($kyc->status === KycVerification::STATUS_EN_ATTENTE, 422, 'Statut invalide.');

        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($kyc, $data) {
            $kyc->update([
                'status'            => KycVerification::STATUS_REJETE,
                'reviewed_at'       => now(),
                'reviewed_by'       => Auth::id(),
                'rejection_reason'  => $data['reason'],
            ]);

            KycReview::create([
                'kyc_verification_id' => $kyc->id,
                'reviewed_by'         => Auth::id(),
                'action'              => KycReview::ACTION_REJECTED,
                'reason'              => $data['reason'],
            ]);
        });

        $this->notifyClient($kyc, 'rejected');

        return back()->with('success', 'Identité rejetée.');
    }

    private function notifyClient(KycVerification $kyc, string $action): void
    {
        $client = $kyc->user;

        ClientNotification::notifyUser(
            $client,
            'system',
            $action === 'approved' ? 'kyc.notif_approved' : 'kyc.notif_rejected',
            $action === 'approved' ? 'kyc.notif_approved_body' : 'kyc.notif_rejected_body',
            ['reason' => $kyc->rejection_reason ?? ''],
            ['kyc_id' => $kyc->id]
        );

        try {
            Mail::to($client->email)
                ->locale($client->locale ?? 'fr')
                ->send(new KycStatusMail($kyc, $action));
        } catch (\Throwable $e) {
            Log::error("KycStatusMail ({$action}) failed: " . $e->getMessage());
        }
    }
}
