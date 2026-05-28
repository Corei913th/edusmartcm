<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $jour
 * @property string $heure_debut
 * @property string $heure_fin
 * @property string $created_at
 * @property string $updated_at
 */
class CreneauHoraire extends Model {
    protected $table = 'creneaux_horaires';

    public $timestamps = false;

    protected $fillable = [
        'jour',
        'heure_debut',
        'heure_fin',
    ];

    public function emploisDuTemps(): HasMany {
        return $this->hasMany(EmploiDuTemps::class);
    }
}
