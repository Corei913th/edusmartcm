<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on rattachements_parent_eleve.lien
 */
enum LienParente: string
{
    case PERE = 'PERE';
    case MERE = 'MERE';
    case TUTEUR = 'TUTEUR';
    case AUTRE = 'AUTRE';
}
