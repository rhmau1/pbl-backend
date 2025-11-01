<?php

use App\Core\Router;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;
use App\Controllers\AuthController;

return function (Router $r) {
  $r->get('/ping', fn($req, $res) => $res->json(['pong' => true]));

  $r->group('/api/v1', function (Router $api) {
    $api->get('/health', fn($req, $res) => $res->json(['ok' => true]));

    $api->post('/login', [AuthController::class, 'login']);
    $api->post('/register', [AuthController::class, 'register']);

    $api->get('/users', [UserController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/users/{id}', [UserController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/users/{id}', [UserController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/users/{id}', [UserController::class, 'delete'])->middleware(new AuthMiddleware());
    $api->post('/logout', [UserController::class, 'logout'])->middleware(new AuthMiddleware());
  });
};
