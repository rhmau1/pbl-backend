<?php

namespace App\Repositories;

use App\Models\VisiMisi;
use Config\Database;
use PDO;

final class VisiMisiRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?VisiMisi
    {
        $st = $this->db->prepare('SELECT * FROM visi_misi WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM visi_misi ORDER BY position DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM visi_misi')->fetchColumn();
    }

    public function create(string $content, string $type, int $position): int
    {
        $st = $this->db->prepare('INSERT INTO visi_misi(content,position,type) VALUES(:c,:p,:t) RETURNING id');
        $st->execute(['c' => $content, 'p' => $position, 't' => $type]);
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

        $sql = 'UPDATE visi_misi SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM visi_misi WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): VisiMisi
    {
        return new VisiMisi(
            (int) $r['id'],
            $r['type'],
            $r['content'],
            $r['position'],
            $r['updated_at']
        );
    }
}
