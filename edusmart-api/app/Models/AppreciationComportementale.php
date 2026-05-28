<?php

namespace App\Models;

use App\Enums\NiveauAppreciation;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $inscription_id
 * @property string $periode_id
 * @property NiveauAppreciation $discipline
 * @property NiveauAppreciation $ponctualite
 * @property NiveauAppreciation $travail
 * @property string $commentaire
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_at
 */
class AppreciationComportementale extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'appreciations_comportementales';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'inscription_id',
        'periode_id',
        'discipline',
        'ponctualite',
        'travail',
        'commentaire',
        'created_by',
    ];

    protected $casts = [
        'discipline' => NiveauAppreciation::class,
        'ponctualite' => NiveauAppreciation::class,
        'travail' => NiveauAppreciation::class,
    ];

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
