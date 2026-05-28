<?php

namespace App\Models;

use App\Constants\TokenConstants;
use App\Enums\Role;
use App\Models\Traits\HasRelations;
use App\Traits\HasEncryptedPii;
use App\Traits\HasUpdatedAtTrigger;
use App\Traits\UsesUuidAsPrimaryKey;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string      $id
 * @property Role        $role_code
 * @property int         $etablissement_id
 * @property string      $nom
 * @property string      $prenom
 * @property string      $telephone
 * @property string      $email
 * @property string      $password_hash
 * @property bool        $est_actif
 * @property Carbon|null $derniere_connexion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Utilisateur extends Authenticatable {
    use HasApiTokens;
    use HasEncryptedPii;
    use HasFactory;
    use HasRelations;
    use HasUpdatedAtTrigger;
    use UsesUuidAsPrimaryKey;
    public const ROLE_DEFAULT = 'ENSEIGNANT';

    protected $table = 'utilisateurs';

    protected $fillable = [
        'role_code', 'etablissement_id', 'nom', 'prenom', 'telephone',
        'email', 'password_hash', 'est_actif', 'derniere_connexion',
    ];

    protected $hidden = [
        'password_hash', 'remember_token',
    ];

    protected $casts = [
        'role_code' => Role::class,
        'est_actif' => 'boolean',
        'derniere_connexion' => 'datetime',
    ];

    protected array $encrypted = [
        'nom', 'prenom', 'telephone',
    ];

    public function getAuthPassword(): string {
        return $this->password_hash;
    }

    public function issueToken(): string {
        return $this->createToken(
            TokenConstants::DEFAULT_ACCESS_TOKEN_NAME,
            [TokenConstants::ABILITY_ALL],
            now()->addMinutes(TokenConstants::DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES),
        )->plainTextToken;
    }

    public function markAsConnected(): void {
        $this->update(['derniere_connexion' => now()]);
    }
}
