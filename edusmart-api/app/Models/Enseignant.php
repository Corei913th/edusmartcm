<?php

namespace App\Models;

use App\Traits\BelongsToEtablissement;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string      $id
 * @property string      $utilisateur_id
 * @property string      $etablissement_id
 * @property string      $matricule
 * @property string|null $grade
 * @property string|null $specialite
 * @property string|null $date_prise_fonction
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Utilisateur $utilisateur
 * @property-read Etablissement $etablissement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AffectationEnseignement> $affectationsEnseignement
 */
class Enseignant extends Model {
    use BelongsToEtablissement;
    use HasFactory;
    use UsesUuidAsPrimaryKey;

    protected $table = 'enseignants';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'utilisateur_id', 'etablissement_id', 'matricule',
        'grade', 'specialite', 'date_prise_fonction',
    ];

    protected function casts(): array {
        return [
            'date_prise_fonction' => 'date:Y-m-d',
        ];
    }

    public function utilisateur(): BelongsTo {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function affectationsEnseignement(): HasMany {
        return $this->hasMany(AffectationEnseignement::class);
    }
}
