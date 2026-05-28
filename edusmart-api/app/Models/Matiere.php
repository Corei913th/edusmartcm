<?php

namespace App\Models;

use App\Enums\MatiereType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $code
 * @property string      $nom
 * @property int         $coefficient_defaut
 * @property MatiereType $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AffectationEnseignement> $affectationsEnseignement
 */
class Matiere extends Model {
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['code', 'nom', 'coefficient_defaut', 'type'];

    protected function casts(): array {
        return [
            'type' => MatiereType::class,
            'coefficient_defaut' => 'integer',
        ];
    }

    public function affectationsEnseignement(): HasMany {
        return $this->hasMany(AffectationEnseignement::class);
    }
}
