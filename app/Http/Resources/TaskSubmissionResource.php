<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\RolesEnum;
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
            'task_id' => $this->task->id,
            'user' => $this->user->name,
            'file' => $this->file,
            'grade' => $this->when(
                auth()->user()->role->name !== RolesEnum::Delegate->value,
                $this->grade
            ),
            'status' => $this->status,
            'council_session' => $this->task->councilSession?->title,
            'council' => $this->task->councilSession?->council?->name,
            'council_id' => $this->task->councilSession?->council?->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
