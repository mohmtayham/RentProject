<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalContractResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'tenant_id' => $this->tenant_id,
            'landlord_id' => $this->landlord_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rent_amount' => $this->rent_amount,
            'rate' => $this->rate,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

   