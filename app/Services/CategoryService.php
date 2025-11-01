<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

final class CategoryService
{
    public function __construct(private ?CategoryRepository $repo = null)
    {
        $this->repo ??= new CategoryRepository();
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

    public function create(string $name, string $type): array
    {
        if ($this->repo->findByName($name))
            return [false, 'Category already registered'];
        $id = $this->repo->create($name, $type);

        if (!$id) {
            return [false, null];
        }

        return [
            true,
            [
                'id'    => $id,
                'name'  => $name,
                'slug'  => strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name))),
                'type'  => $type
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
            'name' => $u->name,
            'slug' => $u->slug,
            'type' => $u->type
        ];
    }

    public function update(int $id, array $payload): bool
    {
        $fields = [];
        if (isset($payload['name']))
            $fields['name'] = (string) $payload['name'];
        if (isset($payload['type']))
            $fields['type'] = (string) $payload['type'];
        if (!$fields)
            return true;
        return $this->repo->update($id, $fields);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
