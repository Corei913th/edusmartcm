<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour le formatage des données de note dans les réponses API
 *
 * Transforme les données du modèle Note en format standardisé pour l'API.
 */
class NoteResource extends JsonResource {
    /**
     * Transforme la ressource en tableau
     *
     * @param Request $request Requête HTTP
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->resource->id,
            'inscription_id' => $this->resource->inscription_id,
            'affectation_id' => $this->resource->affectation_id,
            'periode_id' => $this->resource->periode_id,
            'type_evaluation' => $this->resource->type_evaluation->value,
            'note' => (float) $this->resource->note,
            'coefficient' => $this->resource->coefficient,
            'date_evaluation' => $this->resource->date_evaluation->format('Y-m-d'),
            'saisie_hors_ligne' => $this->resource->saisie_hors_ligne,
            'sync_at' => $this->resource->sync_at?->format('Y-m-d H:i:s'),
            'created_by' => $this->resource->created_by,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at->format('Y-m-d H:i:s'),

            // Relations chargées conditionnellement
            'inscription' => $this->whenLoaded('inscription', function () {
                return [
                    'id' => $this->resource->inscription->id,
                    'eleve' => $this->whenLoaded('inscription.eleve', function () {
                        return [
                            'id' => $this->resource->inscription->eleve->id,
                            'nom' => $this->resource->inscription->eleve->nom,
                            'prenom' => $this->resource->inscription->eleve->prenom,
                            'matricule' => $this->resource->inscription->eleve->matricule,
                        ];
                    }),
                ];
            }),

            'affectation_enseignement' => $this->whenLoaded('affectationEnseignement', function () {
                return [
                    'id' => $this->resource->affectationEnseignement->id,
                    'matiere' => $this->whenLoaded('affectationEnseignement.matiere', function () {
                        return [
                            'id' => $this->resource->affectationEnseignement->matiere->id,
                            'nom' => $this->resource->affectationEnseignement->matiere->nom,
                            'code' => $this->resource->affectationEnseignement->matiere->code,
                        ];
                    }),
                ];
            }),

            'periode' => $this->whenLoaded('periode', function () {
                return [
                    'id' => $this->resource->periode->id,
                    'nom' => $this->resource->periode->nom,
                    'numero' => $this->resource->periode->numero,
                ];
            }),

            'createur' => $this->whenLoaded('createur', function () {
                return [
                    'id' => $this->resource->createur->id,
                    'nom' => $this->resource->createur->nom,
                    'prenom' => $this->resource->createur->prenom,
                ];
            }),
        ];
    }
}
