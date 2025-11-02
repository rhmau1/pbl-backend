<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\RolePermissionRepository;

final class RolePermissionService
{
    public function __construct(private ?RolePermissionRepository $repo = null, private ?RoleRepository $roleRepo = null)
    {
        $this->repo ??= new RolePermissionRepository();
        $this->roleRepo ??= new RoleRepository();
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

    public function create(int $roleId, string $permission): array
    {
        $role = $this->roleRepo->findById($roleId);
        if (!$role) {
            return [false, 'Role ID not found'];
        }

        $id = $this->repo->create($roleId, $permission);

        return [true, [
            'id'         => $id,
            'role_id'    => $roleId,
            'permission' => $permission
        ]];
    }

    public function get(int $id): ?array
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return null;

        return [
            'id' => $u->id,
            'role_id'    => $u->roleId,
            'permission' => $u->permission
        ];
    }

    public function update(int $id, int $roleId, string $permission): array
    {
        $role = $this->roleRepo->findById($roleId);
        if (!$role) {
            return [false, 'Role ID not found'];
        }
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'Role Permission ID not found'];
        }

        $success = $this->repo->update($id, $roleId, $permission);
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
