<?php

namespace App\Models;

use App\Enums\MotifRadiation;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string         $id
 * @property string         $inscription_id
 * @property string         $date_radiation
 * @property MotifRadiation $motif
 * @property string|null    $observations
 * @property string|null    $created_by
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 * @property-read Inscription $inscription
 * @property-read Utilisateur|null $createur
 */
class Radiation extends Model {
    use UsesUuidAsPrimaryKey;

    protected $table = 'radiations';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'inscription_id', 'date_radiation', 'motif', 'observations', 'created_by',
    ];

    protected function casts(): array {
        return [
            'motif' => MotifRadiation::class,
            'date_radiation' => 'date:Y-m-d',
        ];
    }

    public function inscription(): BelongsTo {
        return $this->belongsTo(Inscription::class);
    }

    public function createur(): BelongsTo {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
