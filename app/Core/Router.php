<?php

namespace App\Core;

use App\Helpers\ResponseFormatter;

final class Router
{
    private array $routes = [];
    private string $prefix = '';

    public function group(string $prefix, callable $cb): void
    {
        $prev = $this->prefix;
        $this->prefix = rtrim($prev . '/' . ltrim($prefix, '/'), '/');
        $cb($this);
        $this->prefix = $prev;
    }

    public function get(string $p, callable|array $h): Route
    {
        return $this->map('GET', $p, $h);
    }
    public function post(string $p, callable|array $h): Route
    {
        return $this->map('POST', $p, $h);
    }
    public function put(string $p, callable|array $h): Route
    {
        return $this->map('PUT', $p, $h);
    }
    public function delete(string $p, callable|array $h): Route
    {
        return $this->map('DELETE', $p, $h);
    }

    private function map(string $m, string $p, callable|array $h): Route
    {
        $full = rtrim($this->prefix . '/' . ltrim($p, '/'), '/') ?: '/';

        $r = new Route($m, $full, $h);
        $this->routes[$m][] = $r;
        return $r;
    }


    public function dispatch(Request $req, Response $res): Response
    {
        $pathMatched = false;

        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $route) {
                $params = $route->match($req->path);

                if ($params !== null) {
                    if ($method !== $req->method) {
                        $pathMatched = true;
                        continue;
                    }

                    $core = function (Request $rq) use ($route, $res, $params): Response {
                        return $route->run($rq, $res, $params);
                    };

                    $next = $core;
                    $middlewares = array_reverse($route->getMiddlewares());
                    foreach ($middlewares as $mw) {
                        $next = function (Request $rq) use ($mw, $next): Response {
                            return $mw->process($rq, $next);
                        };
                    }

                    return $next($req);
                }
            }
        }

        if ($pathMatched) {
            return $res->json(
                ResponseFormatter::error('Method not allowed', 405),
                405
            );
        }

        return $res->json(
            ResponseFormatter::error('Route not found', 404),
            404
        );
    }
}

final class Route
{
    private array $middlewares = [];

    public function __construct(private string $method, private string $pattern, private $handler) {}

    public function middleware($m): self
    {
        $this->middlewares[] = $m;
        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function match(string $path): ?array
    {
        $regex = preg_replace('#\{([^/]+)\}#', '(?P<$1>[^/]+)', $this->pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $path, $m)) {
            $named = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            return $named;
        }
        return null;
    }

    public function run(Request $req, Response $res, array $params): Response
    {
        $handler = $this->handler;

        if (is_array($handler)) {
            [$cls, $met] = $handler;
            $c = new $cls;
            return $c->{$met}($req, $res, $params);
        }

        return $handler($req, $res);
    }
}
