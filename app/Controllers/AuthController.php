<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Helpers\ResponseFormatter;
use App\Requests\AuthLoginRequest;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function __construct(private ?AuthService $auth = null)
    {
        $this->auth ??= new AuthService();
    }

    public function register(Request $req)
    {
        $body = $req->json ?: ($req->body ?? $_POST ?? []);
        $email = (string) ($body['email'] ?? '');
        $name = (string) ($body['name'] ?? '');
        $pass = (string) ($body['password'] ?? '');

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email';
        }
        if ($name === '') {
            $errors['name'] = 'Name required';
        }
        if (strlen($pass) < 6) {
            $errors['password'] = 'Min 6 chars';
        }
        if ($errors) {
            ResponseFormatter::error('Validation failed', 422, $errors);
        }

        [$ok, $res] = $this->auth->register($email, $name, $pass);
        if (!$ok) {
            $msg = is_string($res) ? $res : 'Register failed';
            ResponseFormatter::error('Register failed', 400, ['detail' => $msg]);
        }

        $id = is_array($res) ? ($res['id'] ?? null) : ($res->id ?? null);
        $outName = is_array($res) ? ($res['name'] ?? null) : ($res->name ?? null);
        $outEmail = is_array($res) ? ($res['email'] ?? null) : ($res->email ?? null);

        $payload = [
            'id' => $id,
            'name' => $outName ?? $name,
            'email' => $outEmail ?? $email,
        ];

        ResponseFormatter::success('Success', $payload, 201);
    }


    public function login(Request $req)
    {
        $body = $req->json ?: ($req->body ?? $_POST ?? []);
        $payload = [
            'email' => isset($body['email']) ? (string) $body['email'] : '',
            'password' => isset($body['password']) ? (string) $body['password'] : '',
        ];

        if (class_exists(AuthLoginRequest::class)) {
            [$valid, $errors, $payload] = AuthLoginRequest::validate($body);
            if (!$valid) {
                ResponseFormatter::error('Input invalid', 422, $errors);
            }
        } else {
            $errors = [];
            if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL))
                $errors['email'] = 'Invalid email';
            if ($payload['password'] === '')
                $errors['password'] = 'Password required';
            if ($errors)
                ResponseFormatter::error('Input invalid', 422, $errors);
        }

        [$ok, $out] = $this->auth->login($payload['email'], $payload['password']);
        if (!$ok) {
            $msg = is_string($out) ? $out : 'Wrong email/password';
            ResponseFormatter::error('Wrong email/password', 401, ['detail' => $msg]);
        }

        $token = is_array($out) ? ($out['token'] ?? null) : ($out->token ?? null);
        $usr = is_array($out) ? ($out['user'] ?? null) : ($out->user ?? null);

        $uid = is_array($usr) ? ($usr['id'] ?? null) : ($usr->id ?? null);
        $uname = is_array($usr) ? ($usr['name'] ?? null) : ($usr->name ?? null);
        $uemail = is_array($usr) ? ($usr['email'] ?? null) : ($usr->email ?? null);

        $data = [
            'token' => $token,
            'user' => [
                'id' => $uid,
                'name' => $uname,
                'email' => $uemail,
            ],
        ];

        ResponseFormatter::success('Success', $data, 200);
    }

}
