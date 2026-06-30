<?php

declare(strict_types=1);

require_once __DIR__ . '/avisos_defaults.php';

function wiznet_avisos_storage_path(): string
{
    return dirname(__DIR__) . '/storage/avisos.php';
}

function wiznet_avisos_seed_records(): array
{
    $seed = [];
    $nextId = 1;

    foreach ([
        'advertencia' => 10,
        'recomendacion' => 10,
    ] as $tipo => $initialOrder) {
        $order = $initialOrder;

        foreach (wiznet_default_avisos()[$tipo] ?? [] as $aviso) {
            $seed[] = [
                'id' => $nextId++,
                'titulo' => (string) ($aviso['titulo'] ?? ''),
                'tipo' => $tipo,
                'contenido' => (string) ($aviso['contenido'] ?? ''),
                'activo' => 1,
                'orden' => $order,
                'created_at' => date('c'),
            ];

            $order += 10;
        }
    }

    return $seed;
}

function wiznet_avisos_normalize_record(array $record): array
{
    return [
        'id' => max(1, (int) ($record['id'] ?? 0)),
        'titulo' => trim((string) ($record['titulo'] ?? '')),
        'tipo' => (string) ($record['tipo'] ?? 'advertencia'),
        'contenido' => trim((string) ($record['contenido'] ?? '')),
        'activo' => (int) (!empty($record['activo'])),
        'orden' => (int) ($record['orden'] ?? 0),
        'created_at' => (string) ($record['created_at'] ?? date('c')),
    ];
}

function wiznet_avisos_sort_records(array &$records): void
{
    usort(
        $records,
        static function (array $left, array $right): int {
            $typeWeight = static fn(string $tipo): int => $tipo === 'advertencia' ? 0 : 1;

            $leftType = $typeWeight((string) ($left['tipo'] ?? ''));
            $rightType = $typeWeight((string) ($right['tipo'] ?? ''));

            if ($leftType !== $rightType) {
                return $leftType <=> $rightType;
            }

            $leftOrder = (int) ($left['orden'] ?? 0);
            $rightOrder = (int) ($right['orden'] ?? 0);

            if ($leftOrder !== $rightOrder) {
                return $leftOrder <=> $rightOrder;
            }

            return strcmp((string) ($right['created_at'] ?? ''), (string) ($left['created_at'] ?? ''));
        }
    );
}

function wiznet_avisos_write_records(array $records): void
{
    $normalized = array_map(static fn(array $record): array => wiznet_avisos_normalize_record($record), $records);
    $export = var_export(array_values($normalized), true);

    $storagePath = wiznet_avisos_storage_path();
    $storageDir = dirname($storagePath);

    if (!is_dir($storageDir) && !mkdir($storageDir, 0777, true) && !is_dir($storageDir)) {
        throw new RuntimeException('No fue posible preparar el almacenamiento de avisos.');
    }

    $content = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . $export . ";\n";

    if (file_put_contents($storagePath, $content, LOCK_EX) === false) {
        throw new RuntimeException('No fue posible guardar los avisos.');
    }
}

function wiznet_avisos_load_records(): array
{
    static $records = null;

    if (is_array($records)) {
        return $records;
    }

    $storagePath = wiznet_avisos_storage_path();

    if (!is_file($storagePath)) {
        $records = wiznet_avisos_seed_records();
        wiznet_avisos_write_records($records);

        return $records;
    }

    $decoded = require $storagePath;
    if (!is_array($decoded)) {
        throw new RuntimeException('El almacenamiento de avisos no contiene una estructura valida.');
    }

    $records = array_map(
        static fn(array $record): array => wiznet_avisos_normalize_record($record),
        array_values(array_filter($decoded, static fn($item): bool => is_array($item)))
    );

    return $records;
}

function wiznet_avisos_fetch_public(): array
{
    $records = array_values(array_filter(
        wiznet_avisos_load_records(),
        static fn(array $record): bool => (int) ($record['activo'] ?? 0) === 1
    ));

    wiznet_avisos_sort_records($records);

    return $records;
}

function wiznet_avisos_fetch_all(): array
{
    $records = wiznet_avisos_load_records();
    wiznet_avisos_sort_records($records);

    return $records;
}

function wiznet_avisos_find(int $id): ?array
{
    foreach (wiznet_avisos_load_records() as $record) {
        if ((int) ($record['id'] ?? 0) === $id) {
            return $record;
        }
    }

    return null;
}

function wiznet_avisos_save(?int $id, string $titulo, string $tipo, string $contenido, int $activo, int $orden): void
{
    $records = wiznet_avisos_load_records();
    $timestamp = date('c');

    if ($id !== null && $id > 0) {
        foreach ($records as $index => $record) {
            if ((int) ($record['id'] ?? 0) !== $id) {
                continue;
            }

            $records[$index] = [
                'id' => $id,
                'titulo' => $titulo,
                'tipo' => $tipo,
                'contenido' => $contenido,
                'activo' => $activo,
                'orden' => $orden,
                'created_at' => (string) ($record['created_at'] ?? $timestamp),
            ];

            wiznet_avisos_write_records($records);

            return;
        }

        throw new RuntimeException('El aviso que intentaste actualizar no existe.');
    }

    $maxId = 0;
    foreach ($records as $record) {
        $maxId = max($maxId, (int) ($record['id'] ?? 0));
    }

    $records[] = [
        'id' => $maxId + 1,
        'titulo' => $titulo,
        'tipo' => $tipo,
        'contenido' => $contenido,
        'activo' => $activo,
        'orden' => $orden,
        'created_at' => $timestamp,
    ];

    wiznet_avisos_write_records($records);
}

function wiznet_avisos_delete(int $id): bool
{
    $records = wiznet_avisos_load_records();
    $filtered = array_values(array_filter(
        $records,
        static fn(array $record): bool => (int) ($record['id'] ?? 0) !== $id
    ));

    if (count($filtered) === count($records)) {
        return false;
    }

    wiznet_avisos_write_records($filtered);

    return true;
}
