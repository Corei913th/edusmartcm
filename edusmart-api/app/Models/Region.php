<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $nom
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Departement> $departements
 */
class Region extends Model
{
    protected $table = 'regions';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = ['code', 'nom'];

    public function departements(): HasMany
    {
        return $this->hasMany(Departement::class);
    }
}
