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
        $st = $this->db->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
        $st->execute(['e' => $email]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
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
        $st = $this->db->prepare('SELECT * FROM users ORDER BY created_at DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function create(string $email, string $name, string $passwordHash): int
    {
        $st = $this->db->prepare('INSERT INTO users(email,name,password_hash) VALUES(:e,:n,:p) RETURNING id');
        $st->execute(['e' => $email, 'n' => $name, 'p' => $passwordHash]);
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
            $r['created_at'],
            $r['updated_at']
        );
    }
}
