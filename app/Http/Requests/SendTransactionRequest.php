<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount'            => ['required', 'integer'],
            'currency_id'       => ['required', 'exists:currencies,id'],
            'recipient'         => ['required', 'email', 'exists:users,email'],
            'type'              => ['optional', 'string'],
        ];
    }
}