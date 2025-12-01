<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\MediaAssetRepository;
use App\Repositories\NewsRepository;
use App\Repositories\TagRepository;
use Exception;

final class NewsService
{
    public function __construct(
        private ?NewsRepository $repo = null,
        private ?CategoryRepository $category = null,
        private ?MediaAssetRepository $media = null,
        private ?TagRepository $tagRepo = null,
    ) {
        $this->repo ??= new NewsRepository();
        $this->category ??= new CategoryRepository();
        $this->media ??= new MediaAssetRepository();
        $this->tagRepo ??= new TagRepository();
    }

    public function paginate(int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = min(100, max(1, $limit));
        $offset = ($page - 1) * $limit;

        $data = $this->repo->list($limit, $offset);
        $total = $this->repo->count();

        return [
            array_map(fn($p) => is_object($p) ? (array) $p : $p, $data),
            [
                'page' => $page,
                'limit' => $limit,
                'total' => $total
            ]
        ];
    }

    public function get(int $id): ?array
    {
        $p = $this->repo->findById($id);
        if (!$p)
            return null;
        $media = null;

        if (!empty($p->cover_asset_id)) {
            $media = $this->media->findById($p->cover_asset_id);
        }
        return [
            ...((array) $p),
            'cover_url' => $media ? $media->url : null,
        ];
    }

    public function create(array $payload): int
    {
        // Validate category
        if (!empty($payload['category_id']) && !$this->category->exists($payload['category_id'])) {
            throw new Exception("Category not found");
        }

        // Validate cover asset
        if (!empty($payload['cover_asset_id']) && !$this->media->exists($payload['cover_asset_id'])) {
            throw new Exception("Cover asset not found");
        }

        // Force JSON encode on attachments
        if (isset($payload['attachments'])) {
            $payload['attachments'] = json_decode($payload['attachments'], true);

            if (!is_array($payload['attachments'])) throw new Exception("attachments must be array");
            $payload['attachments'] = json_encode($payload['attachments']);
        }

        // Author handled in controller
        $tags = $payload['tags'] ?? [];
        unset($payload['tags']);

        $id = $this->repo->create($payload);

        // Upsert tags relations
        $this->repo->syncTags($id, $tags);

        return $id;
    }

    public function update(int $id, array $payload): bool
    {
        // var_dump($payload);
        if (!$this->repo->findById($id)) throw new Exception("News not found");

        if (isset($payload['category_id']) && !$this->category->exists($payload['category_id'])) {
            throw new Exception("Category not found");
        }

        if (isset($payload['cover_asset_id']) && !$this->media->exists($payload['cover_asset_id'])) {
            throw new Exception("Cover asset not found");
        }

        if (isset($payload['attachments'])) {
            $payload['attachments'] = json_decode($payload['attachments'], true);

            if (!is_array($payload['attachments'])) throw new Exception("attachments must be array");
            $payload['attachments'] = json_encode($payload['attachments']);
        }

        $tags = $payload['tags'] ?? null;
        unset($payload['tags']);

        $ok = $this->repo->update($id, $payload);

        if ($tags !== null) {
            $this->repo->syncTags($id, $tags);
        }

        return $ok;
    }

    public function delete(int $id): array
    {
        if (!$this->repo->findById($id)) return [false, 'project not found'];
        return $this->repo->delete($id);
    }
}
