<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Services\KycDocumentService;
use App\Services\KycForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function __construct(private KycDocumentService $documents) {}

    public function show(Request $request)
    {
        $user  = Auth::user();
        $kyc   = $user->kycVerification;
        $steps = KycForm::steps();
        $total = count($steps);

        // Étape demandée, sinon la première qui n'est pas complète ; ?step=n permet de revenir modifier une étape.
        $requested = (int) $request->query('step');
        $step      = ($requested >= 1 && $requested <= $total) ? $requested : KycForm::firstIncompleteStep($user, $steps);

        return view('client.app.kyc.show', [
            'kyc'    => $kyc,
            'user'   => $user,
            'steps'  => $steps,
            'total'  => $total,
            'step'   => $step,
            'fields' => $steps[$step],
            'allFields' => collect($steps)->flatten(1),
        ]);
    }

    /** Enregistre l'étape envoyée ; à la dernière étape, la vérification est transmise pour examen. */
    public function saveStep(Request $request)
    {
        $user  = Auth::user();
        $steps = KycForm::steps();
        $total = count($steps);
        $step  = (int) $request->input('step', 1);
        abort_unless(isset($steps[$step]), 404);

        abort_if($user->kycVerification?->status === KycVerification::STATUS_EN_ATTENTE, 422, __('kyc.already_pending'));
        abort_if($user->kycVerification?->status === KycVerification::STATUS_APPROUVE, 422, __('kyc.already_approved'));

        $fields = $steps[$step];
        $request->validate(KycForm::rules($fields, $user), [], KycForm::attributes($fields));

        DB::transaction(function () use ($user, $fields, $request) {
            KycForm::save($user, $fields, $request, $this->documents);
        });

        if ($step < $total) {
            $savedFiles = $fields->contains(fn ($f) => $f->isFile());

            return redirect()->route('client.app.kyc.show', ['step' => $step + 1])
                ->with('success', __($savedFiles ? 'kyc.step_saved_docs' : 'onboarding.step1_saved'));
        }

        // Dernière étape : tout doit être complet avant de transmettre
        $user->unsetRelation('kycVerification');
        $all = collect($steps)->flatten(1);
        foreach ($steps as $n => $stepFields) {
            if (KycForm::missingRequired($user, $stepFields)) {
                return redirect()->route('client.app.kyc.show', ['step' => $n])->with('error', __('onboarding.complete_step1_first'));
            }
        }

        $user->kycVerification()->updateOrCreate(['user_id' => $user->id], [
            'status'           => KycVerification::STATUS_EN_ATTENTE,
            'id_document_type' => $user->id_type,
            'submitted_at'     => now(),
            'reviewed_at'      => null,
            'reviewed_by'      => null,
            'rejection_reason' => null,
        ]);

        return redirect()->route('client.app.kyc.show')->with('success', __(KycForm::hasFiles() ? 'kyc.submitted_success' : 'kyc.submitted_info'));
    }
}
