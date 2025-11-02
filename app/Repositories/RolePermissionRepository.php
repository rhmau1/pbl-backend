<?php

namespace App\Repositories;

use App\Models\RolePermission;
use Config\Database;
use PDO;

final class RolePermissionRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?RolePermission
    {
        $st = $this->db->prepare('SELECT * FROM role_permissions WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM role_permissions LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM role_permissions')->fetchColumn();
    }

    public function create(int $roleId, string $permission): int
    {
        $st = $this->db->prepare(
            'INSERT INTO role_permissions(role_id, permission) VALUES(:rid, :p) RETURNING id'
        );
        $st->execute([
            'rid' => $roleId,
            'p'   => $permission
        ]);
        return (int) $st->fetchColumn();
    }

    public function update(int $id, int $roleId, string $permission): bool
    {
        $permission = trim($permission);
        if ($permission === '') {
            return false;
        }

        $sql = 'UPDATE role_permissions SET permission = :p, role_id = :rid WHERE id = :id';
        $st = $this->db->prepare($sql);

        return $st->execute([
            'p'   => $permission,
            'rid' => $roleId,
            'id'  => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM role_permissions WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): RolePermission
    {
        return new RolePermission(
            (int) $r['id'],
            (int) $r['role_id'],
            $r['permission']
        );
    }
}
