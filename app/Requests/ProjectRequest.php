<?php

namespace App\Requests;

final class ProjectRequest
{
    public static function validateCreate(array $in): array
    {
        $title = isset($in['title']) ? trim((string)$in['title']) : '';
        $summary = isset($in['summary']) ? trim((string)$in['summary']) : '';
        $description = isset($in['description']) ? trim((string)$in['description']) : '';
        $status = isset($in['status']) ? trim((string)$in['status']) : 'draft';


        $errors = [];
        if ($title === '') $errors['title'] = 'Title required';
        if ($summary === '') $errors['summary'] = 'Summary required';
        if ($description === '') $errors['description'] = 'Description required';


        // technologies must be array if provided
        $technologies = $in['technologies'] ?? null;
        if ($technologies !== null && is_string($technologies)) {
            $technologies = json_decode($technologies, true);
        }

        // tags must be array of strings if provided
        $tags = $in['tags'] ?? null;
        if ($tags !== null && is_string($tags)) {
            $tags = json_decode($tags, true);
        }
        if ($tags !== null && !is_array($tags)) {
            $errors['tags'] = 'Tags must be an array';
        }


        // year if provided must be int
        $year = isset($in['year']) ? (int)$in['year'] : null;


        $payload = array_filter([
            'title' => $title,
            'slug' => strtolower(str_replace(' ', '-', $title)),
            'summary' => $summary,
            'description' => $description,
            'status' => $status,
            'year' => $year,
            'technologies' => $technologies !== null ? json_encode($technologies) : null,
            'demo_url' => $in['demo_url'] ?? null,
            'repo_url' => $in['repo_url'] ?? null,
            'reviewer_id' => $in['reviewer_id'] ?? null,
            'version' => $in['version'] ?? null,
            'category_id' => $in['category_id'] ?? null,
            'cover_asset_id' => $in['cover_asset_id'] ?? null,
            'tags' => $tags
        ], fn($v) => $v !== null);


        return [$errors === [], $errors, $payload];
    }


    public static function validateUpdate(array $in): array
    {
        $errors = [];


        if (isset($in['title']) && trim((string)$in['title']) === '') {
            $errors['title'] = 'Title cannot be empty';
        }

        $technologies = $in['technologies'] ?? null;
        if ($technologies !== null && is_string($technologies)) {
            $technologies = json_decode($technologies, true);
        }

        $tags = $in['tags'] ?? null;
        if ($tags !== null && is_string($tags)) {
            $tags = json_decode($tags, true);
        }
        if ($tags !== null && !is_array($tags)) {
            $errors['tags'] = 'Tags must be an array';
        }

        $payload = array_filter([
            'title' => $in['title'] ?? null,
            'slug' => isset($in['title']) ? strtolower(str_replace(' ', '-', $in['title'])) : null,
            'summary' => $in['summary'] ?? null,
            'description' => $in['description'] ?? null,
            'status' => $in['status'] ?? null,
            'year' => isset($in['year']) ? (int)$in['year'] : null,
            'technologies' => $technologies !== null ? json_encode($technologies) : null,
            'demo_url' => $in['demo_url'] ?? null,
            'repo_url' => $in['repo_url'] ?? null,
            'reviewer_id' => $in['reviewer_id'] ?? null,
            'version' => $in['version'] ?? null,
            'category_id' => $in['category_id'] ?? null,
            'cover_asset_id' => $in['cover_asset_id'] ?? null,
            'tags' => $tags
        ], fn($v) => $v !== null);


        return [$errors === [], $errors, $payload];
    }
}
