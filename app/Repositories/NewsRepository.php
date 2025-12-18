<?php

namespace App\Repositories;

use App\Models\News;
use Config\Database;
use PDO;

final class NewsRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM news_view ORDER BY created_at DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => [
            ...$r,
            'attachments' => !empty($r['attachments'])
                ? json_decode($r['attachments'], true)
                : [],
            'tags' => !empty($r['tags'])
                ? json_decode($r['tags'], true)
                : [],
        ], $rows);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM news_view')->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM news_view WHERE news_id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        if (isset($row['attachments'])) {
            if (is_string($row['attachments'])) {
                $row['attachments'] = json_decode($row['attachments'], true) ?: [];
            }
        } else {
            $row['attachments'] = [];
        }

        if (isset($row['tags'])) {
            if (is_string($row['tags'])) {
                $row['tags'] = json_decode($row['tags'], true) ?: [];
            }
        } else {
            $row['tags'] = [];
        }

        return $row;
    }

    public function create(array $fields): int
    {
        $cols   = implode(',', array_keys($fields));
        $params = implode(',', array_map(fn($k) => ":$k", array_keys($fields)));

        $sql = "INSERT INTO news($cols) VALUES($params) RETURNING id";
        $st  = $this->db->prepare($sql);
        $st->execute($fields);
        return (int)$st->fetchColumn();
    }

    public function update(int $id, array $fields): bool
    {
        $set = [];
        $params = ['id' => $id];

        foreach ($fields as $k => $v) {
            $set[] = "$k=:$k";
            $params[$k] = $v;
        }

        $set[] = "updated_at = NOW()";

        $sql = "UPDATE news SET " . implode(',', $set) . " WHERE id=:id";
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): array
    {
        try {
            $st = $this->db->prepare('CALL delete_news(:id)');
            $st->execute(['id' => $id]);

            return [true, $id];
        } catch (\PDOException $e) {
            return [
                false,
                $e->getMessage()
            ];
        }
    }

    public function syncTags(int $newsId, array $tags): void
    {
        // clear old
        $this->db->prepare('DELETE FROM news_tags WHERE news_id=:nid')->execute(['nid' => $newsId]);

        if (!$tags) return;

        $tagRepo = new \App\Repositories\TagRepository();

        foreach ($tags as $t) {
            $found = $tagRepo->findByName($t);
            $tagId = $found ? $found['id'] : $tagRepo->create($t);

            $st = $this->db->prepare('INSERT INTO news_tags(news_id,tag_id) VALUES(:n,:t)');
            $st->execute(['n' => $newsId, 't' => $tagId]);
        }
    }

    private function map(array $r): News
    {
        return new News(
            (int)$r['id'],
            $r['title'],
            $r['slug'],
            $r['summary'],
            $r['content'],
            $r['type'],
            $r['date'],
            json_decode($r['attachments'] ?? '[]', true),
            $r['status'],
            $r['cover_asset_id'] ?? null,
            $r['reviewer_id'] ?? null,
            $r['published_at'] ?? null,
            $r['updated_at'] ?? null,
            $r['version'],
            $r['author_id'] ?? null,
            $r['created_at']
        );
    }
}
