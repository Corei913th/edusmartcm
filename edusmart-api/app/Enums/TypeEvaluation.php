<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on notes.type_evaluation
 */
enum TypeEvaluation: string
{
    case DEVOIR = 'DEVOIR';
    case COMPOSITION = 'COMPOSITION';
    case ORAL = 'ORAL';
    case TP = 'TP';
    case EXAMEN = 'EXAMEN';
}
