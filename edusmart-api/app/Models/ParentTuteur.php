<?php

namespace App\Models;

use App\Traits\HasEncryptedPii;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $utilisateur_id
 * @property string $nom
 * @property string $prenom
 * @property string $telephone
 * @property string $email
 * @property string $profession
 * @property string $created_at
 * @property string $updated_at
 */
class ParentTuteur extends Model
{
    use HasEncryptedPii;
    use UsesUuidAsPrimaryKey;

    protected $table = 'parents_tuteurs';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'prenom',
        'telephone',
        'email',
        'profession',
    ];

    protected array $encrypted = [
        'nom',
        'prenom',
        'telephone',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function rattachements(): HasMany
    {
        return $this->hasMany(RattachementParentEleve::class, 'parent_id');
    }

    public function preferencesNotifications(): HasMany
    {
        return $this->hasMany(PreferenceNotification::class, 'parent_id');
    }
}
