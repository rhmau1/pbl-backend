<?php
namespace App\Core;

abstract class Controller
{
    protected function json(Response $res, mixed $data, int $status=200): Response { return $res->json($data, $status); }
}
