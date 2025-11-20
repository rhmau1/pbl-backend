<?php

namespace App\Services;

use App\Repositories\ProjectMemberRepository;

final class ProjectMemberService
{
    public function __construct(private ?ProjectMemberRepository $repo = null)
    {
        $this->repo ??= new ProjectMemberRepository();
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

    public function update(array $payload): array
    {
        $success = $this->repo->updateRole($payload);

        if (!$success) {
            return [false, 'Update failed'];
        }

        return $payload;
    }

    public function create(array $payload): array
    {
        $this->repo->create($payload);

        return [
            'project_id' => $payload['project_id'],
            'member_id' => $payload['member_id'],
            'role' => $payload['role'],
        ];
    }

    public function delete(int $pid, int $mid): bool
    {
        return $this->repo->delete($pid, $mid);
    }

    public function countAllMember(): int
    {
        return $this->repo->countAllMember();
    }

    public function countAllDosen(): int
    {
        return $this->repo->countAllDosen();
    }

    public function countMemberByProject(int $projectId): int
    {
        return $this->repo->countMemberByProject($projectId);
    }

    public function countDosenByProject(int $projectId): int
    {
        return $this->repo->countDosenByProject($projectId);
    }
}
