<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on fils_discussion.type
 */
enum TypeDiscussion: string
{
    case PARENT_ENSEIGNANT = 'PARENT_ENSEIGNANT';
    case PARENT_DIRECTION = 'PARENT_DIRECTION';
    case ENSEIGNANT_DIRECTION = 'ENSEIGNANT_DIRECTION';
    case INTERNE = 'INTERNE';
}
