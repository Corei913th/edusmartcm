<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $utilisateur_id
 * @property string $token_hash
 * @property string $ip_address
 * @property string $user_agent
 * @property string $expire_at
 * @property bool $revoque
 * @property string $created_at
 * @property string $updated_at
 */
class Session extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'sessions';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'utilisateur_id',
        'token_hash',
        'ip_address',
        'user_agent',
        'expire_at',
        'revoque',
    ];

    protected $casts = [
        'revoque' => 'boolean',
        'expire_at' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
