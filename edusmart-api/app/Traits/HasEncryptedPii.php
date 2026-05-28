<?php

namespace App\Traits;

use App\Casts\EncryptedBytea;

/**
 * Automatically encrypt/decrypt PII columns stored as BYTEA in PostgreSQL.
 *
 * Define an `$encrypted` array property on the model listing columns
 * that should be transparently encrypted via EncryptedBytea cast.
 */
trait HasEncryptedPii {
    public function initializeHasEncryptedPii(): void {
        $encrypted = $this->getEncryptedColumns();
        $casts = [];
        foreach ($encrypted as $column) {
            $casts[$column] = EncryptedBytea::class;
        }
        $this->mergeCasts($casts);
    }

    /** @return array<int, string> */
    protected function getEncryptedColumns(): array {
        return $this->encrypted;
    }
}
