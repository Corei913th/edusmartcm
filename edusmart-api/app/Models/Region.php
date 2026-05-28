<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $code
 * @property string      $nom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Departement> $departements
 */
class Region extends Model {
    use HasFactory;

    protected $table = 'regions';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = ['code', 'nom'];

    public function departements(): HasMany {
        return $this->hasMany(Departement::class);
    }
}
