<?php

namespace App\Services;

use App\Repositories\StrukturOrganisasiRepository;

final class StrukturOrganisasiService
{
    public function __construct(private ?StrukturOrganisasiRepository $repo = null)
    {
        $this->repo ??= new StrukturOrganisasiRepository();
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

    public function get(int $id): ?array
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return null;

        return [
            'id' => $u->id,
            'nama' => $u->nama,
            'jabatan' => $u->jabatan,
            'keahlian' => $u->keahlian,
            'minat_penelitian' => $u->minat_penelitian,
            'sosial' => $u->sosial,
            'foto' => $u->foto,
            'parent_id' => $u->parent_id,
        ];
    }

    public function update(int $id, array $payload): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'Struktur Organisasi not found'];
        }

        if (isset($payload['foto']) && is_array($payload['foto'])) {

            $folder = "public/struktur/";
            if (!file_exists($folder))
                mkdir($folder, 0777, true);

            $hash = md5_file($payload['foto']['tmp_name']);
            $ext = pathinfo($payload['foto']['name'], PATHINFO_EXTENSION);
            $filename = $hash . "." . strtolower($ext);
            $targetPath = $folder . $filename;

            // Reuse if exists
            if (!file_exists($targetPath)) {
                move_uploaded_file($payload['foto']['tmp_name'], $targetPath);
            }

            $payload['foto'] = '/struktur/' . $filename;
        } else {
            unset($payload['foto']);
        }

        $success = $this->repo->update($id, $payload);

        if (!$success) {
            return [false, 'Update failed'];
        }

        return [true, null];
    }

    public function create(array $payload): array
    {
        if (isset($payload['foto']) && is_array($payload['foto'])) {

            $folder = "public/struktur/";
            if (!file_exists($folder))
                mkdir($folder, 0777, true);

            $hash = md5_file($payload['foto']['tmp_name']);
            $ext = pathinfo($payload['foto']['name'], PATHINFO_EXTENSION);
            $filename = $hash . "." . strtolower($ext);
            $targetPath = $folder . $filename;

            // Reuse if exists
            if (!file_exists($targetPath)) {
                move_uploaded_file($payload['foto']['tmp_name'], $targetPath);
            }

            $payload['foto'] = 'struktur/' . $filename;
        } else {
            $payload['foto'] = null;
        }

        $id = $this->repo->create($payload);
        $created = $this->repo->findById($id);

        return [true, [
            'id' => $created->id,
            'nama' => $created->nama,
            'jabatan' => $created->jabatan,
            'keahlian' => $created->keahlian,
            'minat_penelitian' => $created->minat_penelitian,
            'sosial' => $created->sosial,
            'foto' => $created->foto,
            'parent_id' => $created->parent_id,
        ]];
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
