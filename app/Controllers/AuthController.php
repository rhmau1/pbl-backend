<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Requests\AuthLoginRequest;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function __construct(private ?AuthService $auth = null)
    {
        $this->auth ??= new AuthService();
    }

    public function register(Request $req, Response $res): Response
    {
        $email = (string)($req->json['email'] ?? '');
        $name  = (string)($req->json['name'] ?? '');
        $pass  = (string)($req->json['password'] ?? '');
        $role  = (string)($req->json['role'] ?? '');

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email';
        if ($name === '')                               $errors['name']  = 'Name required';
        if ($role === '')                               $errors['role']  = 'role required';
        if (strlen($pass) < 6)                          $errors['password'] = 'Min 6 chars';

        if ($errors) {
            return $res->json(
                ResponseFormatter::error('Validation failed', 422, $errors),
                422
            );
        }

        [$ok, $user] = $this->auth->register($email, $name, $pass, $role);
        if (!$ok) {
            return $res->json(
                ResponseFormatter::error($user, 400),
                400
            );
        }

        $payload = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
        ];

        return $res->json(
            ResponseFormatter::success('Success', $payload, 201),
            201
        );
    }

    public function login(Request $req, Response $res): Response
    {
        [$valid, $errors, $payload] = AuthLoginRequest::validate($req->json);
        if (!$valid) {
            return $res->json(
                ResponseFormatter::error('Input invalid', 422, $errors),
                422
            );
        }

        [$ok, $out] = $this->auth->login($payload['email'], $payload['password']);
        if (!$ok) {
            return $res->json(
                ResponseFormatter::error('Wrong email/password', 400),
                400
            );
        }

        $data = [
            'token' => $out['token'],
            'user'  => [
                'id'    => $out['user']['id'],
                'name'  => $out['user']['name'],
                'email' => $out['user']['email'],
                'role_id' => $out['user']['role_id'],
                'last_login_id' => $out['user']['last_login_at'],
            ],
        ];

        return $res->json(
            ResponseFormatter::success('Success', $data, 200),
            200
        );
    }
}
