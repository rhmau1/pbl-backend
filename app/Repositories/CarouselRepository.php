<?php

namespace App\Repositories;

use App\Models\Carousel;
use Config\Database;
use PDO;

final class CarouselRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?Carousel
    {
        $st = $this->db->prepare('SELECT * FROM carousels WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function exists(int $id): bool
    {
        $st = $this->db->prepare('SELECT * FROM carousels WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? true : false;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM carousels ORDER BY position ASC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM carousels')->fetchColumn();
    }

    public function create(string $carousel_url, int $position): int
    {
        $st = $this->db->prepare('INSERT INTO carousels(carousel_url, position) VALUES(:carousel_url, :position) RETURNING id');
        $st->execute(['carousel_url' => $carousel_url, 'position' => $position]);
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

        $sql = 'UPDATE carousels SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM carousels WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    public function getMaxPosition(): int
    {
        $result = $this->db->query('SELECT MAX(position) FROM carousels')->fetchColumn();
        return $result ? (int) $result : 0;
    }

    private function map(array $r): Carousel
    {
        return new Carousel(
            (int) $r['id'],
            $r['carousel_url'],
            (int) $r['position']
        );
    }
}
