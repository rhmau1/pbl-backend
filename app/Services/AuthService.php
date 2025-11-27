<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(private ?UserRepository $repo = null, private ?RoleRepository $roleRepo = null)
    {
        $this->repo ??= new UserRepository();
        $this->roleRepo ??= new RoleRepository();
    }

    public function issueToken(int $userId, int $roleId): string
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
                'role_id' => $roleId
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
        if (!$u || !password_verify($password, $u->password_hash)) {
            return [false, 'Invalid credentials'];
        }

        $token = $this->issueToken($u->id, $u->role_id);
        $this->repo->updateLastLogin($u->id);

        return [
            true,
            [
                'token' => $token,
                'user' => [
                    'id' => $u->id,
                    'email' => $u->email,
                    'name' => $u->name,
                    'role_id' => $u->role_id,
                    'last_login_at' => $u->last_login_at,
                    'avatar' => $u ->avatar
                ],
            ],
        ];
    }

    public function register(string $email, string $name, string $password, string $roleId): array
    {
        if ($this->repo->findByEmail($email))
            return [false, 'Email already registered'];
        $role = $this->roleRepo->findById($roleId);
        if (!$role) {
            return [false, 'Role ID not found'];
        }
        $id = $this->repo->create($email, $name, password_hash($password, PASSWORD_BCRYPT), $roleId);
        return [true, ['id' => $id, 'name' => $name, 'email' => $email, 'role_id' => $roleId]];
    }
}
