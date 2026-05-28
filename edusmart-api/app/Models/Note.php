<?php

namespace App\Models;

use App\Enums\TypeEvaluation;
use App\Traits\HasOfflineSyncTracking;
use App\Traits\HasUpdatedAtTrigger;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string         $id
 * @property string         $inscription_id
 * @property string         $affectation_id
 * @property string         $periode_id
 * @property TypeEvaluation $type_evaluation
 * @property float          $note
 * @property int            $coefficient
 * @property string         $date_evaluation
 * @property bool           $saisie_hors_ligne
 * @property string         $sync_at
 * @property string         $created_by
 * @property string         $created_at
 * @property string         $updated_at
 */
class Note extends Model {
    use HasFactory;
    use HasOfflineSyncTracking;
    use HasUpdatedAtTrigger;
    use UsesUuidAsPrimaryKey;

    protected $table = 'notes';

    protected $fillable = [
        'inscription_id',
        'affectation_id',
        'periode_id',
        'type_evaluation',
        'note',
        'coefficient',
        'date_evaluation',
        'saisie_hors_ligne',
        'sync_at',
        'created_by',
    ];

    protected $casts = [
        'type_evaluation' => TypeEvaluation::class,
        'note' => 'decimal:2',
        'coefficient' => 'integer',
        'saisie_hors_ligne' => 'boolean',
        'date_evaluation' => 'date:Y-m-d',
        'sync_at' => 'datetime',
    ];

    public function inscription(): BelongsTo {
        return $this->belongsTo(Inscription::class);
    }

    public function affectationEnseignement(): BelongsTo {
        return $this->belongsTo(AffectationEnseignement::class, 'affectation_id');
    }

    public function periode(): BelongsTo {
        return $this->belongsTo(Periode::class);
    }

    public function createur(): BelongsTo {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
