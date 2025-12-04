<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

final class UserService
{
    public function __construct(private ?UserRepository $repo = null, private ?RoleRepository $roleRepo = null)
    {
        $this->repo ??= new UserRepository();
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
            unset($u['passwordHash'], $u['password_hash']);
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

    public function get(int $id): ?array
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return null;
        unset($u['passwordHash'], $u['password_hash']);

        return (array)$u;
    }

    public function update(int $id, array $payload): bool
    {
        $fields = [];
        if (isset($payload['name']))
            $fields['name'] = (string) $payload['name'];
        if (isset($payload['password']))
            $fields['password_hash'] = password_hash((string) $payload['password'], PASSWORD_BCRYPT);
        if (!$fields)
            return true;
        return $this->repo->update($id, $fields);
    }

    public function updateProfile(int $id, array $payload): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'User not found'];
        }

        if (isset($payload['skills'])) {
            $payload['skills'] = json_encode($payload['skills']);
        }

        if (isset($payload['socials'])) {
            $payload['socials'] = json_encode($payload['socials']);
        }

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {

            $folder = "public/avatars/";
            if (!file_exists($folder))
                mkdir($folder, 0777, true);

            $hash = md5_file($_FILES['avatar']['tmp_name']);
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = $hash . "." . strtolower($ext);
            $targetPath = $folder . $filename;

            // Reuse if exists
            if (!file_exists($targetPath)) {
                move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath);
            }

            $payload['avatar'] = 'uploads/avatars/' . $filename;
        }

        $success = $this->repo->updateProfile($id, $payload);

        if (!$success) {
            return [false, 'Update failed'];
        }

        $updated = $this->repo->findById($id);

        return [true, $updated];
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
