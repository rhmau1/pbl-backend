<?php

use App\Core\Router;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\MediaAssetController;
use App\Controllers\NewsController;
use App\Controllers\ProjectController;
use App\Controllers\RoleController;
use App\Controllers\RolePermissionController;

return function (Router $r) {
  $r->get('/ping', fn($req, $res) => $res->json(['pong' => true]));

  $r->group('/api/v1', function (Router $api) {
    $api->get('/health', fn($req, $res) => $res->json(['ok' => true]));

    $api->post('/login', [AuthController::class, 'login']);
    $api->post('/register', [AuthController::class, 'register']);

    $api->get('/users', [UserController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/users/{id}', [UserController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/users/{id}', [UserController::class, 'update'])->middleware(new AuthMiddleware());
    $api->post('/users/profile/{id}', [UserController::class, 'updateProfile'])->middleware(new AuthMiddleware());
    $api->delete('/users/{id}', [UserController::class, 'delete'])->middleware(new AuthMiddleware());
    $api->post('/logout', [UserController::class, 'logout'])->middleware(new AuthMiddleware());

    $api->post('/categories', [CategoryController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/categories', [CategoryController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/categories/{id}', [CategoryController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/categories/{id}', [CategoryController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/categories/{id}', [CategoryController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/roles', [RoleController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/roles', [RoleController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/roles/{id}', [RoleController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/roles/{id}', [RoleController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/roles/{id}', [RoleController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/role-permissions', [RolePermissionController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/role-permissions', [RolePermissionController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/role-permissions/{id}', [RolePermissionController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/role-permissions/{id}', [RolePermissionController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/role-permissions/{id}', [RolePermissionController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/media', [MediaAssetController::class, 'upload'])->middleware(new AuthMiddleware());
    $api->get('/media', [MediaAssetController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/media/{id}', [MediaAssetController::class, 'detail'])->middleware(new AuthMiddleware());
    $api->put('/media/{id}', [MediaAssetController::class, 'updateVisibility'])->middleware(new AuthMiddleware());
    $api->delete('/media/{id}', [MediaAssetController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/project', [ProjectController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/project', [ProjectController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/project/{id}', [ProjectController::class, 'get'])->middleware(new AuthMiddleware());
    $api->post('/project/like/{id}', [ProjectController::class, 'like'])->middleware(new AuthMiddleware());
    $api->put('/project/{id}', [ProjectController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/project/{id}', [ProjectController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/news', [NewsController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/news', [NewsController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/news/{id}', [NewsController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/news/{id}', [NewsController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/news/{id}', [NewsController::class, 'delete'])->middleware(new AuthMiddleware());

    // PUBLIC
    $api->get('/public/news', [NewsController::class, 'list']);
    $api->get('/public/project', [ProjectController::class, 'list']);
    $api->get('/public/media', [MediaAssetController::class, 'list']);
    $api->get('/public/categories', [CategoryController::class, 'list']);
  });
};
