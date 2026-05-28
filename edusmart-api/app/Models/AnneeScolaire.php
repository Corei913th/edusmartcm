<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $libelle
 * @property string $date_debut
 * @property string $date_fin
 * @property bool $est_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Periode> $periodes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classe> $classes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AffectationEnseignement> $affectationsEnseignement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Inscription> $inscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmploiDuTemps> $emploisDuTemps
 */
class AnneeScolaire extends Model
{
    public $timestamps = false;

    protected $fillable = ['libelle', 'date_debut', 'date_fin', 'est_active'];

    protected function casts(): array
    {
        return [
            'est_active' => 'boolean',
            'date_debut' => 'date:Y-m-d',
            'date_fin' => 'date:Y-m-d',
        ];
    }

    public function periodes(): HasMany
    {
        return $this->hasMany(Periode::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    public function affectationsEnseignement(): HasMany
    {
        return $this->hasMany(AffectationEnseignement::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class);
    }
}
