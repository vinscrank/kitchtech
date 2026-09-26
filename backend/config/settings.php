<?php

declare(strict_types=1);

return [
  'debug' => filter_var(getenv('APP_DEBUG') ?: '0', FILTER_VALIDATE_BOOL),
  'corsOrigins' => array_values(array_filter(array_map(
    trim(...),
    explode(',', getenv('CORS_ORIGIN') ?: 'http://localhost:5173,http://localhost:5174'),
  ))),
  'db' => [
    'host' => getenv('DB_HOST') ?: 'db',
    'port' => getenv('DB_PORT') ?: '3306',
    'name' => getenv('DB_NAME') ?: 'kitchtech',
    'user' => getenv('DB_USER') ?: 'kitchtech',
    'password' => getenv('DB_PASSWORD') ?: 'kitchtech',
  ],
];
