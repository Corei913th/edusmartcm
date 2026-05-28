<?php

namespace App\Http\Requests;

use App\Enums\StatutAbsence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la mise à jour du statut d'une absence
 * 
 * Valide les données pour la modification du statut et du justificatif d'une absence.
 */
class UpdateAbsenceStatutRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour du statut d'une absence
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'statut' => ['required', Rule::enum(StatutAbsence::class)],
            'justificatif_path' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'statut.required' => 'Le statut est obligatoire.',
            'statut.enum' => 'Le statut doit être : JUSTIFIEE, INJUSTIFIEE ou EN_ATTENTE.',
            
            'justificatif_path.string' => 'Le chemin du justificatif doit être une chaîne de caractères.',
            'justificatif_path.max' => 'Le chemin du justificatif ne peut pas dépasser 255 caractères.',
        ];
    }
}