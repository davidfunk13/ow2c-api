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
            'id' => ['integer', 'required']
        ];
    }
}
