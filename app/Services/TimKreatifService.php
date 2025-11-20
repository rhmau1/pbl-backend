<?php

namespace App\Services;

use App\Repositories\TimKreatifRepository;

final class TimKreatifService
{
    public function __construct(private ?TimKreatifRepository $repo = null)
    {
        $this->repo ??= new TimKreatifRepository();
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

    public function create(array $payload): array
    {
        if (isset($payload['photo']) && is_array($payload['photo'])) {

            $folder = "public/timKreatif/";
            if (!file_exists($folder))
                mkdir($folder, 0777, true);

            $hash = md5_file($payload['photo']['tmp_name']);
            $ext = pathinfo($payload['photo']['name'], PATHINFO_EXTENSION);
            $filename = $hash . "." . strtolower($ext);
            $targetPath = $folder . $filename;

            // Reuse if exists
            if (!file_exists($targetPath)) {
                move_uploaded_file($payload['photo']['tmp_name'], $targetPath);
            }

            $payload['photo_url'] = $targetPath;
        } else {
            $payload['photo_url'] = null;
        }

        unset($payload['photo']);
        if (isset($payload['skills']) && is_array($payload['skills'])) {
            $payload['skills'] = json_encode($payload['skills']);
        }
        $id = $this->repo->create($payload);

        return [
            true,
            [
                'id' => $id,
                'name' => $payload['name'],
                'role' => $payload['role'],
                'photo_url' => $payload['photo_url'],
                'skills' => json_decode($payload['skills'], true),
                'position' => $payload['position']
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
            'role' => $u->role,
            'photo_url' => $u->photo_url,
            'skills' => $u->skills,
            'position' => $u->position
        ];
    }

    public function update(int $id, array $payload): array
    {
        $existing = $this->repo->findById($id);
        if (!$existing) {
            return [false, 'TimKreatif not found'];
        }

        $photoUrl = $existing->photo_url;

        if (isset($payload['photo']) && is_array($payload['photo'])) {
            $folder = "public/timKreatif/";
            if (!file_exists($folder))
                mkdir($folder, 0777, true);

            $hash = md5_file($payload['photo']['tmp_name']);
            $ext = pathinfo($payload['photo']['name'], PATHINFO_EXTENSION);
            $filename = $hash . "." . strtolower($ext);
            $targetPath = $folder . $filename;

            // Reuse if exists
            if (!file_exists($targetPath)) {
                move_uploaded_file($payload['photo']['tmp_name'], $targetPath);
            }

            $payload['photo_url'] = $targetPath;
        } else {
            unset($payload['photo']);
        }

        unset($payload['photo']);
        if (isset($payload['skills']) && is_array($payload['skills'])) {
            $payload['skills'] = json_encode($payload['skills']);
        }
        if (empty($payload)) {
            return [false, 'No fields to update'];
        }

        $success = $this->repo->update($id, $payload);
        if (!$success) {
            return [false, 'Update failed'];
        }

        return [true, 'TimKreatif updated successfully'];
    }

    public function delete(int $id): bool
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return false;

        return $this->repo->delete($id);
    }
}
