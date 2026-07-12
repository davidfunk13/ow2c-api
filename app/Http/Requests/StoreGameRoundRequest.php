<?php

namespace App\Http\Requests;

use App\Enums\GameResult;
use App\Enums\RoundSide;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGameRoundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $mapId = $this->route('game')?->map_id;

        return [
            'map_submap_id' => [
                'nullable', 'integer',
                Rule::exists('map_submaps', 'id')->where('map_id', $mapId),
            ],
            'result' => ['nullable', Rule::enum(GameResult::class)],
            'side' => ['nullable', Rule::enum(RoundSide::class)],
            'score_team' => ['nullable', 'integer', 'min:0'],
            'score_enemy' => ['nullable', 'integer', 'min:0'],
            'distance_meters' => ['nullable', 'numeric', 'min:0'],
            'checkpoints_reached' => ['nullable', 'integer', 'min:0'],
            'is_overtime' => ['boolean'],
            'heroes' => ['nullable', 'array'],
            'heroes.*' => ['integer', 'distinct', 'exists:heroes,id'],
        ];
    }
}
