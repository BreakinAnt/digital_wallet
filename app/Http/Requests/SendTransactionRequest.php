<?php

namespace App\Http\Requests;

use App\Enums\TransactionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class SendTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $types = implode(',', TransactionTypeEnum::toArray());
        return [
            'amount'            => ['required', 'integer'],
            'recipient'         => ['required', 'email', 'exists:users,email'],
            'type'              => ['required', 'string', "in:$types"],
        ];
    }
}