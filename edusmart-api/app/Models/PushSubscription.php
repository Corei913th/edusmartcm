<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $utilisateur_id
 * @property string $endpoint
 * @property string $p256dh
 * @property string $auth
 * @property string $user_agent
 * @property string $created_at
 * @property string $updated_at
 */
class PushSubscription extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'push_subscriptions';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'utilisateur_id',
        'endpoint',
        'p256dh',
        'auth',
        'user_agent',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
