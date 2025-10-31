<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Helpers\ResponseFormatter;
use App\Requests\UserUpdateRequest;
use App\Services\UserService;

final class UserController extends Controller
{
    public function __construct(private ?UserService $svc = null)
    {
        $this->svc ??= new UserService();
    }

    public function list(Request $req)
    {
        $page  = (int)($req->query['page'] ?? 1);
        $limit = (int)($req->query['limit'] ?? 20);

        [$data, $meta] = $this->svc->paginate($page, $limit);

        ResponseFormatter::success("Success", [
            "items" => $data,
            "meta"  => $meta
        ]);
    }

    public function get(Request $req, $res, array $params)
    {
        $id = (int)($params['id'] ?? 0);
        $u  = $this->svc->get($id);

        if (!$u) {
            ResponseFormatter::error("User not found", 404);
        }

        ResponseFormatter::success("Success", $u);
    }

    public function update(Request $req, $res, array $params)
    {
        $id = (int)($params['id'] ?? 0);

        [$valid, $errors, $payload] = UserUpdateRequest::validate($req->json);
        if (!$valid) {
            ResponseFormatter::error("Validation error", 422, $errors);
        }

        $ok = $this->svc->update($id, $payload);

        if (!$ok) {
            ResponseFormatter::error("Cannot update", 400);
        }

        ResponseFormatter::success("Success", ["id" => $id]);
    }

    public function delete(Request $req, $res, array $params)
    {
        $id = (int)($params['id'] ?? 0);
        $ok = $this->svc->delete($id);

        if (!$ok) {
            ResponseFormatter::error("Cannot delete", 400);
        }

        ResponseFormatter::success("Success", ["id" => $id]);
    }
}
