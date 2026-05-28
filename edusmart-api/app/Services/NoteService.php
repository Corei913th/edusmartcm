<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Service de gestion des notes
 * 
 * Gère toute la logique métier liée aux notes des élèves.
 * Toutes les opérations de base de données sont encapsulées dans des transactions.
 */
class NoteService
{
    /**
     * Récupère une liste paginée de notes avec filtres optionnels
     *
     * @param array $filters Filtres à appliquer (inscription_id, affectation_id, periode_id, type_evaluation)
     * @param int $perPage Nombre d'éléments par page (défaut: 15)
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return runTransaction(function () use ($filters, $perPage) {
            $query = Note::query()
                ->with(['inscription.eleve', 'affectationEnseignement.matiere', 'periode', 'createur'])
                ->orderBy('created_at', 'desc');

            $this->applyFilters($query, $filters);

            return $query->paginate($perPage);
        }, 'NoteService::list');
    }

    /**
     * Crée une nouvelle note
     *
     * @param array $data Données de la note
     * @param string $createdBy ID de l'utilisateur créateur
     * @return Note
     * @throws \Exception
     */
    public function create(array $data, string $createdBy): Note
    {
        return runTransaction(function () use ($data, $createdBy) {
            $data['created_by'] = $createdBy;
            
            return Note::create($data);
        }, 'NoteService::create');
    }

    /**
     * Trouve une note par son ID
     *
     * @param string $id ID de la note
     * @return Note|null
     */
    public function find(string $id): ?Note
    {
        return runTransaction(function () use ($id) {
            return Note::with(['inscription.eleve', 'affectationEnseignement.matiere', 'periode', 'createur'])
                ->find($id);
        }, 'NoteService::find');
    }

    /**
     * Met à jour une note existante
     *
     * @param Note $note Instance de la note à mettre à jour
     * @param array $data Nouvelles données
     * @return Note
     * @throws \Exception
     */
    public function update(Note $note, array $data): Note
    {
        return runTransaction(function () use ($note, $data) {
            $note->update($data);
            
            return $note->fresh(['inscription.eleve', 'affectationEnseignement.matiere', 'periode', 'createur']);
        }, 'NoteService::update');
    }

    /**
     * Supprime une note
     *
     * @param Note $note Instance de la note à supprimer
     * @return bool
     * @throws \Exception
     */
    public function delete(Note $note): bool
    {
        return runTransaction(function () use ($note) {
            return $note->delete();
        }, 'NoteService::delete');
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

        if (!empty($filters['periode_id'])) {
            $query->where('periode_id', $filters['periode_id']);
        }

        if (!empty($filters['type_evaluation'])) {
            $query->where('type_evaluation', $filters['type_evaluation']);
        }

        if (!empty($filters['date_evaluation_from'])) {
            $query->where('date_evaluation', '>=', $filters['date_evaluation_from']);
        }

        if (!empty($filters['date_evaluation_to'])) {
            $query->where('date_evaluation', '<=', $filters['date_evaluation_to']);
        }
    }
}