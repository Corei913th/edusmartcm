<?php

namespace App\Models;

use App\Enums\Appreciation;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string       $id
 * @property string       $inscription_id
 * @property string       $affectation_id
 * @property string       $periode_id
 * @property float        $moyenne
 * @property int          $rang_matiere
 * @property Appreciation $appreciation
 * @property string       $created_at
 * @property string       $updated_at
 */
class MoyenneMatiere extends Model {
    use UsesUuidAsPrimaryKey;

    protected $table = 'moyennes_matieres';

    public $timestamps = false;

    protected $fillable = [
        'inscription_id',
        'affectation_id',
        'periode_id',
        'moyenne',
        'rang_matiere',
        'appreciation',
    ];

    protected $casts = [
        'moyenne' => 'decimal:2',
        'appreciation' => Appreciation::class,
        'rang_matiere' => 'integer',
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
}
