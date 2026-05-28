<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $annee_id
 * @property int $numero
 * @property string|null $libelle
 * @property string $date_debut
 * @property string $date_fin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read AnneeScolaire $anneeScolaire
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Note> $notes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MoyenneMatiere> $moyennesMatiere
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Bulletin> $bulletins
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AppreciationComportementale> $appreciationsComportementale
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProgressionCours> $progressionsCours
 */
class Periode extends Model
{
    public $timestamps = false;

    protected $fillable = ['annee_id', 'numero', 'libelle', 'date_debut', 'date_fin'];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date:Y-m-d',
            'date_fin' => 'date:Y-m-d',
        ];
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function moyennesMatiere(): HasMany
    {
        return $this->hasMany(MoyenneMatiere::class);
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

    public function appreciationsComportementale(): HasMany
    {
        return $this->hasMany(AppreciationComportementale::class);
    }

    public function progressionsCours(): HasMany
    {
        return $this->hasMany(ProgressionCours::class);
    }
}
