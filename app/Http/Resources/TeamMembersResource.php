<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMembersResource extends JsonResource
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
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'rate' => $this->rate,
            'role' => $this->role,
            'task' => $this->task,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
