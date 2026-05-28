<?php

namespace App\Services;

use App\Enums\StatutAbsence;
use App\Models\Absence;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Service de gestion des absences
 * 
 * Gère toute la logique métier liée aux absences des élèves.
 * Toutes les opérations de base de données sont encapsulées dans des transactions.
 */
class AbsenceService
{
    /**
     * Récupère une liste paginée d'absences avec filtres optionnels
     *
     * @param array $filters Filtres à appliquer (inscription_id, affectation_id, statut, date_absence_from, date_absence_to)
     * @param int $perPage Nombre d'éléments par page (défaut: 15)
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return runTransaction(function () use ($filters, $perPage) {
            $query = Absence::query()
                ->with(['inscription.eleve', 'affectationEnseignement.matiere', 'createur'])
                ->orderBy('date_absence', 'desc')
                ->orderBy('heure_debut', 'desc');

            $this->applyFilters($query, $filters);

            return $query->paginate($perPage);
        }, 'AbsenceService::list');
    }

    /**
     * Crée une nouvelle absence
     *
     * @param array $data Données de l'absence
     * @param string $createdBy ID de l'utilisateur créateur
     * @return Absence
     * @throws \Exception
     */
    public function create(array $data, string $createdBy): Absence
    {
        return runTransaction(function () use ($data, $createdBy) {
            $data['created_by'] = $createdBy;
            
            return Absence::create($data);
        }, 'AbsenceService::create');
    }

    /**
     * Trouve une absence par son ID
     *
     * @param string $id ID de l'absence
     * @return Absence|null
     */
    public function find(string $id): ?Absence
    {
        return runTransaction(function () use ($id) {
            return Absence::with(['inscription.eleve', 'affectationEnseignement.matiere', 'createur'])
                ->find($id);
        }, 'AbsenceService::find');
    }

    /**
     * Met à jour une absence existante
     *
     * @param Absence $absence Instance de l'absence à mettre à jour
     * @param array $data Nouvelles données
     * @return Absence
     * @throws \Exception
     */
    public function update(Absence $absence, array $data): Absence
    {
        return runTransaction(function () use ($absence, $data) {
            $absence->update($data);
            
            return $absence->fresh(['inscription.eleve', 'affectationEnseignement.matiere', 'createur']);
        }, 'AbsenceService::update');
    }

    /**
     * Met à jour le statut d'une absence et optionnellement le justificatif
     *
     * @param Absence $absence Instance de l'absence à mettre à jour
     * @param StatutAbsence $statut Nouveau statut
     * @param string|null $justificatifPath Chemin vers le justificatif (optionnel)
     * @return Absence
     * @throws \Exception
     */
    public function updateStatut(Absence $absence, StatutAbsence $statut, ?string $justificatifPath = null): Absence
    {
        return runTransaction(function () use ($absence, $statut, $justificatifPath) {
            $updateData = ['statut' => $statut];
            
            if ($justificatifPath !== null) {
                $updateData['justificatif_path'] = $justificatifPath;
            }
            
            $absence->update($updateData);
            
            return $absence->fresh(['inscription.eleve', 'affectationEnseignement.matiere', 'createur']);
        }, 'AbsenceService::updateStatut');
    }

    /**
     * Supprime une absence
     *
     * @param Absence $absence Instance de l'absence à supprimer
     * @return bool
     * @throws \Exception
     */
    public function delete(Absence $absence): bool
    {
        return runTransaction(function () use ($absence) {
            return $absence->delete();
        }, 'AbsenceService::delete');
    }

    /**
     * Applique les filtres à la requête
     *
     * @param Builder $query Requête Eloquent
     * @param array $filters Filtres à appliquer
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['inscription_id'])) {
            $query->where('inscription_id', $filters['inscription_id']);
        }

        if (!empty($filters['affectation_id'])) {
            $query->where('affectation_id', $filters['affectation_id']);
        }

        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        if (!empty($filters['date_absence_from'])) {
            $query->where('date_absence', '>=', $filters['date_absence_from']);
        }

        if (!empty($filters['date_absence_to'])) {
            $query->where('date_absence', '<=', $filters['date_absence_to']);
        }
    }
}