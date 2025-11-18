<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\NavbarLogoRequest;
use App\Services\NavbarLogoService;
use Exception;

final class NavbarLogoController extends Controller
{
    public function __construct(private ?NavbarLogoService $svc = null)
    {
        $this->svc ??= new NavbarLogoService();
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
                ResponseFormatter::error('NavbarLogo not found', 404),
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
        try {
            $file = $_FILES['logo'] ?? null;

            if (!$file) {
                return $res->json(
                    ResponseFormatter::error("Logo file is required", 400),
                    400
                );
            }

            $url = $req->json['url'] ?? '';
            $is_active = isset($req->json['is_active']) ? (int) $req->json['is_active'] : 0;

            $mime = mime_content_type($file['tmp_name']);
            if (!str_contains($mime, 'image/')) {
                throw new Exception("Invalid file type, only images allowed");
            }

            $folder = "public/navbarLogo/";
            if (!is_dir($folder)) {
                if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
                    throw new Exception("Failed to create directory");
                }
            }

            if (!is_uploaded_file($file["tmp_name"])) {
                throw new Exception("Invalid uploaded file");
            }

            $filename = uniqid() . "_" . strtolower(str_replace(' ', '-', basename($file["name"])));
            $path = $folder . $filename;

            if (!move_uploaded_file($file["tmp_name"], $path)) {
                throw new Exception("Failed saving file");
            }

            $logo_url = '/' . $path;

            [$ok, $data] = $this->svc->create($logo_url, $url, $is_active);
            if (!$ok) {
                unlink($path); // Cleanup on failure
                return $res->json(
                    ResponseFormatter::error($data, 400),
                    400
                );
            }

            $responseData = [
                'navbarLogo' => $data,
            ];

            return $res->json(
                ResponseFormatter::success('Success', $responseData, 200),
                200
            );
        } catch (Exception $e) {
            return $res->json(
                ResponseFormatter::error($e->getMessage(), 400),
                400
            );
        }
    }

    public function update(Request $req, Response $res, array $params): Response
    {
        $id = (int) ($params['id'] ?? 0);

        try {
            $input = $req->json ?: $_POST;
            $file = $_FILES['logo'] ?? null;

            $logo_url = null;

            if ($file) {
                $mime = mime_content_type($file['tmp_name']);
                if (!str_contains($mime, 'image/')) {
                    throw new Exception("Invalid file type, only images allowed");
                }

                $folder = "public/navbarLogo/";
                if (!is_dir($folder)) {
                    if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
                        throw new Exception("Failed to create directory");
                    }
                }

                if (!is_uploaded_file($file["tmp_name"])) {
                    throw new Exception("Invalid uploaded file");
                }

                $filename = uniqid() . "_" . strtolower(str_replace(' ', '-', basename($file["name"])));
                $path = $folder . $filename;

                if (!move_uploaded_file($file["tmp_name"], $path)) {
                    throw new Exception("Failed saving file");
                }

                $logo_url = '/' . $path;
            }

            [$valid, $errors, $payload] = NavbarLogoRequest::validate($input);
            if (!$valid) {
                if ($logo_url) {
                    unlink($path); // Cleanup on validation failure
                }
                return $res->json(
                    ResponseFormatter::error('Validation error', 422, $errors),
                    422
                );
            }

            if ($logo_url) {
                $payload['logo_url'] = $logo_url;
            }

            [$ok, $msg] = $this->svc->update($id, $payload);
            if (!$ok) {
                if ($logo_url) {
                    unlink($path); // Cleanup on failure
                }
                return $res->json(
                    ResponseFormatter::error($msg, 400),
                    400
                );
            }

            return $res->json(
                ResponseFormatter::success('Success', ['id' => $id], 200),
                200
            );
        } catch (Exception $e) {
            return $res->json(
                ResponseFormatter::error($e->getMessage(), 400),
                400
            );
        }
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

    public function getActive(Request $req, Response $res): Response
    {
        $active = $this->svc->getActive();

        if (!$active) {
            return $res->json(
                ResponseFormatter::error('No active logo found', 404),
                404
            );
        }

        return $res->json(
            ResponseFormatter::success('Success', $active, 200),
            200
        );
    }
}
