<?php

namespace App\Models;

use App\Traits\BelongsToEtablissement;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $etablissement_id
 * @property int $annee_id
 * @property int $niveau_id
 * @property int|null $serie_id
 * @property string $nom
 * @property int $effectif_max
 * @property string|null $salle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Etablissement $etablissement
 * @property-read AnneeScolaire $anneeScolaire
 * @property-read Niveau $niveau
 * @property-read Serie|null $serie
 * @property-read Salle|null $salle
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Inscription> $inscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AffectationEnseignement> $affectationsEnseignement
 */
class Classe extends Model
{
    use BelongsToEtablissement, UsesUuidAsPrimaryKey;

    protected $table = 'classes';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'etablissement_id', 'annee_id', 'niveau_id', 'serie_id',
        'nom', 'effectif_max', 'salle_id',
    ];

    protected function casts(): array
    {
        return [
            'effectif_max' => 'integer',
        ];
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_id');
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function affectationsEnseignement(): HasMany
    {
        return $this->hasMany(AffectationEnseignement::class);
    }
}
