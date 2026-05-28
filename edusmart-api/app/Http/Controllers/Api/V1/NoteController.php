<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contrôleur API pour la gestion des notes
 * 
 * @group Notes
 * @authenticated
 * 
 * Gère les opérations CRUD sur les notes des élèves.
 * Toute la logique métier est déléguée au NoteService.
 */
class NoteController extends Controller
{
    /**
     * Service de gestion des notes
     *
     * @var NoteService
     */
    private NoteService $noteService;

    /**
     * Constructeur du contrôleur
     *
     * @param NoteService $noteService Service de gestion des notes
     */
    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * Liste paginée des notes avec filtres optionnels
     *
     * @param Request $request Requête HTTP avec filtres optionnels
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'inscription_id',
            'affectation_id', 
            'periode_id',
            'type_evaluation',
            'date_evaluation_from',
            'date_evaluation_to'
        ]);

        $perPage = (int) $request->get('per_page', 15);
        
        $notes = $this->noteService->list($filters, $perPage);
        
        return api_paginated($notes, 'Liste des notes récupérée avec succès', NoteResource::class);
    }

    /**
     * Crée une nouvelle note
     *
     * @param StoreNoteRequest $request Requête validée pour la création
     * @return JsonResponse
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $createdBy = $request->user()->id;
        
        $note = $this->noteService->create($request->validated(), $createdBy);
        
        return api_created(new NoteResource($note), 'Note créée avec succès');
    }

    /**
     * Affiche une note spécifique
     *
     * @param string $id ID de la note
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $note = $this->noteService->find($id);
        
        if (!$note) {
            return api_not_found('Note non trouvée');
        }
        
        return api_success(new NoteResource($note), 'Note récupérée avec succès');
    }

    /**
     * Met à jour une note existante
     *
     * @param UpdateNoteRequest $request Requête validée pour la mise à jour
     * @param Note $note Instance de la note à mettre à jour
     * @return JsonResponse
     */
    public function update(UpdateNoteRequest $request, Note $note): JsonResponse
    {
        $updatedNote = $this->noteService->update($note, $request->validated());
        
        return api_updated(new NoteResource($updatedNote), 'Note mise à jour avec succès');
    }

    /**
     * Supprime une note
     *
     * @param Note $note Instance de la note à supprimer
     * @return JsonResponse
     */
    public function destroy(Note $note): JsonResponse
    {
        $this->noteService->delete($note);
        
        return api_deleted('Note supprimée avec succès');
    }
}