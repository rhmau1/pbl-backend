<?php

namespace App\Middlewares;

use App\Core\{MiddlewareInterface, Request, Response};
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Helpers\ResponseFormatter;

final class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private ?array $cfg = null)
    {
        $this->cfg ??= require __DIR__ . '/../../config/jwt.php';
        if (!empty($this->cfg['leeway'])) {
            JWT::$leeway = (int) $this->cfg['leeway'];
        }
    }

    public function process(Request $req, callable $next): Response
    {
        $auth = $req->header('Authorization');

        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return (new Response())->json(
                ResponseFormatter::error('Missing Authorization Bearer token', 401),
                401
            );
        }

        $token = trim(substr($auth, 7));

        try {
            $decoded = JWT::decode($token, new Key($this->cfg['secret'], 'HS256'));

            if (isset($decoded->iss) && $decoded->iss !== $this->cfg['issuer']) {
                return (new Response())->json(
                    ResponseFormatter::error('Bad issuer', 401),
                    401
                );
            }

            if (isset($decoded->aud) && $decoded->aud !== $this->cfg['audience']) {
                return (new Response())->json(
                    ResponseFormatter::error('Bad audience', 401),
                    401
                );
            }

            $user = isset($decoded->user) ? (array) $decoded->user : [];
            $req->withAttribute('user', $user);

            return $next($req);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return (new Response())->json(
                ResponseFormatter::error('Token expired', 401),
                401
            );
        } catch (\Throwable $e) {
            return (new Response())->json(
                ResponseFormatter::error('Invalid token', 401),
                401
            );
        }
    }
}
