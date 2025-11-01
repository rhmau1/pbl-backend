<?php

namespace App\Models;

use DateTime;

final class User
{
    public function __construct(
        public ?int $id,
        public string $email,
        public string $name,
        public string $passwordHash,
        public string $role,
        public int $twoFAEnabled,
        public ?string $lastLoginAt,
        public string $createdAt,
        public string $updatedAt
    ) {}
}
