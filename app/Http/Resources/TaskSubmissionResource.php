<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskSubmissionResource extends JsonResource
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
            'task' => $this->task->title,
            'user' => $this->user->name,
            'file' => $this->file,
            'grade' => $this->grade,
            'status' => $this->status,
            'council_session' => $this->task->council_session->name,
            'council' => $this->task->council_session->council->name,
            'council_id' => $this->task->council_session->council->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
