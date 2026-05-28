<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Automatically generates UUID v4 as primary key on creating.
 *
 * Disables auto-incrementing integer primary keys in favor of UUID.
 */
trait UsesUuidAsPrimaryKey
{
    protected static function bootUsesUuidAsPrimaryKey(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
