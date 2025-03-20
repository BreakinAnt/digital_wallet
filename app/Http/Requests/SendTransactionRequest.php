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
            'recipient'         => ['required', 'email', 'exists:users,email'],
            'type'              => ['optional', 'string'],
        ];
    }
}