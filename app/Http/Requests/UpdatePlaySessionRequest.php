<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaySessionRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $endedAt = $this->date('ended_at');

            if ($endedAt === null) {
                return;
            }

            $startedAt = $this->date('started_at') ?? $this->route('play_session')?->started_at;

            if ($startedAt !== null && $endedAt->lessThanOrEqualTo($startedAt)) {
                $validator->errors()->add('ended_at', 'The ended at must be after the started at.');
            }
        });
    }
}
