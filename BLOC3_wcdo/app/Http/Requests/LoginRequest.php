<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation du formulaire de connexion.
 *
 * CDC : l'identifiant de connexion est l'email du collaborateur.
 * Le password est obligatoire et n'est jamais loggué.
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email:filter', 'max:180'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.email'       => 'Adresse email invalide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ];
    }
}
