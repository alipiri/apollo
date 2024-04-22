<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'full_name'     => $this->fullName,
            'username'      => $this->username,
            'email'         => $this->email,
            'mobile_number' => $this->mobile_number,
            'status'        => ($this->is_active === 1) ? 'active' : 'not active',
            'company'       => CompanyResource::make($this->whenLoaded('company'))
        ];
    }
}
