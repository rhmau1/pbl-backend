<?php
namespace App\Core;

final class Response
{
    private array $headers = [];
    private int $status = 200;
    private string $body = '';

    public function json(array $data, int $status = 200, array $headers = []): self
    {
        $this->status = (int)$status;
        $this->headers = array_merge([
            'Content-Type' => 'application/json; charset=utf-8',
            'Access-Control-Allow-Origin' => '*',
            'Vary' => 'Origin',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'X-Content-Type-Options' => 'nosniff',
        ], $headers);

        $this->body = json_encode($data, JSON_UNESCAPED_SLASHES);
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $k => $v) {
            header("$k: $v");
        }
        echo $this->body;
    }
}
