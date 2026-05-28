<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on conflits_sync.resolution
 */
enum ResolutionConflit: string {
    case LWW_CLIENT = 'LWW_CLIENT';
    case LWW_SERVER = 'LWW_SERVER';
    case MANUEL = 'MANUEL';
}
