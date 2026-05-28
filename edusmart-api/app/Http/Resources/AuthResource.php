<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Utilisateur
 */
class AuthResource extends JsonResource {
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'role_code' => $this->role_code->value,
            'role_libelle' => $this->role_code->libelle(),
            'est_actif' => $this->est_actif,
            'derniere_connexion' => $this->derniere_connexion?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
