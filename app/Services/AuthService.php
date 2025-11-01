<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(private ?UserRepository $repo = null)
    {
        $this->repo ??= new UserRepository();
    }

    public function issueToken(int $userId): string
    {
        $cfg = require __DIR__ . '/../../config/jwt.php';

        $now = time();

        $payload = [
            'iss' => $cfg['issuer'],
            'aud' => $cfg['audience'],
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + ($cfg['ttl'] ?? 3600),
            'user' => [
                'id' => $userId,
            ],
        ];

        return JWT::encode($payload, $cfg['secret'], 'HS256');
    }

    public function verifyToken(string $token): bool
    {
        $cfg = require __DIR__ . '/../../config/jwt.php';

        try {
            JWT::decode($token, new Key($cfg['secret'], 'HS256'));
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }


    public function login(string $email, string $password): array
    {
        $u = $this->repo->findByEmail($email);
        if (!$u || !password_verify($password, $u->passwordHash)) {
            return [false, 'Invalid credentials'];
        }

        $token = $this->issueToken($u->id);
        $this->repo->updateLastLogin($u->id);

        return [
            true,
            [
                'token' => $token,
                'user' => [
                    'id' => $u->id,
                    'email' => $u->email,
                    'name' => $u->name,
                    'role' => $u->role,
                    'lastLoginAt' => $u->lastLoginAt,
                ],
            ],
        ];
    }

    public function register(string $email, string $name, string $password, string $role): array
    {
        if ($this->repo->findByEmail($email))
            return [false, 'Email already registered'];
        $id = $this->repo->create($email, $name, password_hash($password, PASSWORD_BCRYPT), $role);
        return [true, ['id' => $id, 'name' => $name, 'email' => $email, 'role' => $role]];
    }
}
