<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\TeamMemberResource;
class TeamMemberCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => TeamMemberResource::collection($this->collection),
            // 'links' => [
            //     'self' => $this->resource->getLink('self'),
            // ],
        ];
    }
}
