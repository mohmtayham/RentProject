<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    { 
        return [
            'id' => $this->id,
            'landlord_id' => $this->landlord_id,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'square_feet' => $this->square_feet,
            'is_available' => $this->is_available, // Fixed space issue
            'monthly_rent' => $this->monthly_rent,
            'description' => $this->description,
            'note' => $this->note,
            'photo' => $this->photo,
        ];
    }
}