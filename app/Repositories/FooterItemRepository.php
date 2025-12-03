<?php

namespace App\Repositories;

use App\Models\FooterItem;
use Config\Database;
use PDO;

final class FooterItemRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM footer_view WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM footer_view ORDER BY position LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => [...$r], $rows);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM footer_view')->fetchColumn();
    }

    public function create(array $fields): int
    {
        $cols   = implode(',', array_keys($fields));
        $params = implode(',', array_map(fn($k) => ":$k", array_keys($fields)));

        $sql = "INSERT INTO footer_items($cols) VALUES($params) RETURNING id";
        $st  = $this->db->prepare($sql);
        $st->execute($fields);
        return (int)$st->fetchColumn();
    }

    public function update(int $id, array $fields): bool
    {
        $set = [];
        $params = ['id' => $id];

        foreach ($fields as $k => $v) {
            $set[] = "$k = :$k";
            $params[$k] = $v;
        }

        $sql = 'UPDATE footer_items SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM footer_items WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): FooterItem
    {
        return new FooterItem(
            (int) $r['id'],
            $r['section_id'],
            $r['label'],
            $r['content'],
            $r['url'],
            $r['position']
        );
    }
}
