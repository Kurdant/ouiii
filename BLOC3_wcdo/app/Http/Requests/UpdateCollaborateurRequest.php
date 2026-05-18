<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollaborateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $collaborateurId = $this->route('collaborateur')?->id;

        return [
            'nom'                    => ['required', 'string', 'max:100'],
            'prenom'                 => ['required', 'string', 'max:100'],
            'email'                  => ['required', 'email:filter', 'max:180', Rule::unique('collaborateurs', 'email')->ignore($collaborateurId)],
            'telephone'              => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +().\-]{6,20}$/'],
            'date_premiere_embauche' => ['required', 'date'],
            'administrateur'         => ['sometimes', 'boolean'],
            // En modification, password optionnel : vide = inchangé. Si fourni, min:8.
            // Si on passe administrateur=true et qu'aucun mot de passe n'est encore
            // enregistré, on exige password (vérification dans le contrôleur).
            'password'               => ['nullable', 'string', 'min:8', 'max:255'],
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
