<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation de la requête de connexion.
 *
 * @property string $email
 * @property string $password
 */
class LoginRequest extends FormRequest {
    /**
     * Autoriser la requête.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Règles de validation.
     *
     * @return array<string, string>
     */
    public function rules(): array {
        return [
            'email' => 'required|email|max:200',
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * Messages de validation personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }
}
