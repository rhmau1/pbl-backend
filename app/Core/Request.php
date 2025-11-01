<?php

namespace App\Core;

final class Request
{
    public array $attributes = [];
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $headers,
        public readonly array $query,
        public readonly array $json
    ) {}

    public function withAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?? '/', '/') ?: '/';

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $query = $_GET ?? [];

        $raw = file_get_contents('php://input') ?: '';
        $type = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_starts_with($type, 'application/json')) {
            $json = json_decode($raw, true) ?: [];
        } elseif (str_starts_with($type, 'application/x-www-form-urlencoded')) {
            parse_str($raw, $json);
        } else {
            $json = $_POST;
        }

        return new self($method, $path, $headers, $query, $json);
    }

    public function header(string $key, ?string $default = null): ?string
    {
        foreach ($this->headers as $k => $v)
            if (strcasecmp($k, $key) === 0)
                return $v;
        return $default;
    }
}
