<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour le formatage des données de note dans les réponses API
 * 
 * Transforme les données du modèle Note en format standardisé pour l'API.
 */
class NoteResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau
     *
     * @param Request $request Requête HTTP
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inscription_id' => $this->inscription_id,
            'affectation_id' => $this->affectation_id,
            'periode_id' => $this->periode_id,
            'type_evaluation' => $this->type_evaluation->value,
            'note' => (float) $this->note,
            'coefficient' => $this->coefficient,
            'date_evaluation' => $this->date_evaluation->format('Y-m-d'),
            'saisie_hors_ligne' => $this->saisie_hors_ligne,
            'sync_at' => $this->sync_at?->format('Y-m-d H:i:s'),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Relations chargées conditionnellement
            'inscription' => $this->whenLoaded('inscription', function () {
                return [
                    'id' => $this->inscription->id,
                    'eleve' => $this->whenLoaded('inscription.eleve', function () {
                        return [
                            'id' => $this->inscription->eleve->id,
                            'nom' => $this->inscription->eleve->nom,
                            'prenom' => $this->inscription->eleve->prenom,
                            'matricule' => $this->inscription->eleve->matricule,
                        ];
                    }),
                ];
            }),
            
            'affectation_enseignement' => $this->whenLoaded('affectationEnseignement', function () {
                return [
                    'id' => $this->affectationEnseignement->id,
                    'matiere' => $this->whenLoaded('affectationEnseignement.matiere', function () {
                        return [
                            'id' => $this->affectationEnseignement->matiere->id,
                            'nom' => $this->affectationEnseignement->matiere->nom,
                            'code' => $this->affectationEnseignement->matiere->code,
                        ];
                    }),
                ];
            }),
            
            'periode' => $this->whenLoaded('periode', function () {
                return [
                    'id' => $this->periode->id,
                    'nom' => $this->periode->nom,
                    'numero' => $this->periode->numero,
                ];
            }),
            
            'createur' => $this->whenLoaded('createur', function () {
                return [
                    'id' => $this->createur->id,
                    'nom' => $this->createur->nom,
                    'prenom' => $this->createur->prenom,
                ];
            }),
        ];
    }
}