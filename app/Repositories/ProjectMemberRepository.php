<?php

namespace App\Repositories;

use App\Models\ProjectMember;
use Config\Database;
use PDO;

final class ProjectMemberRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function list(int $limit, int $offset): array
    {
        $st = $this->db->prepare('SELECT * FROM project_members LIMIT :l OFFSET :o');
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($r) => $this->map($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM project_members')->fetchColumn();
    }

    public function countAllMember(): int
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM project_members WHERE role = :role');
        $st->execute(['role' => 'member']);
        return (int) $st->fetchColumn();
    }

    public function countAllDosen(): int
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM project_members WHERE role = :role');
        $st->execute(['role' => 'dosen']);
        return (int) $st->fetchColumn();
    }

    public function countMemberByProject(int $projectId): int
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM project_members WHERE role = :role AND project_id = :project_id');
        $st->execute(['role' => 'member', 'project_id' => $projectId]);
        return (int) $st->fetchColumn();
    }

    public function countDosenByProject(int $projectId): int
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM project_members WHERE role = :role AND project_id = :project_id');
        $st->execute(['role' => 'dosen', 'project_id' => $projectId]);
        return (int) $st->fetchColumn();
    }
    public function create(array $data): bool
    {
        $st = $this->db->prepare('INSERT INTO project_members(project_id, member_id, role) VALUES(:project_id, :member_id, :role)');
        return $st->execute([
            'project_id' => $data['project_id'],
            'member_id' => $data['member_id'],
            'role' => $data['role'],
        ]);
    }

    public function updateRole(array $fields): bool
    {
        $st = $this->db->prepare('UPDATE project_members SET role = :role WHERE project_id = :pid AND member_id = :mid');
        return $st->execute([
            'role' => $fields['role'],
            'pid' => $fields['project_id'],
            'mid' => $fields['member_id'],
        ]);
    }

    public function delete(int $pid, int $mid): bool
    {
        $st = $this->db->prepare('DELETE FROM project_members WHERE project_id=:pid AND member_id = :mid');
        return $st->execute(['pid' => $pid, 'mid' => $mid]);
    }



    private function map(array $r): ProjectMember
    {
        return new ProjectMember(
            (int) $r['project_id'],
            (int) $r['member_id'],
            $r['role']
        );
    }
}
