<?php

namespace App\Models\Traits;

use App\Models\Absence;
use App\Models\AppreciationComportementale;
use App\Models\AuditLog;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\Etablissement;
use App\Models\Message;
use App\Models\Note;
use App\Models\Notification;
use App\Models\OtpCode;
use App\Models\ParentTuteur;
use App\Models\ParticipantFil;
use App\Models\PersonnelAdministratif;
use App\Models\PushSubscription;
use App\Models\Radiation;
use App\Models\Session;
use App\Models\SyncQueue;
use App\Models\Transfert;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasRelations {
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
