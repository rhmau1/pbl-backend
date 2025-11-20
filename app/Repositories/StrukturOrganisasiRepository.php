<?php

namespace App\Repositories;


use App\Models\StrukturOrganisasi;
use Config\Database;
use PDO;

final class StrukturOrganisasiRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?StrukturOrganisasi
    {
        $st = $this->db->prepare('SELECT * FROM struktur_organisasi WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM struktur_organisasi ORDER BY position ASC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM struktur_organisasi')->fetchColumn();
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare('INSERT INTO struktur_organisasi(nama, jabatan, keahlian, minat_penelitian, sosial, foto, parent_id) VALUES(:nama, :jabatan, :keahlian, :minat_penelitian, :sosial, :foto, :parent_id) RETURNING id');
        $st->execute([
            'nama' => $data['nama'],
            'jabatan' => $data['jabatan'],
            'keahlian' => $data['keahlian'],
            'minat_penelitian' => isset($data['minat_penelitian']) ? json_encode($data['minat_penelitian']) : null,
            'sosial' => isset($data['sosial']) ? json_encode($data['sosial']) : null,
            'foto' => $data['foto'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
        ]);
        return (int) $st->fetchColumn();
    }

    public function update(int $id, array $fields): bool
    {
        if (isset($fields['minat_penelitian'])) {
            $fields['minat_penelitian'] = json_encode($fields['minat_penelitian']);
        }

        if (isset($fields['sosial'])) {
            $fields['sosial'] = json_encode($fields['sosial']);
        }

        $set = [];
        $params = ['id' => $id];

        foreach ($fields as $k => $v) {
            $set[] = "$k = :$k";
            $params[$k] = $v;
        }

        $sql = 'UPDATE struktur_organisasi SET ' . implode(', ', $set) . ' WHERE id = :id';
        $st  = $this->db->prepare($sql);

        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM struktur_organisasi WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): StrukturOrganisasi
    {
        return new StrukturOrganisasi(
            (int) $r['id'],
            $r['nama'],
            $r['jabatan'],
            $r['keahlian'],
            json_decode($r['minat_penelitian'] ?? '[]', true),
            json_decode($r['sosial'] ?? '{}', true),
            $r['foto'],
            $r['parent_id'] ? (int) $r['parent_id'] : null
        );
    }
}
