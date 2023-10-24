<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'transactions_count' => $this['transactions_count'],
            'transactions' => TransactionResource::collection($this['transactions'])
        ];
    }
}
