<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on salles.type
 */
enum SalleType: string {
    case CLASSE = 'CLASSE';
    case LABO = 'LABO';
    case AMPHI = 'AMPHI';
    case SALLE_INFO = 'SALLE_INFO';
}
