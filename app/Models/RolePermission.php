<?php

namespace App\Models;

final class RolePermission
{
    public function __construct(
        public ?int $id,
        public int $roleId,
        public string $permission
    ) {}
}
