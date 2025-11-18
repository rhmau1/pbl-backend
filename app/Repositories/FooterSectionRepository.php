<?php

namespace App\Repositories;

use App\Models\FooterSection;
use Config\Database;
use PDO;

final class FooterSectionRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?FooterSection
    {
        $st = $this->db->prepare('SELECT * FROM footer_sections WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM footer_sections ORDER BY position LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM footer_sections')->fetchColumn();
    }

    public function create(string $name, int $position): int
    {
        $st = $this->db->prepare('INSERT INTO footer_sections(section_name, position) VALUES(:n, :p) RETURNING id');
        $st->execute(['n' => $name, 'p' => $position]);
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

        $sql = 'UPDATE footer_sections SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM footer_sections WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): FooterSection
    {
        return new FooterSection(
            (int) $r['id'],
            $r['section_name'],
            $r['position']
        );
    }
}
