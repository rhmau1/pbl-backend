<?php
header("Content-Type: application/json");

// CORS (Opsional, tapi disarankan jika pakai frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Hentikan preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require '../vendor/autoload.php';
require '../app/helpers/ResponseFormatter.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/core/Middleware.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';

// Parsing URL
$url = $_GET['url'] ?? '';
$url = trim($url, '/');
$urlParts = explode('/', filter_var($url, FILTER_SANITIZE_URL));

$method = $_SERVER['REQUEST_METHOD'];

try {

    // Default nilai
    $controller = null;
    $action = '';
    $params = [];
    $protected = false;

    $apiPrefix = $urlParts[0] ?? '';
    $resource  = $urlParts[1] ?? '';
    $id        = $urlParts[2] ?? null;

    // Pastikan prefix /api
    if ($apiPrefix !== 'api') {
        throw new Exception("Invalid API endpoint.", 404);
    }

    // =====================
    // PUBLIC ROUTES
    // =====================
    if ($resource === 'register' && $method === 'POST') {
        $controller = new AuthController();
        $action = 'register';
    } elseif ($resource === 'login' && $method === 'POST') {
        $controller = new AuthController();
        $action = 'login';
    }

    // =====================
    // PROTECTED ROUTES
    // =====================
    elseif ($resource === 'users') {
        $controller = new UserController();
        $protected = false;

        if ($method === 'GET') {
            $action = 'list';
        } else {
            throw new Exception("Method not allowed", 405);
        }
    } elseif ($resource === 'user') {
        $controller = new UserController();
        $protected = true;

        if ($method === 'GET' && $id) {
            $action = 'get';
            $params = [$id];
        } elseif ($method === 'PUT' && $id) {
            $action = 'update';
            $params = [$id];
        } elseif ($method === 'DELETE' && $id) {
            $action = 'delete';
            $params = [$id];
        } else {
            throw new Exception("Invalid user route or missing ID", 400);
        }
    }

    // Tidak cocok route manapun
    else {
        throw new Exception("Route not found", 404);
    }

    // Cek login untuk route protected
    if ($protected) {
        Middleware::authenticate();
    }

    // Panggil controller
    if (!method_exists($controller, $action)) {
        throw new Exception("Action not found", 500);
    }

    call_user_func_array([$controller, $action], $params);
} catch (Exception $e) {
    $code = $e->getCode();
    if ($code < 400) $code = 500;

    http_response_code($code);
    echo json_encode(["message" => $e->getMessage()]);
}
