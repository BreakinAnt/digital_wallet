<?php

namespace App\Resources;

use App\Repositories\UserTransactionRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    protected $transactionRepository;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->transactionRepository = new UserTransactionRepository();
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => new TransactionStatusResource($this->transactionRepository->getCurrentStatus($this->resource)),
            'currency' => $this->currency,
            'created_at' => $this->created_at,
            'confirmed_at' => $this->confirmed_at,
            'cancelled_at' => $this->cancelled_at,
            'user' => new UserResource($this->user),
            'target_user' => new UserResource($this->targetUser),
        ];
    }
}