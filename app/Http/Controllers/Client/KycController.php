<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitKycRequest;
use App\Models\KycVerification;
use App\Services\KycDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function __construct(private KycDocumentService $documents) {}

    /** Les champs de l'étape 1 sont considérés complets quand ces valeurs sont renseignées. */
    public static function personalInfoComplete($user): bool
    {
        return $user->birth_date && $user->country && $user->address && $user->id_type && $user->id_number;
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $kyc  = $user->kycVerification;

        $infoComplete = (bool) self::personalInfoComplete($user);
        // Étape 2 par défaut quand l'étape 1 est faite ; ?step=1 permet de modifier ses informations.
        $step = ($infoComplete && $request->query('step') !== '1') ? 2 : 1;

        return view('client.app.kyc.show', compact('kyc', 'user', 'step', 'infoComplete'));
    }

    public function saveInfo(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'birth_date'   => 'required|date|before:today',
            'country'      => 'required|string|max:100',
            'address'      => 'required|string|max:500',
            'id_type'      => 'required|string|in:cni,passeport,permis',
            'id_number'    => 'required|string|max:60',
            'date_delivre' => 'nullable|date|before_or_equal:today',
            'tax_number'   => 'nullable|string|max:60',
            'activity'     => 'nullable|string|max:255',
        ]);

        $user->update($data);

        return redirect()->route('client.app.kyc.show')->with('success', __('onboarding.step1_saved'));
    }

    public function store(SubmitKycRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if (! self::personalInfoComplete($user)) {
            return redirect()->route('client.app.kyc.show', ['step' => 1])
                ->with('error', __('onboarding.complete_step1_first'));
        }

        abort_if(
            $user->kycVerification && $user->kycVerification->status === KycVerification::STATUS_EN_ATTENTE,
            422,
            'Votre vérification est déjà en cours de traitement.'
        );
        abort_if(
            $user->kycVerification && $user->kycVerification->status === KycVerification::STATUS_APPROUVE,
            422,
            'Votre identité est déjà vérifiée.'
        );

        DB::transaction(function () use ($user, $data, $request) {
            // Si une précédente soumission rejetée existe, ses anciens fichiers sont remplacés.
            $old = $user->kycVerification;
            if ($old) {
                $this->documents->delete($old->id_document_front_path);
                $this->documents->delete($old->id_document_back_path);
                $this->documents->delete($old->selfie_path);
            }

            $frontPath = $this->documents->store($request->file('id_document_front'), $user->id, 'id_front');
            $backPath  = $request->hasFile('id_document_back')
                ? $this->documents->store($request->file('id_document_back'), $user->id, 'id_back')
                : null;
            $selfiePath = $this->documents->store($request->file('selfie'), $user->id, 'selfie');

            $user->kycVerification()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'status'                 => KycVerification::STATUS_EN_ATTENTE,
                    'id_document_type'       => $data['id_document_type'],
                    'id_document_front_path' => $frontPath,
                    'id_document_back_path'  => $backPath,
                    'selfie_path'            => $selfiePath,
                    'submitted_at'           => now(),
                    'reviewed_at'            => null,
                    'reviewed_by'            => null,
                    'rejection_reason'       => null,
                ]
            );
        });

        return redirect()->route('client.app.kyc.show')
            ->with('success', __('kyc.submitted_success'));
    }
}
