<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
     public function toArray($request)
    { 
        return [
            'id' => $this->id,
            'tenant' => $this->tenant_id,
            'property_id' => $this->property_id,
            'landlord_id' => $this->landlord_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rent_amount' => $this->rent_amount,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'notes' => $this->notes,
           
           
        ];
    }   
}

  