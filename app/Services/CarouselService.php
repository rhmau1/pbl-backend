<?php

namespace App\Services;

use App\Repositories\CarouselRepository;

final class CarouselService
{
    public function __construct(private ?CarouselRepository $repo = null)
    {
        $this->repo ??= new CarouselRepository();
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

    public function create(string $carousel_url, int $position): array
    {
        if ($position <= 0) {
            $position = $this->repo->getMaxPosition() + 1;
        }

        $id = $this->repo->create($carousel_url, $position);

        if (!$id) {
            return [false, null];
        }

        return [
            true,
            [
                'id' => $id,
                'carousel_url' => $carousel_url,
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
            'carousel_url' => $u->carousel_url,
            'position' => $u->position
        ];
    }

    public function update(int $id, array $payload): array
    {
        $fields = [];
        if (isset($payload['carousel_url']) && trim($payload['carousel_url']) !== '')
            $fields['carousel_url'] = (string) $payload['carousel_url'];
        if (isset($payload['position']))
            $fields['position'] = (int) $payload['position'];

        if (!$fields)
            return [false, 'No fields to update'];

        $u = $this->repo->findById($id);
        if (!$u)
            return [false, 'Carousel not found'];

        $success = $this->repo->update($id, $fields);
        return [true, $success];
    }

    public function delete(int $id): bool
    {
        $u = $this->repo->findById($id);
        if (!$u)
            return false;
        return $this->repo->delete($id);
    }

    public function listAll(): array
    {
        return $this->repo->list(10000, 0); // Get all carousels
    }
}
