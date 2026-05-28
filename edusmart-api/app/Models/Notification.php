<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $destinataire_id
 * @property string $type_evenement
 * @property string $titre
 * @property string $corps
 * @property array $donnees_meta
 * @property bool $lu
 * @property string $lu_at
 * @property string $created_at
 * @property string $updated_at
 */
class Notification extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'notifications';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'destinataire_id',
        'type_evenement',
        'titre',
        'corps',
        'donnees_meta',
        'lu',
        'lu_at',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'donnees_meta' => 'array',
        'lu_at' => 'datetime',
    ];

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'destinataire_id');
    }

    public function envois(): HasMany
    {
        return $this->hasMany(EnvoiNotification::class, 'notification_id');
    }
}
