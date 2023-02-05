<?php

namespace App\Http\Requests\Session;

use App\Http\Requests\Api\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['string', 'required', 'max:35']
        ];
    }
}
