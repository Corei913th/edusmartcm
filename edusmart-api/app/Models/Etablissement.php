<?php

namespace App\Models;

use App\Enums\Connectivite;
use App\Enums\EtablissementType;
use App\Traits\HasEncryptedPii;
use App\Traits\HasUpdatedAtTrigger;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string            $id
 * @property string            $code_uai
 * @property string            $nom
 * @property EtablissementType $type
 * @property int               $departement_id
 * @property string            $adresse
 * @property string            $telephone
 * @property string            $email
 * @property bool              $est_pilote
 * @property float|null        $latitude
 * @property float|null        $longitude
 * @property Connectivite      $connectivite
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 * @property-read Departement $departement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Salle> $salles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classe> $classes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Enseignant> $enseignants
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PersonnelAdministratif> $personnelAdministratif
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Eleve> $eleves
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FilDiscussion> $filsDiscussion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Utilisateur> $utilisateurs
 */
class Etablissement extends Model {
    use HasEncryptedPii;
    use HasFactory;
    use HasUpdatedAtTrigger;
    use UsesUuidAsPrimaryKey;

    protected $fillable = [
        'code_uai', 'nom', 'type', 'departement_id', 'adresse',
        'telephone', 'email', 'est_pilote', 'latitude', 'longitude', 'connectivite',
    ];

    protected array $encrypted = ['telephone'];

    protected function casts(): array {
        return [
            'type' => EtablissementType::class,
            'connectivite' => Connectivite::class,
            'est_pilote' => 'boolean',
        ];
    }

    public function departement(): BelongsTo {
        return $this->belongsTo(Departement::class);
    }

    public function salles(): HasMany {
        return $this->hasMany(Salle::class);
    }

    public function classes(): HasMany {
        return $this->hasMany(Classe::class);
    }

    public function enseignants(): HasMany {
        return $this->hasMany(Enseignant::class);
    }

    public function personnelAdministratif(): HasMany {
        return $this->hasMany(PersonnelAdministratif::class);
    }

    public function eleves(): HasMany {
        return $this->hasMany(Eleve::class);
    }

    public function filsDiscussion(): HasMany {
        return $this->hasMany(FilDiscussion::class);
    }

    public function utilisateurs(): HasMany {
        return $this->hasMany(Utilisateur::class);
    }
}
