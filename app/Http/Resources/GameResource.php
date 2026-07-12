<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'play_session_id' => $this->play_session_id,
            'map_id' => $this->map_id,
            'queue_type' => $this->queue_type,
            'result' => $this->result,
            'status' => $this->status,
            'role_played' => $this->role_played,
            'played_at' => $this->played_at,
            'duration_seconds' => $this->duration_seconds,
            'is_placement' => $this->is_placement,
            'data_source' => $this->data_source,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'heroes' => $this->whenLoaded('gameHeroes', fn () => $this->gameHeroes->map(fn ($gameHero) => [
                'hero_id' => $gameHero->hero_id,
                'name' => $gameHero->hero?->name,
                'is_primary' => $gameHero->is_primary,
                'playtime_seconds' => $gameHero->playtime_seconds,
            ])),
            'rounds' => GameRoundResource::collection($this->whenLoaded('gameRounds')),
            'rank_snapshots' => $this->whenLoaded('rankSnapshots', fn () => $this->rankSnapshots->map(fn ($snapshot) => [
                'id' => $snapshot->id,
                'role' => $snapshot->role,
                'tier' => $snapshot->tier,
                'division' => $snapshot->division,
                'rank_value' => $snapshot->rank_value,
                'progress_percent' => $snapshot->progress_percent,
            ])),
            'hero_srs' => $this->whenLoaded('heroSrSnapshots', fn () => $this->heroSrSnapshots->map(fn ($snapshot) => [
                'id' => $snapshot->id,
                'hero_id' => $snapshot->hero_id,
                'name' => $snapshot->hero?->name,
                'sr_value' => $snapshot->sr_value,
            ])),
            'map' => $this->whenLoaded('map'),
        ];
    }
}
