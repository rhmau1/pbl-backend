<?php

namespace App\Services;

use App\Repositories\MitraPartnerRepository;
use Exception;

final class MitraPartnerService
{
    public function __construct(private ?MitraPartnerRepository $repo = null)
    {
        $this->repo ??= new MitraPartnerRepository();
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

    public function create(array $file, string $name, string $website_url, int $position): array
    {
        $folder = "public/mitpart/logo/";
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
                throw new Exception("Failed to create directory");
            }
        }
        if (!is_uploaded_file($file["tmp_name"])) {
            throw new Exception("Invalid uploaded file");
        }
        $filename = uniqid() . "_" . strtolower(str_replace(' ', '-', basename($file["name"])));
        $path = $folder . $filename;

        if (!move_uploaded_file($file["tmp_name"], $path))
            throw new Exception("Failed saving file");
        $id = $this->repo->create($name, $path, $website_url, $position);

        if (!$id) {
            if (file_exists($path)) {
                unlink($path);
            }
            return [false, null];
        }

        return [
            true,
            [
                'id'    => $id,
                'name'  => $name,
                'website_url'  => $website_url,
                'logo_url'  => $path,
                'position' => $position
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
            'website_url' => $u->website_url,
            'logo_url' => $u->logo_url,
            'position' => $u->position,
        ];
    }

    public function update(int $id, ?array $file, array $payload): array
    {
        $existing = $this->repo->findById($id);
        if (!$existing) {
            return [false, "Data not found"];
        }

        $logoPath = $existing->logo_url;

        if ($file && isset($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
            $folder = "public/mitpart/logo/";
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $filename = uniqid() . "_" . strtolower(str_replace(' ', '-', basename($file["name"])));
            $newPath = $folder . $filename;

            if (!move_uploaded_file($file["tmp_name"], $newPath)) {
                return [false, "Failed saving new file"];
            }

            if ($logoPath && file_exists($logoPath)) {
                @unlink($logoPath);
            }

            $payload['logo_url'] = $newPath;
        }

        if (empty($payload)) {
            return [false, "No fields to update"];
        }

        $ok = $this->repo->update($id, $payload);
        if (!$ok) {
            return [false, "Update failed"];
        }

        $updated = $this->repo->findById($id);

        return [true, $updated];
    }

    public function delete(int $id): bool
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return false;
        return $this->repo->delete($id);
    }
}
