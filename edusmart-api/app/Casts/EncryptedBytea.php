<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;

/**
 * Custom Eloquent cast for BYTEA columns with AES-256 encryption.
 *
 * Handles PostgreSQL hex-encoded bytea format transparently.
 */
class EncryptedBytea implements CastsAttributes
{
    /**
     * Decrypt a BYTEA value from PostgreSQL.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     */
    public function get($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = $this->decodeBytea($value);

        return $raw ? Crypt::decryptString($raw) : null;
    }

    /**
     * Encrypt a value for storage as BYTEA.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $encrypted = Crypt::encryptString($value);

        return $this->encodeBytea($encrypted);
    }

    /**
     * Convert PostgreSQL hex bytea (\x...) to raw binary.
     */
    private function decodeBytea(string $value): string
    {
        if (str_starts_with($value, '\x')) {
            return hex2bin(substr($value, 2)) ?: $value;
        }

        return $value;
    }

    /**
     * Convert raw binary to PostgreSQL hex bytea format.
     */
    private function encodeBytea(string $value): string
    {
        return '\x' . bin2hex($value);
    }
}
