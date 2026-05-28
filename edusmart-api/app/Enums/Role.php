<?php

namespace App\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case ADMIN_ETABLISSEMENT = 'ADMIN_ETABLISSEMENT';
    case DIRECTION = 'DIRECTION';
    case ENSEIGNANT = 'ENSEIGNANT';
    case PARENT = 'PARENT';
    case ELEVE = 'ELEVE';

    public function libelle(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrateur',
            self::ADMIN_ETABLISSEMENT => 'Administrateur Établissement',
            self::DIRECTION => 'Direction',
            self::ENSEIGNANT => 'Enseignant',
            self::PARENT => 'Parent/Tuteur',
            self::ELEVE => 'Élève',
        };
    }
}
