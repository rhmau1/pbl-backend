<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\UserUpdateRequest;
use App\Services\UserService;

final class UserController extends Controller
{
    public function __construct(private ?UserService $svc = null)
    {
        $this->svc ??= new UserService();
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
                ResponseFormatter::error('User not found', 404),
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

        [$valid, $errors, $payload] = UserUpdateRequest::validate($req->json);
        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        $ok = $this->svc->update($id, $payload);
        if (!$ok) {
            return $res->json(
                ResponseFormatter::error('Cannot update', 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', ['id' => $id], 200),
            200
        );
    }
    public function updateProfile(Request $req, Response $res, array $params): Response
    {
        $user = $req->getAttribute('user', null);

        if (!$user || empty($user['id'])) {
            return $res->json(
                ResponseFormatter::error('Invalid user', 401),
                401
            );
        }

        [$valid, $errors, $payload] = UserUpdateRequest::validateProfile($req->json, $_FILES);
        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        [$ok, $msg] = $this->svc->updateProfile($user['id'], $payload);
        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($msg, 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success(
                'Success',
                [
                    'id' => $msg->id,
                    'avatar' => $msg->avatar,
                    'skills' => $msg->skills,
                    'socials' => $msg->socials
                ],
                200
            ),
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
            ResponseFormatter::success('Success', ['id' => $id], 200),
            200
        );
    }

    public function logout(Request $req, Response $res): Response
    {
        $user = $req->getAttribute('user', null);

        if (!$user || empty($user['id'])) {
            return $res->json(
                ResponseFormatter::error('Invalid user', 401),
                401
            );
        }

        $u = $this->svc->get($user['id']);
        if (!$u) {
            return $res->json(
                ResponseFormatter::error('User not found', 404),
                404
            );
        }
        return $res->json(
            ResponseFormatter::success('Logged out successfully', ['id' => $user['id']], 200),
            200
        );
    }
}
