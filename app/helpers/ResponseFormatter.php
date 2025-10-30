<?php

class ResponseFormatter
{
    public static function success($message = 'Success', $data = null, $code = 200)
    {
        http_response_code($code);

        echo json_encode([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public static function error($message = 'Error', $code = 400, $data = null)
    {
        http_response_code($code);

        echo json_encode([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}
