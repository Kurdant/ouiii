<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFonctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fonctionId = $this->route('fonction')?->id;

        return [
            'intitule_poste' => [
                'required',
                'string',
                'max:120',
                Rule::unique('fonctions', 'intitule_poste')->ignore($fonctionId),
            ],
        ];
    }

    public function attributes(): array
    {
        return ['intitule_poste' => 'intitulé du poste'];
    }
}
