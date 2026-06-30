<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
avisos_require_auth();

if (avisos_request_method() !== 'POST') {
    avisos_redirect('dashboard.php');
}

avisos_require_csrf($_POST['csrf_token'] ?? null);

$redirectPath = avisos_sanitize_return_path($_POST['redirect_to'] ?? 'dashboard.php');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$titulo = avisos_normalize_string((string) ($_POST['titulo'] ?? ''));
$tipo = avisos_normalize_string((string) ($_POST['tipo'] ?? ''));
$contenido = avisos_normalize_multiline((string) ($_POST['contenido'] ?? ''));
$ordenRaw = $_POST['orden'] ?? '0';
$orden = filter_var($ordenRaw, FILTER_VALIDATE_INT);
$activo = isset($_POST['activo']) ? 1 : 0;

$errors = [];

if ($titulo === '') {
    $errors['titulo'] = 'El titulo es obligatorio.';
}

if (!in_array($tipo, avisos_allowed_types(), true)) {
    $errors['tipo'] = 'Selecciona un tipo valido.';
}

if ($contenido === '') {
    $errors['contenido'] = 'El contenido es obligatorio.';
}

if ($orden === false) {
    $orden = 0;
}

$formData = [
    'id' => $id !== false && $id !== null ? (string) $id : '',
    'titulo' => $titulo,
    'tipo' => $tipo,
    'contenido' => $contenido,
    'activo' => $activo,
    'orden' => (int) $orden,
];

if ($errors !== []) {
    avisos_set_form_state([
        'data' => $formData,
        'errors' => $errors,
    ]);
    avisos_set_flash('error', 'Revisa los datos del formulario.');
    avisos_redirect($redirectPath);
}

try {
    if ($id !== false && $id !== null) {
        wiznet_avisos_save((int) $id, $titulo, $tipo, $contenido, $activo, (int) $orden);
        avisos_set_flash('success', 'Aviso actualizado correctamente.');
    } else {
        wiznet_avisos_save(null, $titulo, $tipo, $contenido, $activo, (int) $orden);
        avisos_set_flash('success', 'Aviso creado correctamente.');
    }
} catch (Throwable $exception) {
    avisos_set_form_state([
        'data' => $formData,
        'errors' => [],
    ]);
    avisos_set_flash('error', 'No fue posible guardar el aviso.');
}

avisos_redirect($redirectPath);
