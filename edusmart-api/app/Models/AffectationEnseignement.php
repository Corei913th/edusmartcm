<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string      $id
 * @property string      $enseignant_id
 * @property string      $classe_id
 * @property int         $matiere_id
 * @property int         $annee_id
 * @property int         $coefficient
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Enseignant $enseignant
 * @property-read Classe $classe
 * @property-read Matiere $matiere
 * @property-read AnneeScolaire $anneeScolaire
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Note> $notes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MoyenneMatiere> $moyennesMatiere
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Absence> $absences
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProgressionCours> $progressionsCours
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmploiDuTemps> $emploisDuTemps
 */
class AffectationEnseignement extends Model {
    use UsesUuidAsPrimaryKey;

    public $timestamps = false;

    protected $fillable = [
        'enseignant_id', 'classe_id', 'matiere_id', 'annee_id', 'coefficient',
    ];

    protected function casts(): array {
        return [
            'coefficient' => 'integer',
        ];
    }

    public function enseignant(): BelongsTo {
        return $this->belongsTo(Enseignant::class);
    }

    public function classe(): BelongsTo {
        return $this->belongsTo(Classe::class);
    }

    public function matiere(): BelongsTo {
        return $this->belongsTo(Matiere::class);
    }

    public function anneeScolaire(): BelongsTo {
        return $this->belongsTo(AnneeScolaire::class, 'annee_id');
    }

    public function notes(): HasMany {
        return $this->hasMany(Note::class);
    }

    public function moyennesMatiere(): HasMany {
        return $this->hasMany(MoyenneMatiere::class);
    }

    public function absences(): HasMany {
        return $this->hasMany(Absence::class);
    }

    public function progressionsCours(): HasMany {
        return $this->hasMany(ProgressionCours::class);
    }

    public function emploisDuTemps(): HasMany {
        return $this->hasMany(EmploiDuTemps::class);
    }
}
