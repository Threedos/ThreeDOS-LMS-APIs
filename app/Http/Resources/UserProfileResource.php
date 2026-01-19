<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->name,
            'council' => $this->council->name,
            'status' => $this->status,
            'last_active' => $this->last_active,
        ];
    }
}
