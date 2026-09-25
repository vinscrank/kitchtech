<?php

declare(strict_types=1);

return [
  'debug' => filter_var(getenv('APP_DEBUG') ?: '0', FILTER_VALIDATE_BOOL),
  'corsOrigin' => getenv('CORS_ORIGIN') ?: 'http://localhost:5173',
  'db' => [
    'host' => getenv('DB_HOST') ?: 'db',
    'port' => getenv('DB_PORT') ?: '3306',
    'name' => getenv('DB_NAME') ?: 'kitchtech',
    'user' => getenv('DB_USER') ?: 'kitchtech',
    'password' => getenv('DB_PASSWORD') ?: 'kitchtech',
  ],
];
