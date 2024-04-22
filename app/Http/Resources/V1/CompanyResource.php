<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'admin'        => $this->admin,
            'email'        => $this->email,
            'phone_number' => $this->phone_number,
            'certificate'  => $this->certificate,
            'contacts'     => ContactResource::collection($this->whenLoaded('contacts'))
        ];
    }
}
