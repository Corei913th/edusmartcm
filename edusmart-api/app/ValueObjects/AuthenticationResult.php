<?php

namespace App\ValueObjects;

use App\Models\Utilisateur;

/**
 * @readonly
 */
class AuthenticationResult {
    public function __construct(
        public string $token,
        public Utilisateur $user,
    ) {}
}
