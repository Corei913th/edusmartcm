<?php

namespace App\Http\Requests;

use App\Enums\TypeEvaluation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la création d'une note
 * 
 * Valide les données nécessaires à la création d'une nouvelle note.
 */
class StoreNoteRequest extends FormRequest
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
     * Règles de validation pour la création d'une note
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inscription_id' => ['required', 'uuid', 'exists:inscriptions,id'],
            'affectation_id' => ['required', 'uuid', 'exists:affectations_enseignement,id'],
            'periode_id' => ['required', 'uuid', 'exists:periodes,id'],
            'type_evaluation' => ['required', Rule::enum(TypeEvaluation::class)],
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'coefficient' => ['required', 'integer', 'min:1', 'max:10'],
            'date_evaluation' => ['required', 'date', 'date_format:Y-m-d'],
            'saisie_hors_ligne' => ['sometimes', 'boolean'],
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
            'inscription_id.required' => 'L\'inscription est obligatoire.',
            'inscription_id.uuid' => 'L\'identifiant de l\'inscription doit être un UUID valide.',
            'inscription_id.exists' => 'L\'inscription spécifiée n\'existe pas.',
            
            'affectation_id.required' => 'L\'affectation d\'enseignement est obligatoire.',
            'affectation_id.uuid' => 'L\'identifiant de l\'affectation doit être un UUID valide.',
            'affectation_id.exists' => 'L\'affectation d\'enseignement spécifiée n\'existe pas.',
            
            'periode_id.required' => 'La période est obligatoire.',
            'periode_id.uuid' => 'L\'identifiant de la période doit être un UUID valide.',
            'periode_id.exists' => 'La période spécifiée n\'existe pas.',
            
            'type_evaluation.required' => 'Le type d\'évaluation est obligatoire.',
            'type_evaluation.enum' => 'Le type d\'évaluation doit être : DEVOIR, COMPOSITION, ORAL, TP ou EXAMEN.',
            
            'note.required' => 'La note est obligatoire.',
            'note.numeric' => 'La note doit être un nombre.',
            'note.min' => 'La note ne peut pas être inférieure à 0.',
            'note.max' => 'La note ne peut pas être supérieure à 20.',
            
            'coefficient.required' => 'Le coefficient est obligatoire.',
            'coefficient.integer' => 'Le coefficient doit être un nombre entier.',
            'coefficient.min' => 'Le coefficient doit être au minimum de 1.',
            'coefficient.max' => 'Le coefficient ne peut pas dépasser 10.',
            
            'date_evaluation.required' => 'La date d\'évaluation est obligatoire.',
            'date_evaluation.date' => 'La date d\'évaluation doit être une date valide.',
            'date_evaluation.date_format' => 'La date d\'évaluation doit être au format YYYY-MM-DD.',
            
            'saisie_hors_ligne.boolean' => 'Le champ saisie hors ligne doit être vrai ou faux.',
        ];
    }
}