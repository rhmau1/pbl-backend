<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\ProjectRequest;
use App\Services\ProjectService;

final class ProjectController extends Controller
{
    public function __construct(private ?ProjectService $svc = null)
    {
        $this->svc ??= new ProjectService();
    }

    public function list(Request $req, Response $res): Response
    {
        $page = (int) ($req->query['page'] ?? 1);
        $limit = (int) ($req->query['limit'] ?? 20);

        [$items, $meta] = $this->svc->paginate($page, $limit);

        return $res->json(
            ResponseFormatter::success('Success', ['items' => $items, 'meta' => $meta], 200),
            200
        );
    }

    public function get(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);
        $p = $this->svc->get($id);

        if (!$p) {
            return $res->json(
                ResponseFormatter::error('Project not found', 404),
                404
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $p, 200),
            200
        );
    }

    public function create(Request $req, Response $res): Response
    {
        $user = $req->getAttribute('user', null);
        if (!$user || empty($user['id'])) {
            return $res->json(
                ResponseFormatter::error('Invalid user', 401),
                401
            );
        }

        [$valid, $errors, $payload] = ProjectRequest::validateCreate($req->json);

        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }
        $payload['author_id'] = $user['id'];

        $id = $this->svc->create($payload);

        return $res->json(
            ResponseFormatter::success('Created', ['id' => $id], 201),
            201
        );
    }

    public function update(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);

        [$valid, $errors, $payload] = ProjectRequest::validateUpdate($req->json);

        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        $ok = $this->svc->update($id, $payload);

        return $res->json(
            ResponseFormatter::success('Updated', ['id' => $id], 200),
            200
        );
    }

    public function delete(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);

        $ok = $this->svc->delete($id);

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error('Cannot delete', 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Deleted', ['id' => $id], 200),
            200
        );
    }

    public function like(Request $req, Response $res, array $params): Response
    {
        $user = $req->getAttribute('user', null);
        if (!$user || empty($user['id'])) {
            return $res->json(ResponseFormatter::error('Invalid user', 401), 401);
        }

        $id = (int) ($params['id'] ?? 0);
        $ok = $this->svc->like($id, $user['id']);
        return $res->json(
            ResponseFormatter::success($ok, ['id' => $id], 200),
            200
        );
    }
}
