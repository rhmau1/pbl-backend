<?php

namespace App\Repositories;

use App\Models\NavbarLogo;
use Config\Database;
use PDO;

final class NavbarLogoRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function findById(int $id): ?NavbarLogo
    {
        $st = $this->db->prepare('SELECT * FROM navbar_logos WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    public function exists(int $id): bool
    {
        $st = $this->db->prepare('SELECT * FROM navbar_logos WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? true : false;
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM navbar_logos ORDER BY id DESC LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM navbar_logos')->fetchColumn();
    }

    public function create(string $logo_url, string $url, int $is_active): int
    {
        $st = $this->db->prepare('INSERT INTO navbar_logos(logo_url, url, is_active) VALUES(:logo_url, :url, :is_active) RETURNING id');
        $st->execute(['logo_url' => $logo_url, 'url' => $url, 'is_active' => $is_active]);
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

        $sql = 'UPDATE navbar_logos SET ' . implode(',', $set) . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute($params);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM navbar_logos WHERE id=:id');
        return $st->execute(['id' => $id]);
    }

    public function setAllInactive(): bool
    {
        $st = $this->db->prepare('UPDATE navbar_logos SET is_active = 0');
        return $st->execute();
    }

    public function getActive(): ?NavbarLogo
    {
        $st = $this->db->prepare('SELECT * FROM navbar_logos WHERE is_active = 1 LIMIT 1');
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->map($row) : null;
    }

    private function map(array $r): NavbarLogo
    {
        return new NavbarLogo(
            (int) $r['id'],
            $r['logo_url'],
            $r['url'],
            (int) $r['is_active']
        );
    }
}
