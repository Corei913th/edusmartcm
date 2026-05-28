<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on etablissements.connectivite
 */
enum Connectivite: string
{
    case _3G = '3G';
    case _4G = '4G';
    case FIBRE = 'FIBRE';
    case ADSL = 'ADSL';
    case NONE = 'NONE';
}
