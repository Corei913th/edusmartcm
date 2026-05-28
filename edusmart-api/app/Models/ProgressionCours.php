<?php

namespace App\Models;

use App\Enums\StatutProgression;
use App\Traits\HasUpdatedAtTrigger;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $affectation_id
 * @property string $periode_id
 * @property string $chapitre
 * @property string $objectif
 * @property StatutProgression $statut
 * @property string $date_debut_prevu
 * @property string $date_fin_prevu
 * @property string $date_fin_reel
 * @property int $taux_avancement
 * @property string $created_at
 * @property string $updated_at
 */
class ProgressionCours extends Model
{
    use HasUpdatedAtTrigger;
    use UsesUuidAsPrimaryKey;

    protected $table = 'progressions_cours';

    protected $fillable = [
        'affectation_id',
        'periode_id',
        'chapitre',
        'objectif',
        'statut',
        'date_debut_prevu',
        'date_fin_prevu',
        'date_fin_reel',
        'taux_avancement',
    ];

    protected $casts = [
        'statut' => StatutProgression::class,
        'taux_avancement' => 'integer',
        'date_debut_prevu' => 'date:Y-m-d',
        'date_fin_prevu' => 'date:Y-m-d',
        'date_fin_reel' => 'date:Y-m-d',
    ];

    public function affectationEnseignement(): BelongsTo
    {
        return $this->belongsTo(AffectationEnseignement::class, 'affectation_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}
