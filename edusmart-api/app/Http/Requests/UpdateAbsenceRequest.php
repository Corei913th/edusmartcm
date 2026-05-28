<?php

namespace App\Http\Requests;

use App\Enums\StatutAbsence;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la mise à jour d'une absence
 *
 * Valide les données pour la modification d'une absence existante.
 * Tous les champs sont optionnels (sometimes) pour permettre les mises à jour partielles.
 */
class UpdateAbsenceRequest extends FormRequest {
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'une absence
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array {
        return [
            'inscription_id' => ['sometimes', 'uuid', 'exists:inscriptions,id'],
            'affectation_id' => ['sometimes', 'uuid', 'exists:affectations_enseignement,id'],
            'date_absence' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'heure_debut' => ['sometimes', 'date_format:H:i'],
            'heure_fin' => ['sometimes', 'date_format:H:i', 'after:heure_debut'],
            'duree_heures' => ['sometimes', 'integer', 'min:1', 'max:24'],
            'motif' => ['sometimes', 'string', 'max:500'],
            'statut' => ['sometimes', Rule::enum(StatutAbsence::class)],
            'justificatif_path' => ['sometimes', 'string', 'max:255'],
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

            'date_absence.date' => 'La date d\'absence doit être une date valide.',
            'date_absence.date_format' => 'La date d\'absence doit être au format YYYY-MM-DD.',

            'heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM.',

            'heure_fin.date_format' => 'L\'heure de fin doit être au format HH:MM.',
            'heure_fin.after' => 'L\'heure de fin doit être postérieure à l\'heure de début.',

            'duree_heures.integer' => 'La durée doit être un nombre entier.',
            'duree_heures.min' => 'La durée doit être d\'au moins 1 heure.',
            'duree_heures.max' => 'La durée ne peut pas dépasser 24 heures.',

            'motif.string' => 'Le motif doit être une chaîne de caractères.',
            'motif.max' => 'Le motif ne peut pas dépasser 500 caractères.',

            'statut.enum' => 'Le statut doit être : JUSTIFIEE, INJUSTIFIEE ou EN_ATTENTE.',

            'justificatif_path.string' => 'Le chemin du justificatif doit être une chaîne de caractères.',
            'justificatif_path.max' => 'Le chemin du justificatif ne peut pas dépasser 255 caractères.',

            'saisie_hors_ligne.boolean' => 'Le champ saisie hors ligne doit être vrai ou faux.',
        ];
    }
}
