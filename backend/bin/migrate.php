<?php

declare(strict_types=1);

use App\Shared\Database\Migrator;
use App\Shared\Database\PdoFactory;

require dirname(__DIR__).'/vendor/autoload.php';

$settings = require dirname(__DIR__).'/config/settings.php';
$pdo = (new PdoFactory())->create($settings['db']);
(new Migrator($pdo, dirname(__DIR__).'/database/migrations'))->migrate();