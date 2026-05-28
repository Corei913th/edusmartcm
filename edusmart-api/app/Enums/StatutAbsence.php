<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on absences.statut
 */
enum StatutAbsence: string {
    case JUSTIFIEE = 'JUSTIFIEE';
    case INJUSTIFIEE = 'INJUSTIFIEE';
    case EN_ATTENTE = 'EN_ATTENTE';
}
