<?php

namespace App\Helpers;

class ResponseFormatter
{
    private static function respond($message, $data, $code)
    {
        http_response_code($code);
        header('Content-Type: application/json');

        echo json_encode([
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ]);
        exit;
    }

    public static function success($message = 'Success', $data = null, $code = 200)
    {
        self::respond($message, $data, $code);
    }

    public static function error($message = 'Error', $code = 400, $data = null)
    {
        self::respond($message, $data, $code);
    }
}
