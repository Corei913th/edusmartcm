<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $code
 * @property string      $libelle
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classe> $classes
 */
class Serie extends Model {
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['code', 'libelle'];

    public function classes(): HasMany {
        return $this->hasMany(Classe::class);
    }
}
