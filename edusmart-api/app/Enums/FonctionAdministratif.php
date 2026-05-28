<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on personnel_administratif.fonction
 */
enum FonctionAdministratif: string
{
    case PROVISEUR = 'PROVISEUR';
    case CENSEUR = 'CENSEUR';
    case SECRETAIRE = 'SECRETAIRE';
}
