<?php

namespace App\Services;

use App\Repositories\SejarahRepository;

final class SejarahService
{
    public function __construct(private ?SejarahRepository $repo = null)
    {
        $this->repo ??= new SejarahRepository();
    }

    public function paginate(int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = min(100, max(1, $limit));
        $offset = ($page - 1) * $limit;

        $data = $this->repo->list($limit, $offset);
        $total = $this->repo->count();

        $data = array_map(function ($u) {
            if (is_object($u)) {
                $u = (array) $u;
            }
            return $u;
        }, $data);

        return [
            $data,
            [
                'page' => $page,
                'limit' => $limit,
                'total' => $total
            ]
        ];
    }

    public function create(string $content, string $title): array
    {
        $id = $this->repo->create($content, $title);

        if (!$id) {
            return [false, null];
        }

        return [
            true,
            [
                'id'    => $id,
                'content'  => $content,
                'title'  => $title,
            ]
        ];
    }
    public function get(int $id): ?array
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return null;

        return [
            'id' => $u->id,
            'content' => $u->content,
            'title' => $u->title,
        ];
    }

    public function update(int $id, array $payload): bool
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return false;
        return $this->repo->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return false;
        return $this->repo->delete($id);
    }
}
