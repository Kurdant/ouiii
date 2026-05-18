<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCollaborateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'                    => ['required', 'string', 'max:100'],
            'prenom'                 => ['required', 'string', 'max:100'],
            'email'                  => ['required', 'email:filter', 'max:180', Rule::unique('collaborateurs', 'email')],
            'telephone'              => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +().\-]{6,20}$/'],
            'date_premiere_embauche' => ['required', 'date'],
            'administrateur'         => ['sometimes', 'boolean'],
            'password'               => ['nullable', 'string', 'min:8', 'max:255', 'required_if:administrateur,1'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'administrateur' => $this->boolean('administrateur'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'nom'                    => 'nom',
            'prenom'                 => 'prénom',
            'email'                  => 'email',
            'telephone'              => 'téléphone',
            'date_premiere_embauche' => 'date de première embauche',
            'administrateur'         => 'administrateur',
            'password'               => 'mot de passe',
        ];
    }
}
