<?php

namespace App\Services;

use App\Repositories\Media3DAssetRepository;
use App\Models\MediaAsset;
use Exception;

final class Media3DAssetService
{
    public function __construct(private ?Media3DAssetRepository $repo = null)
    {
        $this->repo ??= new Media3DAssetRepository();
    }

    public function upload(string $url, string $visibility, ?string $caption, ?string $alt): array
    {
        $type      = "3D";
        $checksum  = null; // No checksum for remote URLs
        $size      = 0;    // Unknown size for remote URLs

        $id = $this->repo->create([
            ':type'    => $type,
            ':url'     => $url,
            ':alt'     => $alt,
            ':caption' => $caption,
            ':bytes'   => $size,
            ':checksum' => $checksum,
            ':vis'     => $visibility,
        ]);

        if (!$id) {
            return [false, null];
        }

        return [true, [
            'id'         => $id,
            'url'        => $url,
            'type'       => $type,
            'visibility' => $visibility
        ]];
    }

    public function get(int $id): ?MediaAsset
    {
        return $this->repo->findById($id);
    }

    public function list(int $limit, int $offset, ?int $visibility, ?int $owner): array
    {
        return $this->repo->filter($limit, $offset, $visibility, $owner);
    }

    public function delete(int $id): array
    {
        $u = $this->repo->findById($id);
        if (!$u) {
            return [false, 'Media ID not found'];
        }
        $success = $this->repo->delete($id);
        if (!$success) {
            return [false, 'Database delete failed'];
        }

        return [true, 'Media deleted successfully'];
    }

    public function updateVisibility(int $id, int $visibility): bool
    {
        if (!in_array($visibility, [0, 1])) {
            return false;
        }
        $m = $this->repo->findById($id);
        if (!$m) {
            return false;
        }
        return $this->repo->updateVisibility($id, $visibility);
    }

    public function updateMetadata(int $id, ?string $alt, ?string $caption): bool
    {
        return $this->repo->updateMetadata($id, $alt, $caption);
    }
}
