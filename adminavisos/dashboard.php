<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
avisos_require_auth();

$flash = avisos_pull_flash();
$formState = avisos_pull_form_state();
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$formErrors = [];
$isEditing = false;

$defaultForm = [
    'id' => '',
    'titulo' => '',
    'tipo' => 'advertencia',
    'contenido' => '',
    'activo' => 1,
    'orden' => 0,
];

$formValues = $defaultForm;

try {
    $pdo = avisos_pdo();

    if ($formState !== null) {
        $formValues = array_merge($defaultForm, $formState['data'] ?? []);
        $formErrors = $formState['errors'] ?? [];
        $isEditing = !empty($formValues['id']);
    } elseif ($editId !== false && $editId !== null) {
        $editStatement = $pdo->prepare(
            'SELECT id, titulo, tipo, contenido, activo, orden
            FROM avisos
            WHERE id = :id
            LIMIT 1'
        );
        $editStatement->execute(['id' => $editId]);
        $editingAviso = $editStatement->fetch();

        if ($editingAviso === false) {
            avisos_set_flash('error', 'El aviso que intentaste editar no existe.');
            avisos_redirect('dashboard.php');
        }

        $formValues = [
            'id' => (string) $editingAviso['id'],
            'titulo' => (string) $editingAviso['titulo'],
            'tipo' => (string) $editingAviso['tipo'],
            'contenido' => (string) $editingAviso['contenido'],
            'activo' => (int) $editingAviso['activo'],
            'orden' => (int) $editingAviso['orden'],
        ];
        $isEditing = true;
    }

    $listStatement = $pdo->prepare(
        "SELECT id, titulo, tipo, contenido, activo, orden, created_at
        FROM avisos
        ORDER BY CASE WHEN tipo = 'advertencia' THEN 0 ELSE 1 END, orden ASC, created_at DESC"
    );
    $listStatement->execute();
    $avisos = $listStatement->fetchAll();
} catch (Throwable $exception) {
    $avisos = [];
    $flash = [
        'type' => 'error',
        'message' => 'No fue posible cargar la informacion del panel.',
    ];
}

