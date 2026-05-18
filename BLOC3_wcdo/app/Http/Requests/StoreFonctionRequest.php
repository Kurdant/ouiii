<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFonctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'intitule_poste' => [
                'required',
                'string',
                'max:120',
                Rule::unique('fonctions', 'intitule_poste'),
            ],
        ];
    }

    public function attributes(): array
    {
        return ['intitule_poste' => 'intitulé du poste'];
    }
}
