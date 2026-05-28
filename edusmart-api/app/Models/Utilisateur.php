<?php

namespace App\Models;

use App\Constants\TokenConstants;
use App\Enums\Role;
use App\Traits\HasEncryptedPii;
use App\Traits\HasUpdatedAtTrigger;
use App\Traits\UsesUuidAsPrimaryKey;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function etablissement(): BelongsTo {
        return $this->belongsTo(Etablissement::class);
    }

    public function enseignant(): HasOne {
        return $this->hasOne(Enseignant::class, 'utilisateur_id');
    }

    public function personnelAdministratif(): HasOne {
        return $this->hasOne(PersonnelAdministratif::class, 'utilisateur_id');
    }

    public function eleve(): HasOne {
        return $this->hasOne(Eleve::class, 'utilisateur_id');
    }

    public function parentTuteur(): HasOne {
        return $this->hasOne(ParentTuteur::class, 'utilisateur_id');
    }

    public function sessions(): HasMany {
        return $this->hasMany(Session::class);
    }

    public function otpCodes(): HasMany {
        return $this->hasMany(OtpCode::class);
    }

    public function auditLogs(): HasMany {
        return $this->hasMany(AuditLog::class);
    }

    public function pushSubscriptions(): HasMany {
        return $this->hasMany(PushSubscription::class);
    }

    public function syncQueues(): HasMany {
        return $this->hasMany(SyncQueue::class);
    }

    public function messagesEnvoyes(): HasMany {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    public function notifications(): HasMany {
        return $this->hasMany(Notification::class, 'destinataire_id');
    }

    public function notesCrees(): HasMany {
        return $this->hasMany(Note::class, 'created_by');
    }

    public function absencesCrees(): HasMany {
        return $this->hasMany(Absence::class, 'created_by');
    }

    public function appreciationsCrees(): HasMany {
        return $this->hasMany(AppreciationComportementale::class, 'created_by');
    }

    public function transfertsCrees(): HasMany {
        return $this->hasMany(Transfert::class, 'created_by');
    }

    public function radiationsCrees(): HasMany {
        return $this->hasMany(Radiation::class, 'created_by');
    }

    public function participantsFil(): HasMany {
        return $this->hasMany(ParticipantFil::class, 'utilisateur_id');
    }
}
