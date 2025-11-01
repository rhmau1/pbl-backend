<?php

namespace App\Helpers;

class ResponseFormatter
{
    public static function success($message = 'Success', $data = null, $code = 200)
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }

    public static function error($message = 'Error', $code = 400, $data = null)
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}

