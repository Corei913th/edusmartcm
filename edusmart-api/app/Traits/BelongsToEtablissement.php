<?php

namespace App\Traits;

use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Scope models to a specific etablissement and define the relationship.
 *
 * @property-read Etablissement|null $etablissement
 */
trait BelongsToEtablissement {
    /**
     * Get the etablissement that owns this model.
     */
    public function etablissement(): BelongsTo {
        return $this->belongsTo(Etablissement::class);
    }

    /**
     * Scope query to a specific etablissement.
     *
     * @param Builder $query
     * @param string  $etablissementId
     *
     * @return Builder
     */
    public function scopeByEtablissement($query, $etablissementId) {
        return $query->where('etablissement_id', $etablissementId);
    }
}
