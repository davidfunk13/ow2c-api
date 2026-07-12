<?php

namespace App\Http\Requests;

use App\Enums\RankTier;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncGameSnapshotsRequest extends FormRequest
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
            'ranks' => ['nullable', 'array'],
            'ranks.*.role' => ['required', 'distinct', Rule::enum(Role::class)],
            'ranks.*.tier' => ['required', Rule::enum(RankTier::class)],
            'ranks.*.division' => ['nullable', 'integer', 'between:1,5'],
            'ranks.*.progress_percent' => ['nullable', 'integer', 'between:0,100'],
            'hero_srs' => ['nullable', 'array'],
            'hero_srs.*.hero_id' => ['required', 'integer', 'distinct', 'exists:heroes,id'],
            'hero_srs.*.sr_value' => ['required', 'integer', 'min:0', 'max:7000'],
        ];
    }
}
