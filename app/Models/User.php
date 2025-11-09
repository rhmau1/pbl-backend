<?php

namespace App\Models;

final class User
{
    public function __construct(
        public ?int $id,
        public string $email,
        public string $name,
        public string $passwordHash,
        public int $roleId,
        public int $twoFAEnabled,
        public ?string $lastLoginAt,
        public string $createdAt,
        public string $updatedAt,
        public ?array $skills,
        public ?array $socials,
        public ?string $avatar
    ) {}
}
