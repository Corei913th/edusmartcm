<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes for offline-first sync tracking on notes and absences.
 *
 * @method static \Illuminate\Database\Eloquent\Builder pendingSync()
 * @method static \Illuminate\Database\Eloquent\Builder synced()
 * @method static \Illuminate\Database\Eloquent\Builder offlineOnly()
 */
trait HasOfflineSyncTracking
{
    /**
     * Scope to records pending sync (saisie_hors_ligne = true, sync_at is null).
     */
    public function scopePendingSync(Builder $query): Builder
    {
        return $query->where('saisie_hors_ligne', true)
            ->whereNull('sync_at');
    }

    /**
     * Scope to records that have been synced (sync_at is not null).
     */
    public function scopeSynced(Builder $query): Builder
    {
        return $query->whereNotNull('sync_at');
    }

    /**
     * Scope to records created offline (saisie_hors_ligne = true).
     */
    public function scopeOfflineOnly(Builder $query): Builder
    {
        return $query->where('saisie_hors_ligne', true);
    }
}
