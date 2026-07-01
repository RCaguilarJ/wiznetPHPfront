<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
avisos_require_auth();

$redirectPath = avisos_sanitize_return_path($_GET['return'] ?? 'dashboard');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
avisos_require_csrf($_GET['token'] ?? null);

if ($id === false || $id === null) {
    avisos_set_flash('error', 'El identificador del aviso no es valido.');
    avisos_redirect($redirectPath);
}

try {
    $deleted = wiznet_avisos_delete($id);
    avisos_set_flash($deleted ? 'success' : 'error', $deleted ? 'Aviso eliminado correctamente.' : 'El aviso ya no existe.');
} catch (Throwable $exception) {
    avisos_set_flash('error', 'No fue posible eliminar el aviso.');
}

avisos_redirect($redirectPath);
