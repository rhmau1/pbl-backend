<?php

namespace App\Requests;

final class StrukturOrganisasiRequest
{
    public static function validate(array $in, array $files): array
    {
        $nama = isset($in['nama']) ? trim((string) $in['nama']) : null;
        $jabatan = isset($in['jabatan']) ? trim((string) $in['jabatan']) : null;
        $keahlian = isset($in['keahlian']) ? trim((string) $in['keahlian']) : null;
        $minat_penelitian = $in['minat_penelitian'] ?? null;
        $sosial = $in['sosial'] ?? null;
        $parent_id = isset($in['parent_id']) ? (int) $in['parent_id'] : null;

        $errors = [];

        if ($nama === null || $nama === '')
            $errors['nama'] = 'Nama is required';

        if ($jabatan === null || $jabatan === '')
            $errors['jabatan'] = 'Jabatan is required';

        if ($keahlian === null || $keahlian === '')
            $errors['keahlian'] = 'Keahlian is required';

        if ($minat_penelitian !== null && is_string($minat_penelitian)) {
            $minat_penelitian = json_decode($minat_penelitian, true);
        }
        if ($minat_penelitian !== null && !is_array($minat_penelitian)) {
            $errors['minat_penelitian'] = 'Minat penelitian must be an array';
        }

        if ($sosial !== null && is_string($sosial)) {
            $sosial = json_decode($sosial, true);
        }
        if ($sosial !== null && !is_array($sosial)) {
            $errors['sosial'] = 'Sosial must be an object';
        }

        $foto = null;
        if (isset($files['foto']) && $files['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($files['foto']['error'] !== 0) {
                $errors['foto'] = 'Foto upload error';
            } else {
                if ($files['foto']['size'] > 2 * 1024 * 1024) {
                    $errors['foto'] = 'Foto max 2MB';
                }

                $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($files['foto']['type'], $allowed)) {
                    $errors['foto'] = 'Invalid foto type';
                }

                $foto = $files['foto'];
            }
        }

        return [
            $errors === [],
            $errors,
            array_filter([
                'nama' => $nama,
                'jabatan' => $jabatan,
                'keahlian' => $keahlian,
                'minat_penelitian' => $minat_penelitian,
                'sosial' => $sosial,
                'foto' => $foto,
                'parent_id' => $parent_id,
            ], fn($v) => $v !== null)
        ];
    }
}
