<?php

namespace App\Traits;

/**
 * Disables Eloquent's automatic updated_at management.
 *
 * Used when a PostgreSQL trigger manages updated_at column.
 */
trait HasUpdatedAtTrigger
{
    public function setUpdatedAt($value): ?static
    {
        return $this;
    }

    public function getUpdatedAtColumn(): ?string
    {
        return 'updated_at';
    }
}
