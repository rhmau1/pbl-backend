<?php
namespace App\Middlewares;

use App\Core\{MiddlewareInterface, Request, Response};
use App\Helpers\ResponseFormatter;

final class CorsMiddleware implements MiddlewareInterface
{
    public function process(Request $req, callable $next): Response
    {
        // set header always
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        // PRE-FLIGHT
        if ($req->method === "OPTIONS") {
            http_response_code(204);
            exit;
        }

        // lanjut
        return $next($req);
    }
}
