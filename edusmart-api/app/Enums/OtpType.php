<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on otp_codes.type
 */
enum OtpType: string {
    case SMS = 'SMS';
    case EMAIL = 'EMAIL';
}
