<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'address' => [
                'id' => $this->address->id ?? null,
                'zip_code' => $this->address->zip_code ?? null,
                'address_number' => $this->address->address_number ?? null,
                'country' => $this->address->country ?? null,
                'state' => $this->address->state ?? null,
                'street_address' => $this->address->street_address ?? null,
                'city' => $this->address->city ?? null,
                'address_line' => $this->address->address_line ?? null,
                'neighborhood' => $this->address->neighborhood ?? null,
            ],
            'phones' => $this->phones->map(function ($phone) {
                return [
                    'id' => $phone->id,
                    'phone' => $phone->phone,
                ];
            }),
            'emails' => $this->emails->map(function ($email) {
                return [
                    'id' => $email->id,
                    'email' => $email->email,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 