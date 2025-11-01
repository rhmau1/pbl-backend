<?php
namespace App\Middlewares;

use App\Core\{MiddlewareInterface, Request, Response};
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
            return (new Response())->json([
                'ok' => false,
                'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Missing Authorization Bearer token']
            ], 401);
        }

        $token = trim(substr($auth, 7));
        try {
            $decoded = JWT::decode($token, new Key($this->cfg['secret'], 'HS256'));

            if (isset($decoded->iss) && isset($this->cfg['issuer']) && $decoded->iss !== $this->cfg['issuer']) {
                return (new Response())->json(['ok' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Bad issuer']], 401);
            }
            if (isset($decoded->aud) && isset($this->cfg['audience']) && $decoded->aud !== $this->cfg['audience']) {
                return (new Response())->json(['ok' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Bad audience']], 401);
            }

            $user = isset($decoded->user) ? (array) $decoded->user : [];
            $req->withAttribute('user', $user);

            return $next($req);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return (new Response())->json(['ok' => false, 'error' => ['code' => 'TOKEN_EXPIRED', 'message' => 'Token expired']], 401);
        } catch (\Throwable $e) {
            return (new Response())->json(['ok' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Invalid token']], 401);
        }
    }
}
