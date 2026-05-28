<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour le formatage des données d'absence dans les réponses API
 *
 * Transforme les données du modèle Absence en format standardisé pour l'API.
 */
class AbsenceResource extends JsonResource {
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
            'date_absence' => $this->resource->date_absence->format('Y-m-d'),
            'heure_debut' => $this->resource->heure_debut,
            'heure_fin' => $this->resource->heure_fin,
            'duree_heures' => $this->resource->duree_heures,
            'motif' => $this->resource->motif,
            'statut' => $this->resource->statut->value,
            'justificatif_path' => $this->resource->justificatif_path,
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
