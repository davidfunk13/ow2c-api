<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameRoundResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'round_number' => $this->round_number,
            'map_submap_id' => $this->map_submap_id,
            'result' => $this->result,
            'side' => $this->side,
            'score_team' => $this->score_team,
            'score_enemy' => $this->score_enemy,
            'distance_meters' => $this->distance_meters,
            'checkpoints_reached' => $this->checkpoints_reached,
            'is_overtime' => $this->is_overtime,
            'submap' => $this->whenLoaded('mapSubmap'),
            'heroes' => $this->whenLoaded('roundHeroes', fn () => $this->roundHeroes->map(fn ($roundHero) => [
                'hero_id' => $roundHero->hero_id,
                'name' => $roundHero->hero?->name,
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
