<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $inscription_id
 * @property string $periode_id
 * @property int    $rang_classe
 * @property float  $moyenne_generale
 * @property string $appreciation_generale
 * @property string $pdf_path
 * @property string $pdf_genere_at
 * @property bool   $est_publie
 * @property string $publie_at
 * @property string $created_at
 * @property string $updated_at
 */
class Bulletin extends Model {
    use HasFactory;
    use UsesUuidAsPrimaryKey;

    protected $table = 'bulletins';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'inscription_id',
        'periode_id',
        'rang_classe',
        'moyenne_generale',
        'appreciation_generale',
        'pdf_path',
        'pdf_genere_at',
        'est_publie',
        'publie_at',
    ];

    protected $casts = [
        'est_publie' => 'boolean',
        'moyenne_generale' => 'decimal:2',
        'rang_classe' => 'integer',
        'pdf_genere_at' => 'datetime',
        'publie_at' => 'datetime',
    ];

    public function inscription(): BelongsTo {
        return $this->belongsTo(Inscription::class);
    }

    public function periode(): BelongsTo {
        return $this->belongsTo(Periode::class);
    }
}
