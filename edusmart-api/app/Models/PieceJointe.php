<?php

namespace App\Models;

use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $message_id
 * @property string $nom_fichier
 * @property string $type_mime
 * @property int $taille_octets
 * @property string $chemin_stockage
 * @property string $created_at
 * @property string $updated_at
 */
class PieceJointe extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'pieces_jointes';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'message_id',
        'nom_fichier',
        'type_mime',
        'taille_octets',
        'chemin_stockage',
    ];

    protected $casts = [
        'taille_octets' => 'integer',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}
