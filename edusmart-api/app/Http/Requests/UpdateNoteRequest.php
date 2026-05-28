<?php

namespace App\Http\Requests;

use App\Enums\TypeEvaluation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la mise à jour d'une note
 *
 * Valide les données pour la modification d'une note existante.
 * Tous les champs sont optionnels (sometimes) pour permettre les mises à jour partielles.
 */
class UpdateNoteRequest extends FormRequest {
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'une note
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array {
        return [
            'inscription_id' => ['sometimes', 'uuid', 'exists:inscriptions,id'],
            'affectation_id' => ['sometimes', 'uuid', 'exists:affectations_enseignement,id'],
            'periode_id' => ['sometimes', 'uuid', 'exists:periodes,id'],
            'type_evaluation' => ['sometimes', Rule::enum(TypeEvaluation::class)],
            'note' => ['sometimes', 'numeric', 'min:0', 'max:20'],
            'coefficient' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'date_evaluation' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'saisie_hors_ligne' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'inscription_id.uuid' => 'L\'identifiant de l\'inscription doit être un UUID valide.',
            'inscription_id.exists' => 'L\'inscription spécifiée n\'existe pas.',

            'affectation_id.uuid' => 'L\'identifiant de l\'affectation doit être un UUID valide.',
            'affectation_id.exists' => 'L\'affectation d\'enseignement spécifiée n\'existe pas.',

            'periode_id.uuid' => 'L\'identifiant de la période doit être un UUID valide.',
            'periode_id.exists' => 'La période spécifiée n\'existe pas.',

            'type_evaluation.enum' => 'Le type d\'évaluation doit être : DEVOIR, COMPOSITION, ORAL, TP ou EXAMEN.',

            'note.numeric' => 'La note doit être un nombre.',
            'note.min' => 'La note ne peut pas être inférieure à 0.',
            'note.max' => 'La note ne peut pas être supérieure à 20.',

            'coefficient.integer' => 'Le coefficient doit être un nombre entier.',
            'coefficient.min' => 'Le coefficient doit être au minimum de 1.',
            'coefficient.max' => 'Le coefficient ne peut pas dépasser 10.',

            'date_evaluation.date' => 'La date d\'évaluation doit être une date valide.',
            'date_evaluation.date_format' => 'La date d\'évaluation doit être au format YYYY-MM-DD.',

            'saisie_hors_ligne.boolean' => 'Le champ saisie hors ligne doit être vrai ou faux.',
        ];
    }
}
