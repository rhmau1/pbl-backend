<?php

namespace App\Repositories;


use App\Models\User;
use Config\Database;
use PDO;

final class UserRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findByEmail(string $email): ?User
    {
        $st = $this->db->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(:e) LIMIT 1');
        $st->execute(['e' => $email]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function updateLastLogin(int $id): bool
    {
        $st = $this->db->prepare('UPDATE users SET last_login_at=NOW() WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    public function updateProfile(int $id, array $fields): bool
    {
        $set = [];
        $params = ['id' => $id];

        foreach ($fields as $k => $v) {
            $set[] = "$k = :$k";
            $params[$k] = $v;
        }

        $set[] = "updated_at = NOW()";

        $sql = 'UPDATE users SET ' . implode(', ', $set) . ' WHERE id = :id';
        $st  = $this->db->prepare($sql);

        return $st->execute($params);
    }

    public function findById(int $id): ?User
    {
        $st = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM users_view ORDER BY created_at DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => [
            ...$r,
            'skills' => json_decode($r['skills'], true),
            'socials' => json_decode($r['socials'], true),
        ], $rows);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users_view')->fetchColumn();
    }

    public function create(string $email, string $name, string $passwordHash, string $roleId): int
    {
        $st = $this->db->prepare('INSERT INTO users(email,name,password_hash,role_id) VALUES(:e,:n,:p,:r) RETURNING id');
        $st->execute(['e' => $email, 'n' => $name, 'p' => $passwordHash, 'r' => $roleId]);
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
        $sql = 'UPDATE users SET ' . implode(',', $set) . ', updated_at=NOW() WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM users WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): User
    {
        return new User(
            (int) $r['id'],
            $r['email'],
            $r['name'],
            $r['password_hash'],
            $r['role_id'],
            $r['two_fa_enabled'],
            $r['last_login_at'],
            $r['created_at'],
            $r['updated_at'],
            json_decode($r['skills'] ?? '[]', true),
            json_decode($r['socials'] ?? '{}', true),
            $r['avatar']
        );
    }
}
