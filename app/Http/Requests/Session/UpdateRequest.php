<?php

namespace App\Http\Requests\Session;

use App\Http\Requests\Api\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'id' => ['string', 'required'],
            // 'battletag_id' => ['string', 'required'],
            'name' => ['string', 'required'],
            'total_wins' => ['integer', 'required'],
            'wins' => ['integer', 'required'],
            'losses' => ['integer', 'required'],
            'draws' => ['integer', 'required'],
            'total_games' => ['integer', 'required'],
        ];
    }
}
