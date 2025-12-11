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
            'admin_id' => $this->admin_id,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'notes' => $this->notes,
           
           
        ];
    }   
}

  