<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return [
    "secret" => $_ENV['JWT_SECRET'],
    "issuer" => $_ENV['JWT_ISSUER'],
    "audience" => $_ENV['JWT_AUDIENCE'],
    "expires" => (int) $_ENV['JWT_EXPIRES']
];
