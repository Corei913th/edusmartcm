<?php

namespace App\Models;

use App\Enums\TypeDiscussion;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $etablissement_id
 * @property string $sujet
 * @property TypeDiscussion $type
 * @property string $created_at
 * @property string $updated_at
 */
class FilDiscussion extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'fils_discussion';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'etablissement_id',
        'sujet',
        'type',
    ];

    protected $casts = [
        'type' => TypeDiscussion::class,
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ParticipantFil::class, 'fil_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'fil_id');
    }
}
