<?php

namespace App\Http\Requests;

use App\Enums\GameResult;
use App\Enums\GameStatus;
use App\Enums\QueueType;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->status ?? GameStatus::Complete->value,
            'played_at' => $this->played_at ?? now()->toDateTimeString(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $completed = 'required_if:status,'.GameStatus::Complete->value;

        return [
            'play_session_id' => [
                'nullable', 'integer',
                Rule::exists('play_sessions', 'id')->where('user_id', $this->user()->id),
            ],
            'map_id' => ['nullable', 'integer', 'exists:maps,id'],
            'queue_type' => ['required', Rule::enum(QueueType::class)],
            'status' => ['required', Rule::enum(GameStatus::class)],
            'result' => ['nullable', Rule::enum(GameResult::class), $completed],
            'role_played' => ['nullable', Rule::enum(Role::class)],
            'played_at' => ['required', 'date'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'is_placement' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'heroes' => ['nullable', 'array', $completed],
            'heroes.*' => ['integer', 'distinct', 'exists:heroes,id'],
            'primary_hero_id' => ['nullable', 'integer', 'in_array:heroes.*'],
        ];
    }
}
