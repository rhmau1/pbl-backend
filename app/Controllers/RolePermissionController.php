<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\RolePermissionRequest;
use App\Services\RolePermissionService;

final class RolePermissionController extends Controller
{
    public function __construct(private ?RolePermissionService $svc = null)
    {
        $this->svc ??= new RolePermissionService();
    }

    public function list(Request $req, Response $res): Response
    {
        $page = (int) ($req->query['page'] ?? 1);
        $limit = (int) ($req->query['limit'] ?? 20);

        [$items, $meta] = $this->svc->paginate($page, $limit);

        return $res->json(
            ResponseFormatter::success('Success', [
                'items' => $items,
                'meta' => $meta,
            ], 200),
            200
        );
    }

    public function get(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);
        $u = $this->svc->get($id);

        if (!$u) {
            return $res->json(
                ResponseFormatter::error('Role permission not found', 404),
                404
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $u, 200),
            200
        );
    }

    public function update(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);

        $input = $req->json ?: $_POST;

        [$valid, $errors, $payload] = RolePermissionRequest::validate($input);
        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        [$ok, $msg] = $this->svc->update($id, $payload['role_id'], $payload['permission']);

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($msg, 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', ['id' => $id], 200),
            200
        );
    }
    public function create(Request $req, Response $res): Response
    {
        $input = $req->json ?: $_POST;

        [$valid, $errors, $payload] = RolePermissionRequest::validate($input);
        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        [$ok, $out] = $this->svc->create(
            $payload['role_id'],
            $payload['permission']
        );

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($out, 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $out, 200),
            200
        );
    }

    public function delete(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);
        [$ok, $msg] = $this->svc->delete($id);

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($msg, 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', ['id' => $id], 200),
            200
        );
    }
}
