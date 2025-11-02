<?php

namespace App\Repositories;

use App\Models\Role;
use Config\Database;
use PDO;

final class RoleRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?Role
    {
        $st = $this->db->prepare('SELECT * FROM roles WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }
    public function findByName(string $name): ?Role
    {
        $st = $this->db->prepare('SELECT * FROM roles WHERE LOWER(name) = LOWER(:n) LIMIT 1');
        $st->execute(['n' => $name]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM roles ORDER BY name DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM roles')->fetchColumn();
    }

    public function create(string $name): int
    {
        $st = $this->db->prepare('INSERT INTO roles(name) VALUES(:n) RETURNING id');
        $st->execute(['n' => $name]);
        return (int) $st->fetchColumn();
    }

    public function update(int $id, string $name): bool
    {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        $sql = 'UPDATE roles SET name = :n WHERE id = :id';
        $st = $this->db->prepare($sql);

        return $st->execute([
            'n'  => $name,
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM roles WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): Role
    {
        return new Role(
            (int) $r['id'],
            $r['name']
        );
    }
}
