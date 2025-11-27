<?php

namespace App\Requests;

final class CommentRequest
{
    public static function validate(array $in): array
    {
        $entity_type = isset($in['entity_type']) ? trim((string) $in['entity_type']) : '';
        $entity_id = isset($in['entity_id']) ? (int) $in['entity_id'] : null;
        $author = isset($in['author']) ? (int) $in['author'] : null;
        $email = isset($in['email']) ? trim((string) $in['email']) : '';
        $rating = isset($in['rating']) ? (int) $in['rating'] : null;
        $content = isset($in['content']) ? trim((string) $in['content']) : '';
        $status = isset($in['status']) ? trim((string) $in['status']) : 'pending';

        $errors = [];

        if ($entity_type === '') $errors['entity_type'] = 'Entity type required';
        if ($entity_id === null || $entity_id <= 0) $errors['entity_id'] = 'Valid entity ID required';
        if ($author === null || $author <= 0) $errors['author'] = 'Valid author ID required';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';
        if ($rating === null || $rating < 1 || $rating > 5) $errors['rating'] = 'Rating must be 1-5';
        if ($content === '') $errors['content'] = 'Content required';
        if (!in_array($status, ['pending', 'approved', 'rejected'])) $errors['status'] = 'Invalid status';

        return [
            $errors === [],
            $errors,
            array_filter([
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'author' => $author,
                'email' => $email,
                'rating' => $rating,
                'content' => $content,
                'status' => $status
            ], fn($v) => $v !== null)
        ];
    }
}
