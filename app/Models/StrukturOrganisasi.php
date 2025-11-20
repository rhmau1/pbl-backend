<?php

namespace App\Models;

final class StrukturOrganisasi
{
    public function __construct(
        public ?int $id,
        public string $nama,
        public string $jabatan,
        public string $keahlian,
        public ?array $minat_penelitian,
        public ?array $sosial,
        public ?string $foto,
        public ?int $parent_id
    ) {}
}
