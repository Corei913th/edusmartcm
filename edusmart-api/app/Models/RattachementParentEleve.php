<?php

namespace App\Models;

use App\Enums\LienParente;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $parent_id
 * @property string $eleve_id
 * @property LienParente $lien
 * @property bool $est_contact_principal
 * @property bool $peut_consulter_notes
 * @property bool $peut_recevoir_notifs
 * @property string $created_at
 * @property string $updated_at
 */
class RattachementParentEleve extends Model
{
    use UsesUuidAsPrimaryKey;

    protected $table = 'rattachements_parent_eleve';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'parent_id',
        'eleve_id',
        'lien',
        'est_contact_principal',
        'peut_consulter_notes',
        'peut_recevoir_notifs',
    ];

    protected $casts = [
        'lien' => LienParente::class,
        'est_contact_principal' => 'boolean',
        'peut_consulter_notes' => 'boolean',
        'peut_recevoir_notifs' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentTuteur::class, 'parent_id');
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }
}