function avisos_preview(string $content, int $limit = 90): string
{
    $normalized = preg_replace('/\s+/u', ' ', trim($content)) ?? trim($content);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($normalized) <= $limit) {
            return $normalized;
        }

        return mb_substr($normalized, 0, $limit - 1) . '...';
    }

    if (strlen($normalized) <= $limit) {
        return $normalized;
    }

    return substr($normalized, 0, $limit - 3) . '...';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Avisos | Dashboard</title>
    <style>
        :root {
            --blue-900: #0b3f82;
            --blue-800: #21469c;
            --blue-700: #274db2;
            --green-600: #10a735;
            --red-600: #cf171f;
            --ink-900: #121212;
            --ink-700: #4b5563;
            --surface: #ffffff;
            --surface-soft: #f5f7fb;
            --border: #d9dfe8;
            --shadow: 0 18px 42px rgba(15, 33, 63, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink-900);
            background: var(--surface-soft);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(1180px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 1.5rem 0 2rem;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, var(--blue-900), var(--blue-800));
            color: #fff;
            box-shadow: var(--shadow);
        }

        .topbar h1 {
            margin: 0;
            font-size: 1.65rem;
        }

        .topbar p {
            margin: 0.25rem 0 0;
            color: rgba(255, 255, 255, 0.85);
        }

        .topbar__actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .button,
        .button-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0.7rem 1rem;
            border: 0;
            background: var(--blue-800);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .button:hover,
        .button-link:hover {
            background: var(--blue-700);
        }

        .button-link--danger {
            background: var(--red-600);
        }

        .button-link--secondary {
            background: #3b4452;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.9fr);
            gap: 1.5rem;
        }

        .panel {
            background: var(--surface);
            border: 1px solid rgba(33, 70, 156, 0.1);
            box-shadow: var(--shadow);
        }

        .panel__header {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .panel__header h2 {
            margin: 0;
            font-size: 1.15rem;
        }

        .panel__body {
            padding: 1.25rem;
        }

        .alert {
            margin-bottom: 1rem;
            padding: 0.9rem 1rem;
            font-weight: 600;
        }

        .alert-success {
            color: #0e7d29;
            background: rgba(22, 163, 55, 0.12);
            border: 1px solid rgba(22, 163, 55, 0.24);
        }

        .alert-error {
            color: #9d1e25;
            background: rgba(207, 31, 40, 0.1);
            border: 1px solid rgba(207, 31, 40, 0.18);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid #edf1f6;
            text-align: left;
            vertical-align: top;
        }

        th {
            font-size: 0.85rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: var(--ink-700);
        }

        .status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            padding: 0.35rem 0.65rem;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .status--active {
            color: #0e7d29;
            background: rgba(22, 163, 55, 0.12);
        }

        .status--inactive {
            color: #9d1e25;
            background: rgba(207, 31, 40, 0.1);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.65rem;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .badge--warning {
            color: #fff;
            background: var(--red-600);
        }

        .badge--recommendation {
            color: #fff;
            background: var(--green-600);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .actions a {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 700;
            color: #fff;
            background: var(--blue-800);
        }

        .actions .delete-link {
            background: var(--red-600);
        }

        form label {
            display: block;
            margin-bottom: 1rem;
        }

        form label > span {
            display: block;
            margin-bottom: 0.45rem;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 0.85rem 0.95rem;
            border: 1px solid var(--border);
        }

        textarea {
            min-height: 170px;
            resize: vertical;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1.25rem;
        }

        .checkbox-row input {
            width: auto;
        }

        .field-error {
            display: block;
            margin-top: 0.35rem;
            color: #9d1e25;
            font-size: 0.88rem;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .empty-state {
            margin: 0;
            color: var(--ink-700);
        }

        @media (max-width: 980px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .shell {
                width: min(100%, calc(100% - 1rem));
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <h1>Admin Avisos</h1>
                <p>Gestion de avisos publicos para <code>/avisos</code>.</p>
            </div>
            <div class="topbar__actions">
                <a class="button-link button-link--secondary" href="<?= avisos_e(avisos_admin_url('dashboard.php')) ?>">Nuevo aviso</a>
                <a class="button-link" href="<?= avisos_e(avisos_base_path() . '/avisos.php') ?>" target="_blank" rel="noopener">Ver pagina publica</a>
                <a class="button-link button-link--danger" href="<?= avisos_e(avisos_admin_url('logout.php')) ?>">Salir</a>
            </div>
        </header>

        <?php if (is_array($flash) && isset($flash['message'], $flash['type'])): ?>
            <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= avisos_e((string) $flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <section class="panel">
                <div class="panel__header">
                    <h2>Listado de avisos</h2>
                </div>
                <div class="panel__body table-wrap">
                    <?php if ($avisos === []): ?>
                        <p class="empty-state">Todavia no hay avisos registrados.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titulo</th>
                                    <th>Tipo</th>
                                    <th>Contenido</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
                                    <th>Creado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($avisos as $aviso): ?>
                                    <tr>
                                        <td><?= (int) $aviso['id'] ?></td>
                                        <td><?= avisos_e((string) $aviso['titulo']) ?></td>
                                        <td>
                                            <span class="badge <?= $aviso['tipo'] === 'advertencia' ? 'badge--warning' : 'badge--recommendation' ?>">
                                                <?= avisos_e((string) $aviso['tipo']) ?>
                                            </span>
                                        </td>
                                        <td><?= avisos_e(avisos_preview((string) $aviso['contenido'])) ?></td>
                                        <td><?= (int) $aviso['orden'] ?></td>
                                        <td>
                                            <span class="status <?= (int) $aviso['activo'] === 1 ? 'status--active' : 'status--inactive' ?>">
                                                <?= (int) $aviso['activo'] === 1 ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td><?= avisos_e((string) $aviso['created_at']) ?></td>
                                        <td>
                                            <div class="actions">
                                                <a href="<?= avisos_e(avisos_admin_url('dashboard.php?edit=' . (int) $aviso['id'])) ?>">Editar</a>
                                                <a class="delete-link" href="<?= avisos_e(avisos_admin_url('delete.php?id=' . (int) $aviso['id'] . '&token=' . urlencode(avisos_csrf_token()))) ?>">Eliminar</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="panel">
                <div class="panel__header">
                    <h2><?= $isEditing ? 'Editar aviso' : 'Agregar nuevo aviso' ?></h2>
                </div>
                <div class="panel__body">
                    <form method="post" action="<?= avisos_e(avisos_admin_url('save.php')) ?>">
                        <input type="hidden" name="csrf_token" value="<?= avisos_e(avisos_csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= avisos_e((string) $formValues['id']) ?>">

                        <label>
                            <span>Titulo</span>
                            <input type="text" name="titulo" maxlength="255" value="<?= avisos_e((string) $formValues['titulo']) ?>" required>
                            <?php if (isset($formErrors['titulo'])): ?>
                                <small class="field-error"><?= avisos_e((string) $formErrors['titulo']) ?></small>
                            <?php endif; ?>
                        </label>

                        <div class="form-grid">
                            <label>
                                <span>Tipo</span>
                                <select name="tipo" required>
                                    <?php foreach (avisos_allowed_types() as $tipo): ?>
                                        <option value="<?= avisos_e($tipo) ?>" <?= $formValues['tipo'] === $tipo ? 'selected' : '' ?>>
                                            <?= avisos_e($tipo) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($formErrors['tipo'])): ?>
                                    <small class="field-error"><?= avisos_e((string) $formErrors['tipo']) ?></small>
                                <?php endif; ?>
                            </label>

                            <label>
                                <span>Orden</span>
                                <input type="number" name="orden" step="1" value="<?= avisos_e((string) $formValues['orden']) ?>">
                            </label>
                        </div>

                        <label>
                            <span>Contenido</span>
                            <textarea name="contenido" required><?= avisos_e((string) $formValues['contenido']) ?></textarea>
                            <?php if (isset($formErrors['contenido'])): ?>
                                <small class="field-error"><?= avisos_e((string) $formErrors['contenido']) ?></small>
                            <?php endif; ?>
                        </label>

                        <label class="checkbox-row">
                            <input type="checkbox" name="activo" value="1" <?= (int) $formValues['activo'] === 1 ? 'checked' : '' ?>>
                            <span>Publicar este aviso</span>
                        </label>

                        <div class="form-actions">
                            <button class="button" type="submit"><?= $isEditing ? 'Guardar cambios' : 'Agregar aviso' ?></button>
                            <?php if ($isEditing): ?>
                                <a class="button-link button-link--secondary" href="<?= avisos_e(avisos_admin_url('dashboard.php')) ?>">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
