<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $inscription_id
 * @property string $etablissement_origine_id
 * @property string $etablissement_destination_id
 * @property string $date_transfert
 * @property string|null $motif
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Inscription $inscription
 * @property-read Etablissement $etablissementOrigine
 * @property-read Etablissement $etablissementDestination
 * @property-read Utilisateur|null $createur
 */
class Transfert extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'transferts';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'inscription_id', 'etablissement_origine_id', 'etablissement_destination_id',
        'date_transfert', 'motif', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_transfert' => 'date:Y-m-d',
        ];
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function etablissementOrigine(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_origine_id');
    }

    public function etablissementDestination(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_destination_id');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
