<?php

namespace App\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'created_at'    => (new DateTime($this->created_at))->format('Y-m-d H:i:s'),
        ];
    }
}