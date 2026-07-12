<?php

namespace App\Http\Requests;

use App\Enums\GameResult;
use App\Enums\RoundSide;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGameRoundRequest extends FormRequest
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
        $mapId = $this->route('round')?->game?->map_id;

        return [
            'map_submap_id' => [
                'sometimes', 'nullable', 'integer',
                Rule::exists('map_submaps', 'id')->where('map_id', $mapId),
            ],
            'result' => ['sometimes', 'nullable', Rule::enum(GameResult::class)],
            'side' => ['sometimes', 'nullable', Rule::enum(RoundSide::class)],
            'score_team' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'score_enemy' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'distance_meters' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'checkpoints_reached' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_overtime' => ['sometimes', 'boolean'],
            'heroes' => ['sometimes', 'nullable', 'array'],
            'heroes.*' => ['integer', 'distinct', 'exists:heroes,id'],
        ];
    }
}
