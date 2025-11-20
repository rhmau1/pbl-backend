<?php

declare(strict_types=1);
// index.php
if (preg_match('/^\/uploads\/.+/', $_SERVER['REQUEST_URI'])) {
  return false; // biarkan file langsung serve
}
if (preg_match('/^\/avatars\/.+/', $_SERVER['REQUEST_URI'])) {
  return false; // biarkan file langsung serve
}
if (preg_match('/^\/carousel\/.+/', $_SERVER['REQUEST_URI'])) {
  return false; // biarkan file langsung serve
}
if (preg_match('/^\/navbarLogo\/.+/', $_SERVER['REQUEST_URI'])) {
  return false; // biarkan file langsung serve
}
if (preg_match('/^\/struktur\/.+/', $_SERVER['REQUEST_URI'])) {
  return false; // biarkan file langsung serve
}
if (preg_match('/^\/timKreatif\/.+/', $_SERVER['REQUEST_URI'])) {
  return false; // biarkan file langsung serve
}

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\{
  Request,
  Response,
  Router,
  MiddlewarePipeline,
  ErrorHandler
};
use App\Middlewares\CorsMiddleware;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

ErrorHandler::register();

$request = Request::fromGlobals();
$response = new Response();

$router = new Router();
(require __DIR__ . '/../config/routes.php')($router);

$pipeline = new MiddlewarePipeline([
  new CorsMiddleware(),
]);

$pipeline
  ->handle($request, fn($req) => $router->dispatch($req, $response))
  ->send();
