<?php

namespace App\Models;

use App\Enums\TypeEvenement;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string        $id
 * @property string        $parent_id
 * @property TypeEvenement $type_evenement
 * @property bool          $canal_sms
 * @property bool          $canal_email
 * @property bool          $canal_push
 * @property string        $created_at
 * @property string        $updated_at
 */
class PreferenceNotification extends Model {
    use UsesUuidAsPrimaryKey;

    protected $table = 'preferences_notifications';

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'type_evenement',
        'canal_sms',
        'canal_email',
        'canal_push',
    ];

    protected $casts = [
        'type_evenement' => TypeEvenement::class,
        'canal_sms' => 'boolean',
        'canal_email' => 'boolean',
        'canal_push' => 'boolean',
    ];

    public function parent(): BelongsTo {
        return $this->belongsTo(ParentTuteur::class, 'parent_id');
    }
}
