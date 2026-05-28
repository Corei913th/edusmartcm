<?php

namespace App\Services;

use App\Models\Utilisateur;
use App\ValueObjects\AuthenticationResult;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService {
    public function login(string $email, string $password): AuthenticationResult {
        $user = Utilisateur::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (!$user->est_actif) {
            throw ValidationException::withMessages([
                'email' => ['Ce compte est désactivé.'],
            ]);
        }

        $user->markAsConnected();

        return new AuthenticationResult($user->issueToken(), $user);
    }

    /**
     * @param array{nom: string, prenom: string, email: string, password: string, telephone?: string|null, role_code?: string|null} $data
     */
    public function register(array $data): AuthenticationResult {
        $user = runTransaction(function () use ($data): Utilisateur {
            return Utilisateur::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'] ?? null,
                'role_code' => $data['role_code'] ?? Utilisateur::ROLE_DEFAULT,
                'password_hash' => Hash::make($data['password'], ['rounds' => 12]),
                'est_actif' => true,
            ]);
        }, 'AuthService::register');

        return new AuthenticationResult($user->issueToken(), $user);
    }

    public function logout(Utilisateur $user): void {
        $user->currentAccessToken()->delete();
    }

    public function me(Utilisateur $user): Utilisateur {
        return $user;
    }
}
