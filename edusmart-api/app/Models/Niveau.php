<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $libelle
 * @property int|null $ordre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classe> $classes
 */
class Niveau extends Model
{
    public $timestamps = false;

    protected $fillable = ['code', 'libelle', 'ordre'];

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }
}
