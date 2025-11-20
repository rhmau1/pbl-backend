<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\TimKreatifRequest;
use App\Services\TimKreatifService;

final class TimKreatifController extends Controller
{
    public function __construct(private ?TimKreatifService $svc = null)
    {
        $this->svc ??= new TimKreatifService();
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
                ResponseFormatter::error('Tim Kreatif not found', 404),
                404
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $u, 200),
            200
        );
    }

    public function create(Request $req, Response $res): Response
    {
        [$valid, $errors, $payload] = TimKreatifRequest::validateCreate($req->json, $_FILES);

        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        [$ok, $data] = $this->svc->create($payload);
        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($data, 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $data, 201),
            201
        );
    }

    public function update(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);

        [$valid, $errors, $payload] = TimKreatifRequest::validateUpdate($req->json, $_FILES);

        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        [$ok, $msg] = $this->svc->update($id, $payload);
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
}
