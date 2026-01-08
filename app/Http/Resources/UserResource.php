<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
   public function toArray(Request $request): array
    {
        return [
            'current_page' => $this->currentPage(),
            'data' => $this->collection->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'role' => $item->role?->name,
                'council' => $item->council?->name,
            ]),
            'first_page_url' => $this->url(1),
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'links' => $this->linkCollection(), // optional for page links
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }

    // Optional: format links like your example
    private function linkCollection()
    {
        return collect($this->links())->map(function ($link) {
            return [
                'url' => $link['url'],
                'label' => $link['label'],
                'active' => $link['active'],
                'page' => $link['active'] ? $this->currentPage() : null,
            ];
        });
    }
}
