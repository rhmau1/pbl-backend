<?php

namespace App\Services;

use App\Repositories\FooterItemRepository;
use App\Repositories\FooterSectionRepository;

final class FooterItemService
{
    public function __construct(private ?FooterItemRepository $repo = null, private ?FooterSectionRepository $footerSectionRepo = null)
    {
        $this->repo ??= new FooterItemRepository();
        $this->footerSectionRepo ??= new FooterSectionRepository();
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
        $section = $this->footerSectionRepo->findById($payload['section_id']);
        if (!$section) {
            return [false, 'Footer section ID not found'];
        }
        $id = $this->repo->create($payload);
        return [true, [
            'id'         => $id,
            'section_id'    => $payload['section_id'],
            'label' => $payload['label'],
            'content' => $payload['content'],
            'position' => $payload['position'],
        ]];
    }
    public function get(int $id): ?array
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return null;

        return $u;
    }

    public function update(int $id, array $payload): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'FooterItem ID not found'];
        }

        $success = $this->repo->update($id, $payload);
        if (!$success) {
            return [false, 'Database update failed'];
        }

        return [true, 'FooterItem updated successfully'];
    }

    public function delete(int $id): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'FooterItem ID not found'];
        }

        $success = $this->repo->delete($id);
        if (!$success) {
            return [false, 'Database delete failed'];
        }

        return [true, 'FooterItem deleted successfully'];
    }
}
