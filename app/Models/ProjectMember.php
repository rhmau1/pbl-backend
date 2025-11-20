<?php

namespace App\Models;

final class ProjectMember
{
    public function __construct(
        public int $project_id,
        public int $member_id,
        public string $role
    ) {}
}
