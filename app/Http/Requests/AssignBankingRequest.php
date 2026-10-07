<?php

namespace App\Http\Requests;

use App\Rules\ValidIban;
use Illuminate\Foundation\Http\FormRequest;

class AssignBankingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['admin', 'super-admin']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('bic')) {
            $this->merge(['bic' => strtoupper(preg_replace('/[[:space:]-]+/', '', (string) $this->input('bic'))) ?: null]);
        }
    }

    public function messages(): array
    {
        return ['bic.regex' => 'Le BIC doit contenir 8 ou 11 caractères (lettres et chiffres), ex. SOLBDEFF.'];
    }

    public function rules(): array
    {
        return [
            'iban'           => ['required', 'string', 'max:34', new ValidIban()],
            'bic'            => ['nullable', 'string', 'regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/'],
            'card_holder'    => 'required|string|max:255',
            'card_last_four' => 'required|digits:4',
            'card_network'   => 'required|string|in:visa,mastercard',
            'card_expires_at'=> 'required|date|after:today',
        ];
    }
}
