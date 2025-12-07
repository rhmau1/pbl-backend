<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\ProjectMemberRequest;
use App\Services\ProjectMemberService;

final class ProjectMemberController extends Controller
{
    public function __construct(private ?ProjectMemberService $svc = null)
    {
        $this->svc ??= new ProjectMemberService();
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

    public function countMemberByProject(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);
        $p = $this->svc->countMemberByProject($id);

        return $res->json(
            ResponseFormatter::success('Success', $p, 200),
            200
        );
    }
    public function countDosenByProject(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);
        $p = $this->svc->countDosenByProject($id);

        if (!$p) {
            return $res->json(
                ResponseFormatter::error('Project member not found', 404),
                404
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $p, 200),
            200
        );
    }
    public function countAllMember(Request $req, Response $res, array $params): Response
    {
        $p = $this->svc->countAllMember();

        return $res->json(
            ResponseFormatter::success('Success', $p, 200),
            200
        );
    }
    public function countAllDosen(Request $req, Response $res, array $params): Response
    {
        $p = $this->svc->countAllDosen();

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

        [$valid, $errors, $payload] = ProjectMemberRequest::validateCreate($req->json);

        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        $id = $this->svc->create($payload);

        return $res->json(
            ResponseFormatter::success('Created', $id, 201),
            201
        );
    }

    public function update(Request $req, Response $res, array $params): Response
    {
        [$valid, $errors, $payload] = ProjectMemberRequest::validateUpdate($req->json);

        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        $ok = $this->svc->update($payload);

        return $res->json(
            ResponseFormatter::success('Updated', ['project_id' => $ok['project_id'], 'member_id' => $ok['member_id'], 'role' => $ok['role']], 200),
            200
        );
    }

    public function get(Request $req, Response $res, array $params): Response
    {
        $pid = (int) ($params['project_id'] ?? 0);
        $mid = (int) ($params['member_id'] ?? 0);
        $ok = $this->svc->get($pid, $mid);

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error('Cannot get', 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('success', $ok, 200),
            200
        );
    }
    public function getByProject(Request $req, Response $res, array $params): Response
    {
        $pid = (int) ($params['project_id'] ?? 0);
        $ok = $this->svc->getByProject($pid);

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error('Cannot get', 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('success', $ok, 200),
            200
        );
    }
    public function delete(Request $req, Response $res, array $params): Response
    {
        $pid = (int) ($params['project_id'] ?? 0);
        $mid = (int) ($params['member_id'] ?? 0);
        $ok = $this->svc->delete($pid, $mid);

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error('Cannot delete', 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Deleted', ['project_id' => $pid, 'member_id' => $mid], 200),
            200
        );
    }
}
