<?php

namespace App\Models;

final class Project
{
    public function __construct(
        public ?int $id,
        public string $title,
        public ?string $slug,
        public string $summary,
        public string $description,
        public ?int $year,
        public ?array $technologies,
        public string $status,
        public ?int $cover_asset_id,
        public ?string $demo_url,
        public ?string $repo_url,
        public ?int $reviewer_id,
        public ?string $published_at,
        public ?string $updated_at,
        public ?string $version,
        public ?int $category_id,
        public ?int $author_id,
        public string $created_at,
        public ?int $likes,
        public ?int $views
    ) {}
}
