<?php

namespace App\Models;

use App\Enums\MethodeHTTP;
use App\Enums\StatutSync;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $utilisateur_id
 * @property MethodeHTTP $methode
 * @property string $endpoint
 * @property array $payload
 * @property int $tentatives
 * @property StatutSync $statut
 * @property string $timestamp_client
 * @property string $sync_at
 * @property string $erreur
 * @property string $created_at
 * @property string $updated_at
 */
class SyncQueue extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'sync_queue';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'utilisateur_id',
        'methode',
        'endpoint',
        'payload',
        'tentatives',
        'statut',
        'timestamp_client',
        'sync_at',
        'erreur',
    ];

    protected $casts = [
        'methode' => MethodeHTTP::class,
        'statut' => StatutSync::class,
        'tentatives' => 'integer',
        'payload' => 'array',
        'timestamp_client' => 'datetime',
        'sync_at' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function conflits(): HasMany
    {
        return $this->hasMany(ConflitSync::class, 'sync_queue_id');
    }
}
