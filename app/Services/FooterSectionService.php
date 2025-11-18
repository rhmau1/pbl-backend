<?php

namespace App\Services;

use App\Repositories\FooterSectionRepository;

final class FooterSectionService
{
    public function __construct(private ?FooterSectionRepository $repo = null)
    {
        $this->repo ??= new FooterSectionRepository();
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

    public function create(string $section_name, int $position): array
    {
        $id = $this->repo->create($section_name, $position);

        if (!$id) {
            return [false, null];
        }

        return [
            true,
            [
                'id'    => $id,
                'section_name'  => $section_name,
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
            'section_name' => $u->section_name,
            'position' => $u->position
        ];
    }

    public function update(int $id, array $payload): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'FooterSection ID not found'];
        }

        $success = $this->repo->update($id, $payload);
        if (!$success) {
            return [false, 'Database update failed'];
        }

        return [true, 'FooterSection updated successfully'];
    }

    public function delete(int $id): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'FooterSection ID not found'];
        }

        $success = $this->repo->delete($id);
        if (!$success) {
            return [false, 'Database delete failed'];
        }

        return [true, 'FooterSection deleted successfully'];
    }
}
