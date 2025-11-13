<?php

namespace App\Models;

final class News
{
    public function __construct(
        public ?int $id,
        public string $title,
        public ?string $slug,
        public string $summary,
        public string $content,
        public string $type,
        public string $date,
        public ?array $attachments,
        public string $status,
        public ?int $cover_asset_id,
        public ?int $reviewer_id,
        public ?string $published_at,
        public ?string $updated_at,
        public ?string $version,
        public ?int $author_id,
        public string $created_at,
    ) {}
}
