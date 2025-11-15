<?php

namespace App\Repositories;

use App\Models\MediaSocial;
use Config\Database;
use PDO;

final class MediaSocialRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?MediaSocial
    {
        $st = $this->db->prepare('SELECT * FROM media_socials WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM media_socials ORDER BY position DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM media_socials')->fetchColumn();
    }

    public function create(string $platform, string $url, string $icon, int $position): int
    {
        $st = $this->db->prepare('INSERT INTO media_socials(platform, url, icon, position) VALUES(:pl,:u, :i, :p) RETURNING id');
        $st->execute(['pl' => $platform, 'u' => $url, 'i' => $icon, 'p' => $position]);
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

        $sql = 'UPDATE media_socials SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM media_socials WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): MediaSocial
    {
        return new MediaSocial(
            (int) $r['id'],
            $r['platform'],
            $r['url'],
            $r['icon'],
            $r['position'],
        );
    }
}
