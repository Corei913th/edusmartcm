<?php

namespace App\Models;

use App\Enums\ResolutionConflit;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $sync_queue_id
 * @property string $ressource_type
 * @property string $ressource_id
 * @property array $valeur_cliente
 * @property array $valeur_serveur
 * @property string $timestamp_client
 * @property string $timestamp_serveur
 * @property ResolutionConflit $resolution
 * @property string $resolu_at
 * @property string $created_at
 * @property string $updated_at
 */
class ConflitSync extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'conflits_sync';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'sync_queue_id',
        'ressource_type',
        'ressource_id',
        'valeur_cliente',
        'valeur_serveur',
        'timestamp_client',
        'timestamp_serveur',
        'resolution',
        'resolu_at',
    ];

    protected $casts = [
        'resolution' => ResolutionConflit::class,
        'valeur_cliente' => 'array',
        'valeur_serveur' => 'array',
        'timestamp_client' => 'datetime',
        'timestamp_serveur' => 'datetime',
        'resolu_at' => 'datetime',
    ];

    public function syncQueue(): BelongsTo
    {
        return $this->belongsTo(SyncQueue::class, 'sync_queue_id');
    }
}
