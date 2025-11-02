<?php

namespace App\Services;

use App\Repositories\RoleRepository;

final class RoleService
{
    public function __construct(private ?RoleRepository $repo = null)
    {
        $this->repo ??= new RoleRepository();
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

    public function create(string $name): array
    {
        if ($this->repo->findByName($name))
            return [false, 'Role already registered'];
        $id = $this->repo->create($name);

        if (!$id) {
            return [false, null];
        }

        return [
            true,
            [
                'id'    => $id,
                'name'  => $name
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
            'name' => $u->name
        ];
    }

    public function update(int $id, string $name): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'Role ID not found'];
        }

        $success = $this->repo->update($id, $name);
        if (!$success) {
            return [false, 'Database update failed'];
        }

        return [true, 'Role updated successfully'];
    }

    public function delete(int $id): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'Role ID not found'];
        }

        $success = $this->repo->delete($id);
        if (!$success) {
            return [false, 'Database delete failed'];
        }

        return [true, 'Role deleted successfully'];
    }
}
