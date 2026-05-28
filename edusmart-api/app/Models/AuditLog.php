<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property string $utilisateur_id
 * @property string $action
 * @property string $ressource_type
 * @property string $ressource_id
 * @property string $ip_address
 * @property string $user_agent
 * @property array  $metadata
 * @property string $created_at
 * @property string $updated_at
 */
class AuditLog extends Model {
    protected $table = 'audit_logs';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'utilisateur_id',
        'action',
        'ressource_type',
        'ressource_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function utilisateur(): BelongsTo {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
