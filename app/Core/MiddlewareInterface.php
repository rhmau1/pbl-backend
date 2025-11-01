<?php
namespace App\Core;

interface MiddlewareInterface
{
    public function process(Request $request, callable $next): Response;
}

final class MiddlewarePipeline
{
    public function __construct(private array $middlewares = [])
    {
    }
    public function handle(Request $r, callable $core): Response
    {
        $runner = array_reduce(
            array_reverse($this->middlewares),
            fn($next, MiddlewareInterface $m) => fn(Request $req) => $m->process($req, $next),
            fn(Request $req) => $core($req)
        );
        return $runner($r);
    }
}
