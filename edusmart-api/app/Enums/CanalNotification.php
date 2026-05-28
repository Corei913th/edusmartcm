<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on envois_notifications.canal
 */
enum CanalNotification: string
{
    case SMS = 'SMS';
    case EMAIL = 'EMAIL';
    case PUSH = 'PUSH';
    case INAPP = 'INAPP';
}
