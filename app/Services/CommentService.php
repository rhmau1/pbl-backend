<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;

final class CommentService
{
    public function __construct(private ?CommentRepository $repo = null, private ?UserRepository $userRepo = null)
    {
        $this->repo ??= new CommentRepository();
        $this->userRepo ??= new UserRepository();
    }

    public function paginate(int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = min(100, max(1, $limit));
        $offset = ($page - 1) * $limit;

        $data = $this->repo->list($limit, $offset);
        $total = $this->repo->count();

        return [
            array_map(fn($comment) => is_object($comment) ? (array) $comment : $comment, $data),
            [
                'page' => $page,
                'limit' => $limit,
                'total' => $total
            ]
        ];
    }

    public function get(int $id): ?array
    {
        $comment = $this->repo->findById($id);
        return $comment ? (array) $comment : null;
    }

    public function create(array $payload): array
    {
        // Validate author exists
        $authorExists = $this->userRepo->findById($payload['author']);
        if (!$authorExists) {
            return [false, 'Author (user) not found'];
        }

        $id = $this->repo->create(
            $payload['entity_type'],
            $payload['entity_id'],
            $payload['author'],
            $payload['email'],
            $payload['rating'],
            $payload['content'],
            $payload['status'] ?? 'pending'
        );

        if (!$id) {
            return [false, 'Failed to create comment'];
        }

        $created = $this->repo->findById($id);
        return [true, (array) $created];
    }

    public function update(int $id, array $payload): array
    {
        $comment = $this->repo->findById($id);
        if (!$comment) {
            return [false, 'Comment not found'];
        }

        // Validate author if being updated
        if (isset($payload['author'])) {
            $authorExists = $this->userRepo->findById($payload['author']);
            if (!$authorExists) {
                return [false, 'Author (user) not found'];
            }
        }

        $success = $this->repo->update($id, $payload);
        if (!$success) {
            return [false, 'Update failed'];
        }

        return [true, null];
    }

    public function delete(int $id): bool
    {
        $comment = $this->repo->findById($id);
        if (!$comment) {
            return false;
        }

        return $this->repo->delete($id);
    }
}
