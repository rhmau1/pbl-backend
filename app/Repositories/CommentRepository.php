<?php

namespace App\Repositories;

use App\Models\Comment;
use Config\Database;
use PDO;

final class CommentRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?Comment
    {
        $st = $this->db->prepare('SELECT * FROM comments WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function findByEntity(string $entityType, int $entityId): array
    {
        $st = $this->db->prepare('SELECT * FROM comments WHERE entity_type = :et AND entity_id = :ei ORDER BY created_at DESC');
        $st->execute(['et' => $entityType, 'ei' => $entityId]);
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM comments ORDER BY created_at DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM comments')->fetchColumn();
    }

    public function create(string $entityType, int $entityId, int $author, string $email, int $rating, string $content, string $status = 'pending'): int
    {
        $st = $this->db->prepare('INSERT INTO comments(entity_type, entity_id, author, email, rating, content, status) VALUES(:et, :ei, :a, :e, :r, :c, :s) RETURNING id');
        $st->execute([
            'et' => $entityType,
            'ei' => $entityId,
            'a' => $author,
            'e' => $email,
            'r' => $rating,
            'c' => $content,
            's' => $status
        ]);
        return (int) $st->fetchColumn();
    }

    public function update(int $id, array $fields): bool
    {
        $set = [];
        $params = ['id' => $id];

        foreach ($fields as $k => $v) {
            $set[] = "$k = :$k";
            $params[$k] = $v;
        }

        $sql = 'UPDATE comments SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM comments WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    public function findByAuthor(int $authorId): array
    {
        $st = $this->db->prepare('SELECT * FROM comments WHERE author = :a ORDER BY created_at DESC');
        $st->execute(['a' => $authorId]);
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByStatus(string $status): array
    {
        $st = $this->db->prepare('SELECT * FROM comments WHERE status = :s ORDER BY created_at DESC');
        $st->execute(['s' => $status]);
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    private function map(array $r): Comment
    {
        return new Comment(
            (int) $r['id'],
            $r['entity_type'],
            (int) $r['entity_id'],
            (int) $r['author'],
            $r['email'],
            (int) $r['rating'],
            $r['content'],
            $r['status'],
            $r['created_at']
        );
    }
}
