<?php

declare(strict_types=1);

function wiznet_db_connect(array $dbConfig): array
{
    mysqli_report(MYSQLI_REPORT_OFF);

    $connection = @new mysqli(
        $dbConfig['host'],
        $dbConfig['user'],
        $dbConfig['pass'],
        $dbConfig['name'],
        $dbConfig['port']
    );

    if ($connection->connect_errno) {
        return [
            'connection' => null,
            'error' => sprintf(
                'No fue posible conectar con MySQL (%s).',
                $connection->connect_error ?: 'error desconocido'
            ),
        ];
    }

    $connection->set_charset($dbConfig['charset']);

    return [
        'connection' => $connection,
        'error' => null,
    ];
}
