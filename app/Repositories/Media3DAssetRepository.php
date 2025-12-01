<?php

namespace App\Repositories;

use Config\Database;
use App\Models\MediaAsset;
use PDO;

final class Media3DAssetRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare("
            INSERT INTO media_3d_views(type, url, alt_text, caption, bytes, checksum, owner_id, visibility)
            VALUES(:type, :url, :alt, :caption, :bytes, :checksum, :owner, :vis)
            RETURNING id
        ");

        $st->execute($data);
        return (int) $st->fetchColumn();
    }

    public function findByChecksum(string $checksum): ?MediaAsset
    {
        $st = $this->db->prepare("SELECT * FROM media_3d_views WHERE checksum = :c LIMIT 1");
        $st->execute(['c' => $checksum]);

        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }
    public function findById(int $id): ?MediaAsset
    {
        $st = $this->db->prepare("SELECT * FROM media_3d_views WHERE id = :id");
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }
    public function exists(int $id): bool
    {
        $st = $this->db->prepare("SELECT * FROM media_3d_views WHERE id = :id");
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? true : false;
    }

    public function filter(int $limit, int $offset, ?int $visibility, ?int $owner): array
    {
        $sql = "SELECT * FROM media_3d_views WHERE 1=1";
        $params = [];

        if ($visibility) {
            $sql .= " AND visibility = :v";
            $params['v'] = $visibility;
        }
        if ($owner) {
            $sql .= " AND owner_id = :o";
            $params['o'] = $owner;
        }

        $sql .= " LIMIT :l OFFSET :f";
        $st = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }

        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':f', $offset, PDO::PARAM_INT);

        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare("DELETE FROM media_3d_views WHERE id = :id");
        return $st->execute(['id' => $id]);
    }

    public function updateVisibility(int $id, int $visibility): bool
    {
        $st = $this->db->prepare("UPDATE media_3d_views SET visibility = :v WHERE id = :id");
        return $st->execute(['v' => $visibility, 'id' => $id]);
    }

    public function updateMetadata(int $id, ?string $alt, ?string $caption): bool
    {
        $st = $this->db->prepare("UPDATE media_3d_views SET alt_text = :a, caption = :c WHERE id = :id");
        return $st->execute(['a' => $alt, 'c' => $caption, 'id' => $id]);
    }

    private function map(array $r): MediaAsset
    {
        return new MediaAsset(
            (int) $r['id'],
            $r['type'],
            $r['url'],
            $r['alt_text'],
            $r['caption'],
            (int) $r['bytes'],
            $r['checksum'],
            (int) $r['owner_id'],
            $r['visibility'],
            $r['created_at']
        );
    }
}
