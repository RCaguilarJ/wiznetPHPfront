<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function wiznet_avisos_db_config(): array
{
    static $dbConfig = null;

    if ($dbConfig !== null) {
        return $dbConfig;
    }

    $config = require_once __DIR__ . '/config.php';
    $dbConfig = $config['db'] ?? [];

    foreach (['host', 'port', 'name', 'user', 'pass', 'charset'] as $key) {
        if (!array_key_exists($key, $dbConfig)) {
            throw new RuntimeException(sprintf('Falta la configuracion de base de datos: %s.', $key));
        }
    }

    return $dbConfig;
}

function wiznet_avisos_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbConfig = wiznet_avisos_db_config();
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $dbConfig['host'],
        (int) $dbConfig['port'],
        $dbConfig['name'],
        $dbConfig['charset']
    );

    $pdo = new PDO(
        $dsn,
        $dbConfig['user'],
        $dbConfig['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}
