<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\Media3DAssetUploadRequest;
use App\Services\Media3DAssetService;
use Exception;
use PDO;

final class Media3DAssetController extends Controller
{
    public function __construct(private ?Media3DAssetService $svc = null)
    {
        $this->svc ??= new Media3DAssetService();
    }

    public function upload(Request $req, Response $res): Response
    {
        try {
            $url = $req->json['url'] ?? $req->json['file'] ?? null;

            if (!$url) {
                return $res->json(
                    ResponseFormatter::error("URL is required", 400),
                    400
                );
            }

            $visibility = $req->json['visibility'] ?? 1;
            $caption    = $req->json['caption'] ?? null;
            $alt        = $req->json['alt_text'] ?? null;
            $user = $req->getAttribute('user', null);

            if (!$user || empty($user['id'])) {
                return $res->json(
                    ResponseFormatter::error('Invalid user', 401),
                    401
                );
            }
            $owner      = $user['id'] ?? null;

            Media3DAssetUploadRequest::validate($url, $visibility);

            [$ok, $data] = $this->svc->upload($url, $visibility, $owner, $caption, $alt);

            return $res->json(
                ResponseFormatter::success("File uploaded", $data, 200),
                200
            );
        } catch (Exception $e) {
            return $res->json(
                ResponseFormatter::error($e->getMessage(), 400),
                400
            );
        }
    }

    public function list(Request $req, Response $res): Response
    {
        $q = $req->query;
        $type = $q['type'] ?? null;
        $vis  = $q['visibility'] ?? null;
        $own  = isset($q['owner']) ? (int) $q['owner'] : null;

        $limit = (int)($q['limit'] ?? 10);
        $offset = (int)($q['offset'] ?? 0);

        $data = $this->svc->list($limit, $offset, $vis, $own);

        return $res->json(
            ResponseFormatter::success("OK", $data),
            200
        );
    }

    public function detail(Request $req, Response $res, array $params): Response
    {
        $id = (int)$params['id'];
        $row = $this->svc->get($id);

        if (!$row) {
            return $res->json(ResponseFormatter::error("Not found", 404), 404);
        }

        return $res->json(ResponseFormatter::success("OK", $row), 200);
    }

    public function delete(Request $req, Response $res, array $params): Response
    {
        $id = (int)$params['id'];
        [$ok, $msg] = $this->svc->delete($id);

        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($msg, 400),
                400
            );
        }
        return $res->json(ResponseFormatter::success("Deleted", ["id" => $id]), 200);
    }

    public function updateVisibility(Request $req, Response $res, array $params): Response
    {
        $id = (int)$params['id'];
        $data = $req->json;

        if (!isset($data['visibility'])) {
            return $res->json(ResponseFormatter::error("visibility required", 422), 422);
        }

        $ok = $this->svc->updateVisibility($id, $data['visibility']);

        if (!$ok) {
            return $res->json(ResponseFormatter::error("Failed update", 400), 400);
        }

        return $res->json(ResponseFormatter::success("Updated", ["id" => $id]), 200);
    }

    public function updateMetadata(Request $req, Response $res, array $params): Response
    {
        $id = (int)$params['id'];
        $data = $req->json;

        $ok = $this->svc->updateMetadata($id, $data['alt_text'] ?? null, $data['caption'] ?? null);

        if (!$ok) {
            return $res->json(ResponseFormatter::error("Failed update", 400), 400);
        }

        return $res->json(ResponseFormatter::success("Updated", ["id" => $id]), 200);
    }
}
