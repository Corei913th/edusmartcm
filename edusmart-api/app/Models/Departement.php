<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $region_id
 * @property string      $code
 * @property string      $nom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Region $region
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Etablissement> $etablissements
 */
class Departement extends Model {
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['region_id', 'code', 'nom'];

    public function region(): BelongsTo {
        return $this->belongsTo(Region::class);
    }

    public function etablissements(): HasMany {
        return $this->hasMany(Etablissement::class);
    }
}
