<?php

namespace App\Repositories;

use App\Models\Sejarah;
use Config\Database;
use PDO;

final class SejarahRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?Sejarah
    {
        $st = $this->db->prepare('SELECT * FROM sejarah WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM sejarah ORDER BY title DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM sejarah')->fetchColumn();
    }

    public function create(string $content, string $title): int
    {
        $st = $this->db->prepare('INSERT INTO sejarah(content,title) VALUES(:c,:t) RETURNING id');
        $st->execute(['c' => $content, 't' => $title]);
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
        $set[] = "updated_at = NOW()";

        $sql = 'UPDATE sejarah SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM sejarah WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): Sejarah
    {
        return new Sejarah(
            (int) $r['id'],
            $r['title'],
            $r['content'],
            $r['updated_at']
        );
    }
}
