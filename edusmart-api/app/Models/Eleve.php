<?php

namespace App\Models;

use App\Enums\Sexe;
use App\Traits\BelongsToEtablissement;
use App\Traits\HasEncryptedPii;
use App\Traits\HasUpdatedAtTrigger;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string|null $utilisateur_id
 * @property string $etablissement_id
 * @property string $matricule
 * @property string $nom
 * @property string $prenom
 * @property string|null $date_naissance
 * @property string $lieu_naissance
 * @property Sexe $sexe
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Utilisateur|null $utilisateur
 * @property-read Etablissement $etablissement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Inscription> $inscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RattachementParentEleve> $rattachementsParentEleve
 */
class Eleve extends Model
{
    use BelongsToEtablissement, HasEncryptedPii, HasUpdatedAtTrigger, UsesUuidAsPrimaryKey;

    protected $fillable = [
        'utilisateur_id', 'etablissement_id', 'matricule',
        'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'sexe',
    ];

    protected array $encrypted = ['nom', 'prenom', 'lieu_naissance'];

    protected function casts(): array
    {
        return [
            'sexe' => Sexe::class,
            'date_naissance' => 'date:Y-m-d',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function rattachementsParentEleve(): HasMany
    {
        return $this->hasMany(RattachementParentEleve::class);
    }
}
