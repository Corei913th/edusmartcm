<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\StatutAbsence;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use App\Http\Requests\UpdateAbsenceStatutRequest;
use App\Http\Resources\AbsenceResource;
use App\Models\Absence;
use App\Services\AbsenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contrôleur API pour la gestion des absences
 *
 * @group Absences
 *
 * @authenticated
 *
 * Gère les opérations CRUD sur les absences des élèves.
 * Toute la logique métier est déléguée au AbsenceService.
 */
class AbsenceController extends Controller {
    /**
     * Service de gestion des absences
     */
    private AbsenceService $absenceService;

    /**
     * Constructeur du contrôleur
     *
     * @param AbsenceService $absenceService Service de gestion des absences
     */
    public function __construct(AbsenceService $absenceService) {
        $this->absenceService = $absenceService;
    }

    /**
     * Liste paginée des absences avec filtres optionnels
     *
     * @param Request $request Requête HTTP avec filtres optionnels
     */
    public function index(Request $request): JsonResponse {
        $filters = $request->only([
            'inscription_id',
            'affectation_id',
            'statut',
            'date_absence_from',
            'date_absence_to',
        ]);

        $perPage = (int) $request->get('per_page', 15);

        $absences = $this->absenceService->list($filters, $perPage);

        return api_paginated($absences, 'Liste des absences récupérée avec succès', AbsenceResource::class);
    }

    /**
     * Crée une nouvelle absence
     *
     * @param StoreAbsenceRequest $request Requête validée pour la création
     */
    public function store(StoreAbsenceRequest $request): JsonResponse {
        $createdBy = $request->user()->id;

        $absence = $this->absenceService->create($request->validated(), $createdBy);

        return api_created(new AbsenceResource($absence), 'Absence créée avec succès');
    }

    /**
     * Affiche une absence spécifique
     *
     * @param string $id ID de l'absence
     */
    public function show(string $id): JsonResponse {
        $absence = $this->absenceService->find($id);

        if (!$absence) {
            return api_not_found('Absence non trouvée');
        }

        return api_success(new AbsenceResource($absence), 'Absence récupérée avec succès');
    }

    /**
     * Met à jour une absence existante
     *
     * @param UpdateAbsenceRequest $request Requête validée pour la mise à jour
     * @param Absence              $absence Instance de l'absence à mettre à jour
     */
    public function update(UpdateAbsenceRequest $request, Absence $absence): JsonResponse {
        $updatedAbsence = $this->absenceService->update($absence, $request->validated());

        return api_updated(new AbsenceResource($updatedAbsence), 'Absence mise à jour avec succès');
    }

    /**
     * Met à jour le statut d'une absence
     *
     * @param UpdateAbsenceStatutRequest $request Requête validée pour la mise à jour du statut
     * @param Absence                    $absence Instance de l'absence à mettre à jour
     */
    public function updateStatut(UpdateAbsenceStatutRequest $request, Absence $absence): JsonResponse {
        $statut = StatutAbsence::from($request->validated('statut'));
        $justificatifPath = $request->validated('justificatif_path');

        $updatedAbsence = $this->absenceService->updateStatut($absence, $statut, $justificatifPath);

        return api_updated(new AbsenceResource($updatedAbsence), 'Statut de l\'absence mis à jour avec succès');
    }

    /**
     * Supprime une absence
     *
     * @param Absence $absence Instance de l'absence à supprimer
     */
    public function destroy(Absence $absence): JsonResponse {
        $this->absenceService->delete($absence);

        return api_deleted('Absence supprimée avec succès');
    }
}
