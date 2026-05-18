<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAffectationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'collaborateur_id' => ['required', 'integer', 'exists:collaborateurs,id'],
            'restaurant_id'    => ['required', 'integer', 'exists:restaurants,id'],
            'fonction_id'      => ['required', 'integer', 'exists:fonctions,id'],
            'date_debut'       => ['required', 'date'],
            'date_fin'         => ['nullable', 'date', 'after_or_equal:date_debut'],
        ];
    }

    public function attributes(): array
    {
        return [
            'collaborateur_id' => 'collaborateur',
            'restaurant_id'    => 'restaurant',
            'fonction_id'      => 'fonction',
            'date_debut'       => 'date de début',
            'date_fin'         => 'date de fin',
        ];
    }
}
