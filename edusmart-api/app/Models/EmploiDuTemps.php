<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $affectation_id
 * @property int $salle_id
 * @property int $creneau_id
 * @property int $annee_id
 * @property string $created_at
 * @property string $updated_at
 */
class EmploiDuTemps extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'emplois_du_temps';

    public $timestamps = false;

    protected $fillable = [
        'affectation_id',
        'salle_id',
        'creneau_id',
        'annee_id',
    ];

    public function affectationEnseignement(): BelongsTo
    {
        return $this->belongsTo(AffectationEnseignement::class, 'affectation_id');
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function creneauHoraire(): BelongsTo
    {
        return $this->belongsTo(CreneauHoraire::class, 'creneau_id');
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_id');
    }
}
