<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/ResponseFormatter.php';

class AuthController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    private function input()
    {
        $json = json_decode(file_get_contents("php://input"), true);
        return $json ?? $_POST;
    }

    public function register()
    {
        $data = $this->input();

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return ResponseFormatter::error(
                "Missing required fields",
                400
            );
        }

        $ok = $this->user->createUser($data['name'], $data['email'], $data['password']);

        if (!$ok) {
            return ResponseFormatter::error(
                "User registration failed",
                500
            );
        }

        return ResponseFormatter::success(
            "Success registered",
            [
                "name" => $data['name'],
                "email" => $data['email']
            ],
            201
        );
    }

    public function login()
    {
        $data = $this->input();

        if (empty($data['email']) || empty($data['password'])) {
            return ResponseFormatter::error("Missing email or password", 400);
        }

        $user = $this->user->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            return ResponseFormatter::error("Invalid credentials", 401);
        }

        $jwtConfig = require __DIR__ . '/../../config/jwt.php';

        $payload = [
            "iss" => $jwtConfig['issuer'],
            "aud" => $jwtConfig['audience'],
            "exp" => time() + $jwtConfig['expires'],
            "user" => [
                "id" => $user['id'],
                "email" => $user['email'],
                "name" => $user['name']
            ]
        ];

        $token = JWT::encode($payload, $jwtConfig['secret'], 'HS256');

        return ResponseFormatter::success(
            "Login success",
            [
                "token" => $token,
                "user" => [
                    "id"    => $user['id'],
                    "name"  => $user['name'],
                    "email" => $user['email'],
                ]
            ]
        );
    }
}
