<?php
declare(strict_types=1);

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
