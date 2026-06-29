<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
avisos_require_auth();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
avisos_require_csrf($_GET['token'] ?? null);

if ($id === false || $id === null) {
    avisos_set_flash('error', 'El identificador del aviso no es valido.');
    avisos_redirect('dashboard.php');
}

try {
    $pdo = avisos_pdo();
    $statement = $pdo->prepare('DELETE FROM avisos WHERE id = :id');
    $statement->execute(['id' => $id]);

    avisos_set_flash('success', 'Aviso eliminado correctamente.');
} catch (Throwable $exception) {
    avisos_set_flash('error', 'No fue posible eliminar el aviso.');
}

avisos_redirect('dashboard.php');
