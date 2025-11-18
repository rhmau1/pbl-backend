<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\CarouselRequest;
use App\Services\CarouselService;
use Exception;

final class CarouselController extends Controller
{
    public function __construct(private ?CarouselService $svc = null)
    {
        $this->svc ??= new CarouselService();
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
                ResponseFormatter::error('Carousel not found', 404),
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
            $file = $_FILES['carousel'] ?? null;

            if (!$file) {
                return $res->json(
                    ResponseFormatter::error("Carousel image file is required", 400),
                    400
                );
            }

            $position = isset($req->json['position']) ? (int) $req->json['position'] : 0;

            $mime = mime_content_type($file['tmp_name']);
            if (!str_contains($mime, 'image/')) {
                throw new Exception("Invalid file type, only images allowed");
            }

            $folder = "public/carousel/";
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

            $carousel_url = '/' . $path;

            [$ok, $data] = $this->svc->create($carousel_url, $position);
            if (!$ok) {
                unlink($path); // Cleanup on failure
                return $res->json(
                    ResponseFormatter::error($data, 400),
                    400
                );
            }

            $responseData = [
                'carousel' => $data,
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
            $file = $_FILES['carousel'] ?? null;

            $carousel_url = null;

            if ($file) {
                $mime = mime_content_type($file['tmp_name']);
                if (!str_contains($mime, 'image/')) {
                    throw new Exception("Invalid file type, only images allowed");
                }

                $folder = "public/carousel/";
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

                $carousel_url = '/' . $path;
            }

            [$valid, $errors, $payload] = CarouselRequest::validate($input);
            if (!$valid) {
                if ($carousel_url) {
                    unlink($path); // Cleanup on validation failure
                }
                return $res->json(
                    ResponseFormatter::error('Validation error', 422, $errors),
                    422
                );
            }

            if ($carousel_url) {
                $payload['carousel_url'] = $carousel_url;
            }

            [$ok, $msg] = $this->svc->update($id, $payload);
            if (!$ok) {
                if ($carousel_url) {
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

    public function getAll(Request $req, Response $res): Response
    {
        $carousels = $this->svc->listAll();

        return $res->json(
            ResponseFormatter::success('Success', $carousels, 200),
            200
        );
    }
}
