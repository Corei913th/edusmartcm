<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on envois_notifications.statut
 */
enum StatutEnvoi: string {
    case EN_ATTENTE = 'EN_ATTENTE';
    case ENVOYE = 'ENVOYE';
    case ECHEC = 'ECHEC';
    case IGNORE = 'IGNORE';
}
