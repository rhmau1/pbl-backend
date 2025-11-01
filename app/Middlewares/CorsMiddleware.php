<?php
namespace App\Middlewares;

use App\Core\{MiddlewareInterface, Request, Response};
use App\Helpers\ResponseFormatter;

final class CorsMiddleware implements MiddlewareInterface
{
    public function process(Request $req, callable $next): Response
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        if ($req->method === "OPTIONS") {
            http_response_code(204);
            exit;
        }

        return $next($req);
    }
}
