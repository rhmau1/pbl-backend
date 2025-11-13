<?php


namespace App\Repositories;


use App\Models\Tag;
use Config\Database;
use PDO;


final class TagRepository
{
    private PDO $db;


    public function __construct()
    {
        $this->db = Database::pdo();
    }


    public function findByName(string $name): ?array
    {
        $st = $this->db->prepare('SELECT * FROM tags WHERE LOWER(name) = LOWER(:n) LIMIT 1');
        $st->execute(['n' => $name]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ? $r : null;
    }


    public function create(string $name): int
    {
        $slug = $this->slugify($name);
        $st = $this->db->prepare('INSERT INTO tags(name,slug) VALUES(:n,:s) RETURNING id');
        $st->execute(['n' => $name, 's' => $slug]);
        return (int) $st->fetchColumn();
    }


    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
