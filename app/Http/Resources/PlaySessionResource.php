<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaySessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'notes' => $this->notes,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'games_count' => $this->whenCounted('games'),
            'games' => GameResource::collection($this->whenLoaded('games')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
