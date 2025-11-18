<?php

namespace App\Services;

use App\Repositories\NavbarLogoRepository;

final class NavbarLogoService
{
    public function __construct(private ?NavbarLogoRepository $repo = null)
    {
        $this->repo ??= new NavbarLogoRepository();
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

    public function create(string $logo_url, string $url, int $is_active): array
    {
        if ($is_active === 1) {
            // Set all others to inactive
            $this->repo->setAllInactive();
        }

        $id = $this->repo->create($logo_url, $url, $is_active);

        if (!$id) {
            return [false, null];
        }

        return [
            true,
            [
                'id' => $id,
                'logo_url' => $logo_url,
                'url' => $url,
                'is_active' => $is_active
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
            'logo_url' => $u->logo_url,
            'url' => $u->url,
            'is_active' => $u->is_active
        ];
    }

    public function update(int $id, array $payload): array
    {
        $fields = [];
        if (isset($payload['logo_url']) && trim($payload['logo_url']) !== '')
            $fields['logo_url'] = (string) $payload['logo_url'];
        if (isset($payload['url']) && trim($payload['url']) !== '')
            $fields['url'] = (string) $payload['url'];
        if (isset($payload['is_active']))
            $fields['is_active'] = (int) $payload['is_active'];

        if (!$fields)
            return [false, 'No fields to update'];

        $u = $this->repo->findById($id);
        if (!$u)
            return [false, 'NavbarLogo not found'];

        if (isset($fields['is_active']) && $fields['is_active'] === 1) {
            // Set all others to inactive
            $this->repo->setAllInactive();
        }

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

    public function getActive(): ?array
    {
        $u = $this->repo->getActive();
        if (!$u)
            return null;

        return [
            'id' => $u->id,
            'logo_url' => $u->logo_url,
            'url' => $u->url,
            'is_active' => $u->is_active
        ];
    }
}
