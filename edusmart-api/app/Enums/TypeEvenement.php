<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on preferences_notifications.type_evenement
 */
enum TypeEvenement: string
{
    case NOUVEAU_BULLETIN = 'NOUVEAU_BULLETIN';
    case ABSENCE_INJUSTIFIEE = 'ABSENCE_INJUSTIFIEE';
    case NOTE_DISPONIBLE = 'NOTE_DISPONIBLE';
    case REUNION_PARENTS = 'REUNION_PARENTS';
    case MESSAGE_RECU = 'MESSAGE_RECU';
    case ALERTE_SECURITE = 'ALERTE_SECURITE';
}
