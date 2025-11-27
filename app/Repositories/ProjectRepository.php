<?php

namespace App\Repositories;

use App\Models\Project;
use Config\Database;
use PDO;

final class ProjectRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM projects_view ORDER BY created_at DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => [
            ...$r,
            'technologies' => json_decode($r['technologies'], true),
            'tags' => json_decode($r['tags'], true),
        ], $rows);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM projects_view')->fetchColumn();
    }
    public function countPublished(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM projects WHERE published_at IS NOT NULL')->fetchColumn();
    }

    public function findById(int $id): ?Project
    {
        $st = $this->db->prepare('SELECT * FROM projects WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function create(array $fields): int
    {
        $cols   = implode(',', array_keys($fields));
        $params = implode(',', array_map(fn($k) => ":$k", array_keys($fields)));

        $sql = "INSERT INTO projects($cols) VALUES($params) RETURNING id";
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

        $sql = "UPDATE projects SET " . implode(',', $set) . " WHERE id=:id";
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM projects WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    public function syncTags(int $projectId, array $tags): void
    {
        // clear old
        $this->db->prepare('DELETE FROM project_tags WHERE project_id=:pid')->execute(['pid' => $projectId]);

        if (!$tags) return;

        $tagRepo = new \App\Repositories\TagRepository();

        foreach ($tags as $t) {
            $found = $tagRepo->findByName($t);
            $tagId = $found ? $found['id'] : $tagRepo->create($t);

            $st = $this->db->prepare('INSERT INTO project_tags(project_id,tag_id) VALUES(:p,:t)');
            $st->execute(['p' => $projectId, 't' => $tagId]);
        }
    }
    public function hasLiked(int $projectId, int $userId): bool
    {
        $st = $this->db->prepare('SELECT 1 FROM project_likes WHERE project_id=:p AND user_id=:u LIMIT 1');
        $st->execute(['p' => $projectId, 'u' => $userId]);
        return (bool) $st->fetchColumn();
    }

    public function like(int $projectId, int $userId): void
    {
        $st = $this->db->prepare('INSERT INTO project_likes(project_id, user_id) VALUES(:p, :u)');
        $st->execute(['p' => $projectId, 'u' => $userId]);
    }

    public function unlike(int $projectId, int $userId): void
    {
        $st = $this->db->prepare('DELETE FROM project_likes WHERE project_id=:p AND user_id=:u');
        $st->execute(['p' => $projectId, 'u' => $userId]);
    }

    private function map(array $r): Project
    {
        return new Project(
            (int)$r['id'],
            $r['title'],
            $r['slug'],
            $r['summary'],
            $r['description'],
            $r['year'] ?? null,
            json_decode($r['technologies'] ?? '[]', true),
            $r['status'],
            $r['cover_asset_id'] ?? null,
            $r['demo_url'],
            $r['repo_url'],
            $r['reviewer_id'] ?? null,
            $r['published_at'] ?? null,
            $r['updated_at'] ?? null,
            $r['version'],
            $r['category_id'] ?? null,
            $r['author_id'] ?? null,
            $r['created_at'],
            $r['likes'] ?? null,
            $r['views'] ?? null,
        );
    }
}
