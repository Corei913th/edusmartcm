<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on appreciations_comportementales.niveau
 */
enum NiveauAppreciation: string {
    case EXCELLENT = 'EXCELLENT';
    case BIEN = 'BIEN';
    case MOYEN = 'MOYEN';
    case FAIBLE = 'FAIBLE';
}
