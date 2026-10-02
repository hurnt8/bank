<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitKycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('client') ?? false;
    }

    public function rules(): array
    {
        $fileRules = 'file|mimes:jpg,jpeg,png,pdf|max:5120';

        return [
            'id_document_type'  => 'required|string|in:cni,passeport,permis',
            'id_document_front' => "required|{$fileRules}",
            'id_document_back'  => "nullable|{$fileRules}",
            'selfie'            => "required|{$fileRules}",
        ];
    }
}
