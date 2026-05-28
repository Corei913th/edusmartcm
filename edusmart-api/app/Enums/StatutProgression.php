<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on progressions_cours.statut
 */
enum StatutProgression: string
{
    case PLANIFIE = 'PLANIFIE';
    case EN_COURS = 'EN_COURS';
    case TERMINE = 'TERMINE';
}
