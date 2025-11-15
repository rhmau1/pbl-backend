<?php

namespace App\Services;

use App\Repositories\MediaAssetRepository;
use App\Models\MediaAsset;
use Exception;

final class MediaAssetService
{
    public function __construct(private ?MediaAssetRepository $repo = null)
    {
        $this->repo ??= new MediaAssetRepository();
    }

    public function upload(array $file, string $visibility, ?int $owner, ?string $caption, ?string $alt): array
    {
        $mime      = mime_content_type($file['tmp_name']);
        $size      = filesize($file['tmp_name']);
        $checksum  = md5_file($file['tmp_name']);
        $type      = $this->detectType($mime);

        if ($type === 'unknown')
            throw new Exception("Unsupported file type");

        $exists = $this->repo->findByChecksum($checksum);
        if ($exists)
            return [true, ['reuse' => true, 'url' => $exists->url]];

        $folder = "public/uploads/";
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

        $id = $this->repo->create([
            ':type'    => $type,
            ':url'     => $path,
            ':alt'     => $alt,
            ':caption' => $caption,
            ':bytes'   => $size,
            ':checksum' => $checksum,
            ':owner'   => $owner,
            ':vis'     => $visibility,
        ]);
        if (!$id) {
            if (file_exists($path)) {
                unlink($path);
            }
            return [false, null];
        }
        return [true, [
            'id'         => $id,
            'url'        => $path,
            'type'       => $type,
            'visibility' => $visibility
        ]];
    }

    private function detectType(string $mime): string
    {
        if (str_contains($mime, 'image/')) return 'image';
        if (str_contains($mime, 'video/')) return 'video';
        if (str_contains($mime, 'gif')) return 'animation';
        return 'unknown';
    }
    public function get(int $id): ?MediaAsset
    {
        return $this->repo->findById($id);
    }

    public function list(int $limit, int $offset, ?string $type, ?int $visibility, ?int $owner): array
    {
        return $this->repo->filter($limit, $offset, $type, $visibility, $owner);
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
        return $this->repo->updateVisibility($id, $visibility);
    }

    public function updateMetadata(int $id, ?string $alt, ?string $caption): bool
    {
        return $this->repo->updateMetadata($id, $alt, $caption);
    }
}
