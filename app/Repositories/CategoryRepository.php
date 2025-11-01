<?php

namespace App\Repositories;

use App\Models\Category;
use Config\Database;
use PDO;

final class CategoryRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?Category
    {
        $st = $this->db->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }
    public function findByName(string $name): ?Category
    {
        $st = $this->db->prepare('SELECT * FROM categories WHERE LOWER(name) = LOWER(:n) LIMIT 1');
        $st->execute(['n' => $name]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM categories ORDER BY name DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    }

    public function create(string $name, string $type): int
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));
        $st = $this->db->prepare('INSERT INTO categories(name,slug,type) VALUES(:n,:s,:t) RETURNING id');
        $st->execute(['n' => $name, 's' => $slug, 't' => $type]);
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
        $sql = 'UPDATE categories SET ' . implode(',', $set) . ', WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM categories WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): Category
    {
        return new Category(
            (int) $r['id'],
            $r['name'],
            $r['slug'],
            $r['type']
        );
    }
}
