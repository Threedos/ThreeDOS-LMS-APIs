<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AttendanceResource;
class SessionResource extends JsonResource
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
            'title' => $this->title,
            'date' => $this->date,
            'description' => $this->description,
            'material' => $this->material,
            'council' => $this->council->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'attendance' => AttendanceResource::collection($this->whenLoaded('attendance')),
        ];
    }
}
