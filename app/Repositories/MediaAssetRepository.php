<?php

namespace App\Repositories;

use Config\Database;
use App\Models\MediaAsset;
use PDO;

final class MediaAssetRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare("
            INSERT INTO media_assets(type, url, alt_text, caption, bytes, checksum, owner_id, visibility)
            VALUES(:type, :url, :alt, :caption, :bytes, :checksum, :owner, :vis)
            RETURNING id
        ");

        $st->execute($data);
        return (int) $st->fetchColumn();
    }

    public function findByChecksum(string $checksum): ?MediaAsset
    {
        $st = $this->db->prepare("SELECT * FROM media_assets WHERE checksum = :c LIMIT 1");
        $st->execute(['c' => $checksum]);

        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }
    public function findById(int $id): ?MediaAsset
    {
        $st = $this->db->prepare("SELECT * FROM media_assets WHERE id = :id");
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function filter(int $limit, int $offset, ?string $type, ?int $visibility, ?int $owner): array
    {
        $sql = "SELECT * FROM media_assets WHERE 1=1";
        $params = [];

        if ($type) {
            $sql .= " AND type = :t";
            $params['t'] = $type;
        }
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
        $st = $this->db->prepare("DELETE FROM media_assets WHERE id = :id");
        return $st->execute(['id' => $id]);
    }

    public function updateVisibility(int $id, int $visibility): bool
    {
        $st = $this->db->prepare("UPDATE media_assets SET visibility = :v WHERE id = :id");
        return $st->execute(['v' => $visibility, 'id' => $id]);
    }

    public function updateMetadata(int $id, ?string $alt, ?string $caption): bool
    {
        $st = $this->db->prepare("UPDATE media_assets SET alt_text = :a, caption = :c WHERE id = :id");
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
