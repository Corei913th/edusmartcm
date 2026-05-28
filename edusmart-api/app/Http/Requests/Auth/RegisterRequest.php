<?php

namespace App\Http\Requests\Auth;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la requête d'inscription.
 *
 * @property string      $nom
 * @property string      $prenom
 * @property string      $email
 * @property string      $password
 * @property string|null $telephone
 * @property string|null $role_code
 */
class RegisterRequest extends FormRequest {
    /**
     * Autoriser la requête.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Règles de validation.
     *
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|max:200|unique:utilisateurs,email',
            'password' => 'required|string|min:8|max:100',
            'telephone' => 'nullable|string|max:20',
            'role_code' => ['nullable', 'string', Rule::in(array_map(fn (Role $r) => $r->value, Role::cases()))],
        ];
    }

    /**
     * Messages de validation personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }
}
