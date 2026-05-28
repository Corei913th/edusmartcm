<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on matieres.type
 */
enum MatiereType: string
{
    case GENERALE = 'GENERALE';
    case TECHNIQUE = 'TECHNIQUE';
    case EPS = 'EPS';
    case OPTION = 'OPTION';
}
