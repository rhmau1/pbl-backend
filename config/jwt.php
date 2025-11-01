<?php
return [
  'secret'   => $_ENV['JWT_SECRET']   ?? 'dev_secret_change_me',
  'issuer'   => $_ENV['JWT_ISSUER']   ?? ($_ENV['APP_URL'] ?? 'http://localhost:8080'),
  'audience' => $_ENV['JWT_AUDIENCE'] ?? ($_ENV['FE_URL']  ?? 'http://localhost:3000'),
  'ttl'      => (int)($_ENV['JWT_EXPIRES'] ?? 3600),
  'leeway'   => 30,
];
