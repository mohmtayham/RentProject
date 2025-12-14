<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResourcev extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'user_type' => $this->user_type,
            'photo' => $this->photo,
            'id_photo' => $this->id_photo,
            'address' => $this->address,
            'status' => $this->status,
            
        ];
    }
}