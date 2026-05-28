<?php

namespace App\Models;

use App\Enums\OtpType;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $utilisateur_id
 * @property string $code_hash
 * @property OtpType $type
 * @property int $tentatives
 * @property string $expire_at
 * @property bool $utilise
 * @property string $created_at
 * @property string $updated_at
 */
class OtpCode extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'otp_codes';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'utilisateur_id',
        'code_hash',
        'type',
        'tentatives',
        'expire_at',
        'utilise',
    ];

    protected $casts = [
        'type' => OtpType::class,
        'tentatives' => 'integer',
        'utilise' => 'boolean',
        'expire_at' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
