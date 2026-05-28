<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on eleves.sexe
 */
enum Sexe: string {
    case M = 'M';
    case F = 'F';
}
