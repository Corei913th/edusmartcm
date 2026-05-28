<?php

namespace App\Enums;

/**
 * Values from SQL CHECK constraint on sync_queue.methode
 */
enum MethodeHTTP: string
{
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
}
