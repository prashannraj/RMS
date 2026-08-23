<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
        ]);

        $user->assignRole('staff');

        return $user;
    }

    public function login(User $user, string $ip): array
    {
        $user->updateLastLogin($ip);

        $token = $user->createToken(
            name: 'auth-token',
            abilities: ['*'],
            expiresAt: now()->addDays(7)
        );

        return [
            'user' => $user->load('roles'),
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }
}