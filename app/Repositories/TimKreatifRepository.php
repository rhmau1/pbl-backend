<?php

namespace App\Repositories;

use App\Models\TimKreatif;
use Config\Database;
use PDO;

final class TimKreatifRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?TimKreatif
    {
        $st = $this->db->prepare('SELECT * FROM tim_kreatif WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function exists(int $id): bool
    {
        $st = $this->db->prepare('SELECT * FROM tim_kreatif WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? true : false;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM tim_kreatif ORDER BY position ASC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM tim_kreatif')->fetchColumn();
    }

    public function create(array $fields): int
    {
        $cols   = implode(',', array_keys($fields));
        $params = implode(',', array_map(fn($k) => ":$k", array_keys($fields)));

        $sql = "INSERT INTO tim_kreatif($cols) VALUES($params) RETURNING id";
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

        $sql = 'UPDATE tim_kreatif SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM tim_kreatif WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): TimKreatif
    {
        return new TimKreatif(
            (int) $r['id'],
            $r['name'],
            $r['role'],
            $r['photo_url'],
            json_decode($r['skills'] ?? '[]', true),
            (int) $r['position']
        );
    }
}
