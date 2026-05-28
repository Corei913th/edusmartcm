<?php

namespace App\Models;

use App\Enums\StatutAbsence;
use App\Traits\HasOfflineSyncTracking;
use App\Traits\HasUpdatedAtTrigger;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $inscription_id
 * @property string $affectation_id
 * @property string $date_absence
 * @property string $heure_debut
 * @property string $heure_fin
 * @property int $duree_heures
 * @property string $motif
 * @property StatutAbsence $statut
 * @property string $justificatif_path
 * @property bool $saisie_hors_ligne
 * @property string $sync_at
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_at
 */
class Absence extends Model
{
    use HasOfflineSyncTracking;
    use HasUpdatedAtTrigger;
    use UsesUuidAsPrimaryKey;

    protected $table = 'absences';

    protected $fillable = [
        'inscription_id',
        'affectation_id',
        'date_absence',
        'heure_debut',
        'heure_fin',
        'duree_heures',
        'motif',
        'statut',
        'justificatif_path',
        'saisie_hors_ligne',
        'sync_at',
        'created_by',
    ];

    protected $casts = [
        'statut' => StatutAbsence::class,
        'duree_heures' => 'integer',
        'saisie_hors_ligne' => 'boolean',
        'date_absence' => 'date:Y-m-d',
        'sync_at' => 'datetime',
    ];

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function affectationEnseignement(): BelongsTo
    {
        return $this->belongsTo(AffectationEnseignement::class, 'affectation_id');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
