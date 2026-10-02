<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitKycRequest;
use App\Models\KycVerification;
use App\Services\KycDocumentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function __construct(private KycDocumentService $documents) {}

    public function show()
    {
        $user = Auth::user();
        $kyc  = $user->kycVerification;

        return view('client.app.kyc.show', compact('kyc'));
    }

    public function store(SubmitKycRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

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
