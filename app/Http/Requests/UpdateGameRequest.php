<?php

namespace App\Http\Requests;

use App\Enums\GameResult;
use App\Enums\GameStatus;
use App\Enums\QueueType;
use App\Enums\Role;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGameRequest extends FormRequest
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
        return [
            'play_session_id' => [
                'sometimes', 'nullable', 'integer',
                Rule::exists('play_sessions', 'id')->where('user_id', $this->user()->id),
            ],
            'map_id' => ['sometimes', 'nullable', 'integer', 'exists:maps,id'],
            'queue_type' => ['sometimes', Rule::enum(QueueType::class)],
            'status' => ['sometimes', Rule::enum(GameStatus::class)],
            'result' => ['sometimes', 'nullable', Rule::enum(GameResult::class)],
            'role_played' => ['sometimes', 'nullable', Rule::enum(Role::class)],
            'played_at' => ['sometimes', 'date'],
            'duration_seconds' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_placement' => ['sometimes', 'boolean'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'heroes' => ['sometimes', 'nullable', 'array'],
            'heroes.*' => ['integer', 'distinct', 'exists:heroes,id'],
            'primary_hero_id' => ['sometimes', 'nullable', 'integer', 'in_array:heroes.*'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $game = $this->route('game');
            $status = $this->input('status', $game?->status?->value);

            if ($status !== GameStatus::Complete->value) {
                return;
            }

            $result = $this->has('result') ? $this->input('result') : $game?->result?->value;
            if ($result === null) {
                $validator->errors()->add('result', 'A completed game must have a result.');
            }

            if ($this->has('heroes') && count($this->input('heroes') ?? []) === 0) {
                $validator->errors()->add('heroes', 'A completed game must have at least one hero.');
            }
        });
    }
}
