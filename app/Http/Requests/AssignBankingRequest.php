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

    public function rules(): array
    {
        return [
            'iban'           => ['required', 'string', 'max:34', new ValidIban()],
            'bic'            => 'nullable|string|max:11',
            'card_holder'    => 'required|string|max:255',
            'card_last_four' => 'required|digits:4',
            'card_network'   => 'required|string|in:visa,mastercard',
            'card_expires_at'=> 'required|date|after:today',
        ];
    }
}
