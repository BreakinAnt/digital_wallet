<?php

namespace App\Resources;

use App\Repositories\UserTransactionRepository;
use DateTime;
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
            'id'            => $this->id,
            'amount'        => $this->amount / 100,
            'type'          => $this->type,
            'status'        => new TransactionStatusResource($this->transactionRepository->getCurrentStatus($this->resource)),
            'currency'      => new CurrencyResource($this->currency),
            'created_at'    => isset($this->created_at) ? (new DateTime($this->created_at))->format('Y-m-d H:i:s') : null,
            'completed_at'  => isset($this->completed_at) ? (new DateTime($this->completed_at))->format('Y-m-d H:i:s') : null,
            'cancelled_at'  => isset($this->cancelled_at) ? (new DateTime($this->cancelled_at))->format('Y-m-d H:i:s') : null,
            'user'          => new UserResource($this->user),
            'target_user'   => new UserResource($this->targetUser),
        ];
    }
}