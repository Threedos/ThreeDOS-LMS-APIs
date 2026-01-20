<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouncilResource extends JsonResource
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
            'head' => $this->whenLoaded('Head', function () {
    return $this->head?->name;
}),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
