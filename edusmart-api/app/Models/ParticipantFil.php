<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $fil_id
 * @property string $utilisateur_id
 * @property string $lu_at
 */
class ParticipantFil extends Model
{
    protected $table = 'participants_fil';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'fil_id',
        'utilisateur_id',
        'lu_at',
    ];

    protected $casts = [
        'lu_at' => 'datetime',
    ];

    public function filDiscussion(): BelongsTo
    {
        return $this->belongsTo(FilDiscussion::class, 'fil_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
