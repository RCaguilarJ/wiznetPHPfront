<?php

declare(strict_types=1);

function wiznet_load_env(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || substr($line, 0, 1) === '#' || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($value !== '' && $value[0] === '"' && substr($value, -1) === '"') {
            $value = substr($value, 1, -1);
        }

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

wiznet_load_env(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

return [
    'db' => [
        'host' => getenv('WIZNET_DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('WIZNET_DB_PORT') ?: '3306'),
        'name' => getenv('WIZNET_DB_NAME') ?: 'wiznet',
        'user' => getenv('WIZNET_DB_USER') ?: 'root',
        'pass' => getenv('WIZNET_DB_PASS') ?: '',
        'charset' => getenv('WIZNET_DB_CHARSET') ?: 'utf8mb4',
    ],
    'site' => [
        'name' => 'WIZNET',
        'base_path' => getenv('WIZNET_BASE_PATH') ?: '',
    ],
];
