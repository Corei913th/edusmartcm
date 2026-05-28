<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on etablissements.type
 */
enum EtablissementType: string
{
    case LYCEE = 'LYCEE';
    case CES = 'CES';
    case COLLEGE = 'COLLEGE';
}
