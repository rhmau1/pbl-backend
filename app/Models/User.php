<?php

namespace App\Models;

final class User
{
    public function __construct(
        public ?int $id,
        public string $email,
        public string $name,
        public string $password_hash,
        public int $role_id,
        public int $two_fa_enabled,
        public ?string $last_login_at,
        public string $created_at,
        public string $updated_at,
        public ?array $skills,
        public ?array $socials,
        public ?string $avatar
    ) {}
}
