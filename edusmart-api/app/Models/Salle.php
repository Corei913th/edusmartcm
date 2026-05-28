<?php

namespace App\Models;

use App\Enums\SalleType;
use App\Traits\BelongsToEtablissement;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string      $id
 * @property string      $etablissement_id
 * @property string      $nom
 * @property int         $capacite
 * @property SalleType   $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Etablissement $etablissement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classe> $classes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmploiDuTemps> $emploisDuTemps
 */
class Salle extends Model {
    use BelongsToEtablissement;
    use UsesUuidAsPrimaryKey;

    public $timestamps = false;

    protected $fillable = ['etablissement_id', 'nom', 'capacite', 'type'];

    protected function casts(): array {
        return [
            'type' => SalleType::class,
            'capacite' => 'integer',
        ];
    }

    public function classes(): HasMany {
        return $this->hasMany(Classe::class);
    }

    public function emploisDuTemps(): HasMany {
        return $this->hasMany(EmploiDuTemps::class);
    }
}
