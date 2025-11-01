<?php
namespace App\Models;

final class User
{
    public function __construct(
        public ?int $id,
        public string $email,
        public string $name,
        public string $passwordHash,
        public string $createdAt,
        public string $updatedAt
    ) {
    }
}
