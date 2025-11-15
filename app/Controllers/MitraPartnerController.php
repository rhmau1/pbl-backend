<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\MitraPartnerRequest;
use App\Services\MitraPartnerService;

final class MitraPartnerController extends Controller
{
    public function __construct(private ?MitraPartnerService $svc = null)
    {
        $this->svc ??= new MitraPartnerService();
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
                ResponseFormatter::error('Media social not found', 404),
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
        $file = $_FILES['file'] ?? null;

        [$valid, $errors, $payload] = MitraPartnerRequest::validateUpdate($file, $input);
        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Validation error', 422, $errors),
                422
            );
        }

        [$ok, $data] = $this->svc->update($id, $file, $payload);
        if (!$ok) {
            return $res->json(
                ResponseFormatter::error('Cannot update', 400),
                400
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $data, 200),
            200
        );
    }

    public function create(Request $req, Response $res): Response
    {
        $file = $_FILES['file'] ?? null;

        if (!$file) {
            return $res->json(
                ResponseFormatter::error("File is required", 400),
                400
            );
        }
        [$valid, $errors, $payload] = MitraPartnerRequest::validateCreate($file, $req->json);

        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Input invalid', 422, $errors),
                422
            );
        }

        [$ok, $out] = $this->svc->create($file, $payload['name'], $payload['website_url'], $payload['position']);
        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($out, 400),
                400
            );
        }

        $data = [
            'id'    => $out['id'],
            'name'  => $out['name'],
            'website_url'  => $out['website_url'],
            'logo_url'  => $out['logo_url'],
            'position'  => $out['position']
        ];

        return $res->json(
            ResponseFormatter::success('Success', $data, 200),
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
