<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on sync_queue.statut
 */
enum StatutSync: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case EN_COURS = 'EN_COURS';
    case SYNCHRONISE = 'SYNCHRONISE';
    case CONFLIT = 'CONFLIT';
    case ECHEC = 'ECHEC';
}
