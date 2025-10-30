<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../../config/jwt.php';
require_once __DIR__ . '/../models/User.php';

class Middleware
{
    public static function authenticate()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Missing Authorization"]);
            exit;
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);

        $jwtConfig = require __DIR__ . '/../../config/jwt.php';
        $secret = $jwtConfig['secret'];

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (array)$decoded->user;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid token"]);
            exit;
        }
    }
}
