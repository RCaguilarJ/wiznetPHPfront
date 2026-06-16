<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_readable($composerAutoload)) {
    require_once $composerAutoload;
}

require_once __DIR__ . '/functions.php';

$site = site_config();
