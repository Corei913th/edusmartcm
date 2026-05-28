<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on moyennes_matieres.appreciation
 */
enum Appreciation: string
{
    case TRES_BIEN = 'TRES_BIEN';
    case BIEN = 'BIEN';
    case ASSEZ_BIEN = 'ASSEZ_BIEN';
    case PASSABLE = 'PASSABLE';
    case MEDIOCRE = 'MEDIOCRE';
    case INSUFFISANT = 'INSUFFISANT';
}
