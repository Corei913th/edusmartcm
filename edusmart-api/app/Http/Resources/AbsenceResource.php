<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour le formatage des données d'absence dans les réponses API
 * 
 * Transforme les données du modèle Absence en format standardisé pour l'API.
 */
class AbsenceResource extends JsonResource
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
            'date_absence' => $this->date_absence->format('Y-m-d'),
            'heure_debut' => $this->heure_debut,
            'heure_fin' => $this->heure_fin,
            'duree_heures' => $this->duree_heures,
            'motif' => $this->motif,
            'statut' => $this->statut->value,
            'justificatif_path' => $this->justificatif_path,
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