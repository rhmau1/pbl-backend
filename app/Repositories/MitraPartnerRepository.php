<?php

namespace App\Repositories;

use App\Models\MitraPartner;
use Config\Database;
use PDO;

final class MitraPartnerRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?MitraPartner
    {
        $st = $this->db->prepare('SELECT * FROM mitra_partner WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM mitra_partner ORDER BY position DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM mitra_partner')->fetchColumn();
    }

    public function create(string $name, string $logo_url, string $website_url, int $position): int
    {
        $st = $this->db->prepare('INSERT INTO mitra_partner(name, logo_url, website_url, position) VALUES(:pl,:u, :i, :p) RETURNING id');
        $st->execute(['pl' => $name, 'u' => $logo_url, 'i' => $website_url, 'p' => $position]);
        return (int) $st->fetchColumn();
    }

    public function update(int $id, array $fields): bool
    {
        $set = [];
        $params = ['id' => $id];

        foreach ($fields as $k => $v) {
            $set[] = "$k = :$k";
            $params[$k] = $v;
        }

        $sql = 'UPDATE mitra_partner SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM mitra_partner WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    private function map(array $r): MitraPartner
    {
        return new MitraPartner(
            (int) $r['id'],
            $r['name'],
            $r['logo_url'],
            $r['website_url'],
            $r['position'],
        );
    }
}
