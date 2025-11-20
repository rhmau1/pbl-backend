<?php

use App\Core\Router;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\FooterItemController;
use App\Controllers\FooterSectionController;
use App\Controllers\MediaAssetController;
use App\Controllers\MediaSocialController;
use App\Controllers\CarouselController;
use App\Controllers\MitraPartnerController;
use App\Controllers\NavbarLogoController;
use App\Controllers\NewsController;
use App\Controllers\ProjectController;
use App\Controllers\RoleController;
use App\Controllers\RolePermissionController;
use App\Controllers\SejarahController;
use App\Controllers\StrukturOrganisasiController;
use App\Controllers\VisiMisiController;
use App\Controllers\ProjectMemberController;

return function (Router $r) {
  $r->get('/ping', fn($req, $res) => $res->json(['pong' => true]));

  $r->group('/api/v1', function (Router $api) {
    $api->get('/health', fn($req, $res) => $res->json(['ok' => true]));

    $api->post('/login', [AuthController::class, 'login']);
    $api->post('/register', [AuthController::class, 'register']);

    $api->get('/users', [UserController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/users/{id}', [UserController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/users/{id}', [UserController::class, 'update'])->middleware(new AuthMiddleware());
    $api->post('/users/profile', [UserController::class, 'updateProfile'])->middleware(new AuthMiddleware());
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
    $api->post('/project-members', [ProjectMemberController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/project-members', [ProjectMemberController::class, 'list']);
    $api->get('/project-members/{id}', [ProjectMemberController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/project-members/{id}', [ProjectMemberController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/project-members/{id}', [ProjectMemberController::class, 'delete'])->middleware(new AuthMiddleware());

    // Counting endpoints
    $api->get('/project/count/published', [ProjectController::class, 'countPublished']);
    $api->get('/project-members/count/member', [ProjectMemberController::class, 'countAllMember']);
    $api->get('/project-members/count/dosen', [ProjectMemberController::class, 'countAllDosen']);
    $api->get('/project-members/count/member/{id}', [ProjectMemberController::class, 'countMemberByProject']);
    $api->get('/project-members/count/dosen/{id}', [ProjectMemberController::class, 'countDosenByProject']);


    $api->post('/news', [NewsController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/news', [NewsController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/news/{id}', [NewsController::class, 'get'])->middleware(new AuthMiddleware());
    $api->put('/news/{id}', [NewsController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/news/{id}', [NewsController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/visi-misi', [VisiMisiController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/visi-misi', [VisiMisiController::class, 'list']);
    $api->get('/visi-misi/{id}', [VisiMisiController::class, 'get']);
    $api->put('/visi-misi/{id}', [VisiMisiController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/visi-misi/{id}', [VisiMisiController::class, 'delete'])->middleware(new AuthMiddleware());

    // $api->post('/sejarah', [SejarahController::class, 'create'])->middleware(new AuthMiddleware());
    // $api->get('/sejarah', [SejarahController::class, 'list']);
    // $api->get('/sejarah/{id}', [SejarahController::class, 'get']);
    // $api->put('/sejarah/{id}', [SejarahController::class, 'update'])->middleware(new AuthMiddleware());
    // $api->delete('/sejarah/{id}', [SejarahController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/struktur-organisasi', [StrukturOrganisasiController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/struktur-organisasi', [StrukturOrganisasiController::class, 'list']);
    $api->get('/struktur-organisasi/{id}', [StrukturOrganisasiController::class, 'get']);
    $api->post('/struktur-organisasi/{id}', [StrukturOrganisasiController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/struktur-organisasi/{id}', [StrukturOrganisasiController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/media-social', [MediaSocialController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/media-social', [MediaSocialController::class, 'list']);
    $api->get('/media-social/{id}', [MediaSocialController::class, 'get']);
    $api->put('/media-social/{id}', [MediaSocialController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/media-social/{id}', [MediaSocialController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/mitra-partner', [MitraPartnerController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/mitra-partner', [MitraPartnerController::class, 'list']);
    $api->get('/mitra-partner/{id}', [MitraPartnerController::class, 'get']);
    $api->post('/mitra-partner/{id}', [MitraPartnerController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/mitra-partner/{id}', [MitraPartnerController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/footer-section', [FooterSectionController::class, 'create']);
    $api->get('/footer-section', [FooterSectionController::class, 'list']);
    $api->get('/footer-section/{id}', [FooterSectionController::class, 'get']);
    $api->put('/footer-section/{id}', [FooterSectionController::class, 'update']);
    $api->delete('/footer-section/{id}', [FooterSectionController::class, 'delete']);

    $api->post('/footer-item', [FooterItemController::class, 'create']);
    $api->get('/footer-item', [FooterItemController::class, 'list']);
    $api->get('/footer-item/{id}', [FooterItemController::class, 'get']);
    $api->put('/footer-item/{id}', [FooterItemController::class, 'update']);
    $api->delete('/footer-item/{id}', [FooterItemController::class, 'delete']);

    $api->post('/carousel', [CarouselController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/carousel', [CarouselController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/carousel/{id}', [CarouselController::class, 'get'])->middleware(new AuthMiddleware());
    $api->post('/carousel/{id}', [CarouselController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/carousel/{id}', [CarouselController::class, 'delete'])->middleware(new AuthMiddleware());

    $api->post('/navbar-logo', [NavbarLogoController::class, 'create'])->middleware(new AuthMiddleware());
    $api->get('/navbar-logo', [NavbarLogoController::class, 'list'])->middleware(new AuthMiddleware());
    $api->get('/navbar-logo/{id}', [NavbarLogoController::class, 'get'])->middleware(new AuthMiddleware());
    $api->post('/navbar-logo/{id}', [NavbarLogoController::class, 'update'])->middleware(new AuthMiddleware());
    $api->delete('/navbar-logo/{id}', [NavbarLogoController::class, 'delete'])->middleware(new AuthMiddleware());

    // PUBLIC
    $api->get('/public/carousel', [CarouselController::class, 'getAll']);
    $api->get('/public/news', [NewsController::class, 'list']);
    $api->get('/public/project', [ProjectController::class, 'list']);
    $api->get('/public/project/{id}', [ProjectController::class, 'get']);
    $api->get('/public/media', [MediaAssetController::class, 'list']);
    $api->get('/public/categories', [CategoryController::class, 'list']);
    $api->get('/public/navbar-logo/active', [NavbarLogoController::class, 'getActive']);
  });
};
