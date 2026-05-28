<?php

namespace App\Models;

use App\Traits\HasEncryptedPii;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $fil_id
 * @property string $expediteur_id
 * @property string $contenu
 * @property bool $est_archive
 * @property string $created_at
 * @property string $updated_at
 */
class Message extends Model
{
    use HasEncryptedPii;
    use UsesUuidAsPrimaryKey;

    protected $table = 'messages';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected array $encrypted = [
        'contenu',
    ];

    protected $fillable = [
        'fil_id',
        'expediteur_id',
        'contenu',
        'est_archive',
    ];

    protected $casts = [
        'est_archive' => 'boolean',
    ];

    public function filDiscussion(): BelongsTo
    {
        return $this->belongsTo(FilDiscussion::class, 'fil_id');
    }

    public function expediteur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'expediteur_id');
    }

    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointe::class, 'message_id');
    }
}
