<?php

namespace App\Models;

use App\Enums\CanalNotification;
use App\Enums\StatutEnvoi;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $notification_id
 * @property CanalNotification $canal
 * @property StatutEnvoi $statut
 * @property int $tentatives
 * @property string $derniere_tentative
 * @property string $erreur
 * @property string $envoye_at
 * @property string $created_at
 * @property string $updated_at
 */
class EnvoiNotification extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'envois_notifications';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'notification_id',
        'canal',
        'statut',
        'tentatives',
        'derniere_tentative',
        'erreur',
        'envoye_at',
    ];

    protected $casts = [
        'canal' => CanalNotification::class,
        'statut' => StatutEnvoi::class,
        'tentatives' => 'integer',
        'derniere_tentative' => 'datetime',
        'envoye_at' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
