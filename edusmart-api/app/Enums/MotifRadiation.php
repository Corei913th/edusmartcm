<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on radiations.motif
 */
enum MotifRadiation: string {
    case EXCLUSION = 'EXCLUSION';
    case ABANDON = 'ABANDON';
    case DECES = 'DECES';
    case AUTRE = 'AUTRE';
}
