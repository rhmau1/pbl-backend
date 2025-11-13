<?php

namespace App\Requests;

use Exception;

final class NewsRequest
{
    public static function validateCreate(array $in): array
    {
        $title = isset($in['title']) ? trim((string)$in['title']) : '';
        $summary = isset($in['summary']) ? trim((string)$in['summary']) : '';
        $content = isset($in['content']) ? trim((string)$in['content']) : '';
        $status = isset($in['status']) ? trim((string)$in['status']) : 'draft';
        $date = isset($in['date']) ? trim((string)$in['date']) : '';
        $type = isset($in['type']) ? trim((string) $in['type']) : 'news';
        if (!in_array($type, ["news", "event"])) {
            throw new Exception("Invalid type (news or event only)");
        }

        $errors = [];
        if ($title === '') $errors['title'] = 'Title required';
        if ($summary === '') $errors['summary'] = 'Summary required';
        if ($content === '') $errors['content'] = 'content required';
        if ($date === '') $errors['date'] = 'date required';


        // attachments must be array if provided
        $attachments = $in['attachments'] ?? null;
        if ($attachments !== null && is_string($attachments)) {
            $attachments = json_decode($attachments, true);
        }

        // tags must be array of strings if provided
        $tags = $in['tags'] ?? null;
        if ($tags !== null && is_string($tags)) {
            $tags = json_decode($tags, true);
        }
        if ($tags !== null && !is_array($tags)) {
            $errors['tags'] = 'Tags must be an array';
        }

        $payload = array_filter([
            'title' => $title,
            'slug' => strtolower(str_replace(' ', '-', $title)),
            'summary' => $summary,
            'content' => $content,
            'status' => $status,
            'type' => $type,
            'date' => $date,
            'attachments' => $attachments !== null ? json_encode($attachments) : null,
            'reviewer_id' => $in['reviewer_id'] ?? null,
            'version' => $in['version'] ?? null,
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

        $attachments = $in['attachments'] ?? null;
        if ($attachments !== null && is_string($attachments)) {
            $attachments = json_decode($attachments, true);
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
            'content' => $in['content'] ?? null,
            'status' => $in['status'] ?? null,
            'type' => $in['type'] ?? null,
            'date' => $in['date'] ?? null,
            'attachments' => $attachments !== null ? json_encode($attachments) : null,
            'reviewer_id' => $in['reviewer_id'] ?? null,
            'version' => $in['version'] ?? null,
            'cover_asset_id' => $in['cover_asset_id'] ?? null,
            'tags' => $tags
        ], fn($v) => $v !== null);


        return [$errors === [], $errors, $payload];
    }
}
