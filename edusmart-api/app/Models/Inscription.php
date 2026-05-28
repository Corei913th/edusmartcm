<?php

namespace App\Models;

use App\Enums\StatutInscription;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $eleve_id
 * @property string $classe_id
 * @property int $annee_id
 * @property string $date_inscription
 * @property StatutInscription $statut
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Eleve $eleve
 * @property-read Classe $classe
 * @property-read AnneeScolaire $anneeScolaire
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Note> $notes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MoyenneMatiere> $moyennesMatiere
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Absence> $absences
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Bulletin> $bulletins
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AppreciationComportementale> $appreciationsComportementale
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Transfert> $transferts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Radiation> $radiations
 */
class Inscription extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'inscriptions';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'eleve_id', 'classe_id', 'annee_id', 'date_inscription', 'statut',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutInscription::class,
            'date_inscription' => 'date:Y-m-d',
        ];
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
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

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

    public function appreciationsComportementale(): HasMany
    {
        return $this->hasMany(AppreciationComportementale::class);
    }

    public function transferts(): HasMany
    {
        return $this->hasMany(Transfert::class);
    }

    public function radiations(): HasMany
    {
        return $this->hasMany(Radiation::class);
    }
}
