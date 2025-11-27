<?php

namespace App\Models;

final class Comment
{
    public function __construct(
        public ?int $id,
        public string $entity_type,
        public int $entity_id,
        public int $author,
        public string $email,
        public int $rating,
        public string $content,
        public string $status,
        public string $created_at
    ) {}
}
