<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on inscriptions.statut
 */
enum StatutInscription: string
{
    case ACTIF = 'ACTIF';
    case TRANSFERE = 'TRANSFERE';
    case RADIE = 'RADIE';
    case DIPLOME = 'DIPLOME';
}
