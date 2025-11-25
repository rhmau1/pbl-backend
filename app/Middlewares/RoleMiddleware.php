<?php

namespace App\Middlewares;

use App\Core\{MiddlewareInterface, Request, Response};
use App\Helpers\ResponseFormatter;

final class RoleMiddleware implements MiddlewareInterface
{
    private const ROLES = [
        'viewer' => 1,
        'user' => 2,
        'kontributor' => 2,
        'editor' => 3,
        'admin' => 4,
    ];

    public function __construct(private string $minRole)
    {
        if (!isset(self::ROLES[$this->minRole])) {
            throw new \InvalidArgumentException("Invalid role: {$this->minRole}");
        }
    }

    public function process(Request $req, callable $next): Response
    {
        $user = $req->getAttribute('user');

        if (!$user || !isset($user['role'])) {
            return (new Response())->json(
                ResponseFormatter::error('User role not found', 403),
                403
            );
        }

        $role = $user['role'];

        if (!isset(self::ROLES[$role])) {
            return (new Response())->json(
                ResponseFormatter::error('Invalid user role', 403),
                403
            );
        }

        if (self::ROLES[$role] < self::ROLES[$this->minRole]) {
            return (new Response())->json(
                ResponseFormatter::error('Insufficient permissions', 403),
                403
            );
        }

        return $next($req);
    }
}
