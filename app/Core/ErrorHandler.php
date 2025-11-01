<?php
namespace App\Core;

final class ErrorHandler
{
    public static function register(): void {
        set_exception_handler(function(\Throwable $e){
            $code = (int)$e->getCode();
            if ($code < 400 || $code >= 600) $code = 500;
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok'=>false,'error'=>['code'=>'EXCEPTION','message'=>$e->getMessage()]], JSON_UNESCAPED_UNICODE);
        });
    }
}
