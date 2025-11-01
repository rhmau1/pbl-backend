<?php
namespace App\Services;

use App\Repositories\UserRepository;

final class UserService
{
    public function __construct(private ?UserRepository $repo = null)
    {
        $this->repo ??= new UserRepository();
    }

    public function paginate(int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = min(100, max(1, $limit));
        $offset = ($page - 1) * $limit;
        $data = $this->repo->list($limit, $offset);
        $total = $this->repo->count();
        return [$data, ['page' => $page, 'limit' => $limit, 'total' => $total]];
    }

    public function get(int $id): ?array
    {
        $u = $this->repo->findById($id);
        return $u ? ['id' => $u->id, 'email' => $u->email, 'name' => $u->name, 'created_at' => $u->createdAt] : null;
    }

    public function update(int $id, array $payload): bool
    {
        $fields = [];
        if (isset($payload['name']))
            $fields['name'] = (string) $payload['name'];
        if (isset($payload['password']))
            $fields['password_hash'] = password_hash((string) $payload['password'], PASSWORD_BCRYPT);
        if (!$fields)
            return true;
        return $this->repo->update($id, $fields);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
