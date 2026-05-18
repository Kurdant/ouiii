<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:150'],
            'adresse'     => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10', 'regex:/^[0-9A-Za-z\- ]{2,10}$/'],
            'ville'       => ['required', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nom'         => 'nom du restaurant',
            'adresse'     => 'adresse',
            'code_postal' => 'code postal',
            'ville'       => 'ville',
        ];
    }
}
