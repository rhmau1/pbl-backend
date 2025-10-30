<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../../config/jwt.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/ResponseFormatter.php';

class Middleware
{
    public static function authenticate()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            ResponseFormatter::error("Missing Authorization", 401);
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);

        $jwtConfig = require __DIR__ . '/../../config/jwt.php';
        $secret = $jwtConfig['secret'];

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            return (array)$decoded->user;
        } catch (\Firebase\JWT\ExpiredException $e) {
            ResponseFormatter::error("Token expired", 401);
        } catch (\Exception $e) {
            ResponseFormatter::error("Invalid token", 401);
        }
    }
}
