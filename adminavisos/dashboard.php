<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
avisos_require_auth();

$flash = avisos_pull_flash();
$formState = avisos_pull_form_state();
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$requestedPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$requestedPage = $requestedPage !== false && $requestedPage !== null ? $requestedPage : 1;
$searchQuery = trim((string) ($_GET['q'] ?? ''));
$tipoFilter = (string) ($_GET['tipo'] ?? 'all');
$estadoFilter = (string) ($_GET['estado'] ?? 'all');
$allowedStatus = ['all', 'active', 'inactive'];
$formErrors = [];
$isEditing = false;
$perPage = 8;

if (!in_array($tipoFilter, array_merge(['all'], avisos_allowed_types()), true)) {
    $tipoFilter = 'all';
}

if (!in_array($estadoFilter, $allowedStatus, true)) {
    $estadoFilter = 'all';
}

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
    if ($formState !== null) {
        $formValues = array_merge($defaultForm, $formState['data'] ?? []);
        $formErrors = $formState['errors'] ?? [];
        $isEditing = !empty($formValues['id']);
    } elseif ($editId !== false && $editId !== null) {
        $editingAviso = wiznet_avisos_find((int) $editId);

        if ($editingAviso === null) {
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

    $avisos = wiznet_avisos_fetch_all();
} catch (Throwable $exception) {
    $avisos = [];
    $flash = [
        'type' => 'error',
        'message' => 'No fue posible cargar la informacion del panel.',
    ];
}

function avisos_preview(string $content, int $limit = 110): string
{
    $normalized = preg_replace('/\s+/u', ' ', trim($content)) ?? trim($content);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($normalized) <= $limit) {
            return $normalized;
        }

        return mb_substr($normalized, 0, $limit - 1) . '…';
    }

    if (strlen($normalized) <= $limit) {
        return $normalized;
    }

    return substr($normalized, 0, $limit - 3) . '...';
}

function avisos_text_lower(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function avisos_format_datetime(string $value): string
{
    try {
        return (new DateTimeImmutable($value))->format('d/m/Y H:i');
    } catch (Throwable $exception) {
        return $value;
    }
}

function avisos_render_preview_html(array $formValues): string
{
    $title = trim((string) ($formValues['titulo'] ?? ''));
    $content = trim((string) ($formValues['contenido'] ?? ''));
    $tipo = (string) ($formValues['tipo'] ?? 'advertencia');
    $isActive = (int) ($formValues['activo'] ?? 0) === 1;

    $titleHtml = $title !== '' ? avisos_e($title) : 'Vista previa del titulo';
    $contentHtml = $content !== '' ? nl2br(avisos_e($content)) : 'Aqui se mostrara el contenido del aviso mientras editas.';
    $typeLabel = $tipo === 'recomendacion' ? 'Recomendación' : 'Advertencia';
    $typeClass = $tipo === 'recomendacion' ? 'preview-badge--recommendation' : 'preview-badge--warning';
    $statusLabel = $isActive ? 'Publicado' : 'Borrador';
    $statusClass = $isActive ? 'preview-status--active' : 'preview-status--draft';

    return sprintf(
        '<div class="preview-card">
            <div class="preview-card__meta">
                <span class="preview-badge %s">%s</span>
                <span class="preview-status %s">%s</span>
            </div>
            <h3 class="preview-card__title">%s</h3>
            <p class="preview-card__body">%s</p>
        </div>',
        avisos_e($typeClass),
        avisos_e($typeLabel),
        avisos_e($statusClass),
        avisos_e($statusLabel),
        $titleHtml,
        $contentHtml
    );
}

function avisos_dashboard_query(array $params): string
{
    $normalized = [];

    foreach ($params as $key => $value) {
        if ($value === null || $value === '' || $value === 'all') {
            continue;
        }

        if ($key === 'page' && (int) $value <= 1) {
            continue;
        }

        $normalized[$key] = (string) $value;
    }

    return $normalized === [] ? '' : '?' . http_build_query($normalized);
}

function avisos_dashboard_path(array $params = []): string
{
    return 'dashboard.php' . avisos_dashboard_query($params);
}

$stats = [
    'total' => count($avisos),
    'active' => count(array_filter($avisos, static fn(array $aviso): bool => (int) ($aviso['activo'] ?? 0) === 1)),
    'warnings' => count(array_filter($avisos, static fn(array $aviso): bool => (string) ($aviso['tipo'] ?? '') === 'advertencia')),
    'recommendations' => count(array_filter($avisos, static fn(array $aviso): bool => (string) ($aviso['tipo'] ?? '') === 'recomendacion')),
];

$filteredAvisos = array_values(array_filter(
    $avisos,
    static function (array $aviso) use ($searchQuery, $tipoFilter, $estadoFilter): bool {
        if ($tipoFilter !== 'all' && (string) ($aviso['tipo'] ?? '') !== $tipoFilter) {
            return false;
        }

        if ($estadoFilter === 'active' && (int) ($aviso['activo'] ?? 0) !== 1) {
            return false;
        }

        if ($estadoFilter === 'inactive' && (int) ($aviso['activo'] ?? 0) !== 0) {
            return false;
        }

        if ($searchQuery === '') {
            return true;
        }

        $needle = avisos_text_lower($searchQuery);
        $haystack = avisos_text_lower(implode(' ', [
            (string) ($aviso['titulo'] ?? ''),
            (string) ($aviso['contenido'] ?? ''),
            (string) ($aviso['id'] ?? ''),
        ]));

        return str_contains($haystack, $needle);
    }
));

$totalFiltered = count($filteredAvisos);
$totalPages = max(1, (int) ceil($totalFiltered / $perPage));
$currentPage = min($requestedPage, $totalPages);
$currentPage = max(1, $currentPage);
$offset = ($currentPage - 1) * $perPage;
$pageAvisos = array_slice($filteredAvisos, $offset, $perPage);
$rangeStart = $totalFiltered === 0 ? 0 : $offset + 1;
$rangeEnd = $totalFiltered === 0 ? 0 : min($offset + $perPage, $totalFiltered);

$listState = [
    'q' => $searchQuery,
    'tipo' => $tipoFilter,
    'estado' => $estadoFilter,
    'page' => $currentPage,
];
$listReturnPath = avisos_dashboard_path($listState);
$filtersActive = $searchQuery !== '' || $tipoFilter !== 'all' || $estadoFilter !== 'all';
$editingNoticeLabel = $isEditing
    ? sprintf(
        '#%d · %s',
        (int) $formValues['id'],
        (int) $formValues['activo'] === 1 ? 'Publicado' : 'Borrador'
    )
    : '';
$createFormValues = $isEditing ? $defaultForm : $formValues;
$createFormErrors = $isEditing ? [] : $formErrors;
$editFormValues = $formValues;
$editFormErrors = $isEditing ? $formErrors : [];
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
            --blue-950: #0a2d66;
            --blue-900: #0b3f82;
            --blue-800: #21469c;
            --blue-700: #274db2;
            --blue-100: #eaf1ff;
            --green-600: #10a735;
            --green-100: #e8f7ec;
            --red-600: #cf171f;
            --red-100: #fdecec;
            --amber-500: #f59e0b;
            --amber-100: #fff6dd;
            --ink-950: #0f172a;
            --ink-900: #121212;
            --ink-700: #475569;
            --ink-600: #64748b;
            --surface: #ffffff;
            --surface-soft: #f3f6fb;
            --surface-muted: #f8fafc;
            --border: #d9e2ef;
            --border-strong: #c5d3e6;
            --shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
            --radius: 18px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink-950);
            background:
                radial-gradient(circle at top right, rgba(33, 70, 156, 0.08), transparent 28%),
                linear-gradient(180deg, #eef4fb 0%, #f6f8fc 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        .shell {
            width: min(1380px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 1.5rem 0 2.5rem;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding: 1.25rem 1.35rem;
            background: linear-gradient(135deg, var(--blue-950), var(--blue-800));
            color: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .topbar h1 {
            margin: 0;
            font-size: clamp(1.35rem, 1.1rem + 0.7vw, 1.8rem);
        }

        .topbar p {
            margin: 0.35rem 0 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 0.95rem;
        }

        .topbar code {
            padding: 0.1rem 0.4rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
        }

        .topbar__actions,
        .form-actions,
        .filters__actions,
        .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .button,
        .button-link,
        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0.72rem 1rem;
            border: 0;
            border-radius: 12px;
            background: var(--blue-800);
            color: #fff;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            transition: transform 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
        }

        .button:hover,
        .button-link:hover,
        .pagination a:hover {
            background: var(--blue-700);
            transform: translateY(-1px);
        }

        .button-link--danger {
            background: var(--red-600);
        }

        .button-link--secondary {
            background: rgba(255, 255, 255, 0.16);
        }

        .button-link--ghost {
            background: transparent;
            color: var(--blue-800);
            border: 1px solid var(--border-strong);
        }

        .button-link--ghost:hover {
            background: var(--blue-100);
        }

        .stats-grid,
        .grid {
            display: grid;
            gap: 1rem;
        }

        .stats-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 1rem;
        }

        .stat-card,
        .panel {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(33, 70, 156, 0.1);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .stat-card {
            padding: 1rem 1.1rem;
        }

        .stat-card__label {
            display: block;
            margin-bottom: 0.45rem;
            color: var(--ink-600);
            font-size: 0.86rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .stat-card__value {
            font-size: clamp(1.4rem, 1.15rem + 0.6vw, 2rem);
            font-weight: 800;
            color: var(--ink-950);
        }

        .stat-card__hint {
            margin-top: 0.35rem;
            color: var(--ink-600);
            font-size: 0.9rem;
        }

        .grid {
            grid-template-columns: minmax(0, 1.55fr) minmax(340px, 0.95fr);
            align-items: start;
        }

        .panel {
            overflow: hidden;
        }

        .panel__header,
        .panel__body {
            padding: 1.2rem 1.25rem;
        }

        .panel__header {
            border-bottom: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(248, 250, 255, 0.95), rgba(255, 255, 255, 0.96));
        }

        .panel__header--split,
        .table-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .panel__header--stacked {
            display: grid;
            gap: 1rem;
        }

        .panel__title-block {
            display: grid;
            gap: 0.7rem;
        }

        .panel__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            width: fit-content;
            padding: 0.4rem 0.72rem;
            border-radius: 999px;
            background: var(--blue-100);
            color: var(--blue-800);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .panel__header h2 {
            margin: 0;
            font-size: 1.15rem;
            line-height: 1.2;
        }

        .panel__subtitle,
        .table-meta,
        .filters__hint {
            color: var(--ink-600);
            font-size: 0.94rem;
        }

        .panel__subtitle {
            margin: 0;
            max-width: 62ch;
            line-height: 1.55;
        }

        .alert {
            margin-bottom: 1rem;
            padding: 0.95rem 1rem;
            border-radius: 14px;
            font-weight: 700;
            border: 1px solid transparent;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .alert-success {
            color: #0f7a2e;
            background: var(--green-100);
            border-color: rgba(16, 167, 53, 0.16);
        }

        .alert-error {
            color: #a01f25;
            background: var(--red-100);
            border-color: rgba(207, 31, 40, 0.12);
        }

        .filters {
            display: grid;
            grid-template-columns: minmax(240px, 1.45fr) repeat(2, minmax(170px, 0.8fr)) auto;
            gap: 0.85rem;
            align-items: end;
            margin-bottom: 1rem;
        }

        .field {
            display: block;
        }

        .field__label {
            display: block;
            margin-bottom: 0.45rem;
            color: var(--ink-700);
            font-size: 0.92rem;
            font-weight: 700;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 0.82rem 0.95rem;
            border: 1px solid var(--border-strong);
            border-radius: 12px;
            background: #fff;
            color: var(--ink-950);
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--blue-700);
            box-shadow: 0 0 0 4px rgba(39, 77, 178, 0.12);
        }

        textarea {
            min-height: 200px;
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
            margin: 0.25rem 0 1.25rem;
            padding: 0.8rem 0.9rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface-muted);
        }

        .checkbox-row input {
            width: auto;
            margin: 0;
        }

        .checkbox-row span {
            margin: 0;
            font-weight: 700;
        }

        .field-error {
            display: block;
            margin-top: 0.35rem;
            color: #a01f25;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .table-meta {
            margin-bottom: 1rem;
        }

        .table-meta__count {
            font-weight: 700;
        }

        .pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            background: var(--surface-muted);
            color: var(--ink-700);
            font-size: 0.88rem;
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
        }

        table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.95rem 0.85rem;
            border-bottom: 1px solid #edf1f6;
            text-align: left;
            vertical-align: top;
        }

        tbody tr:hover {
            background: rgba(33, 70, 156, 0.03);
        }

        th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f8fbff;
            color: var(--ink-700);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .col-id {
            width: 72px;
        }

        .col-title {
            width: 240px;
        }

        .col-order,
        .col-status {
            width: 104px;
        }

        .col-created {
            width: 150px;
        }

        .title-cell__title {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 700;
            color: var(--ink-950);
        }

        .title-cell__meta {
            color: var(--ink-600);
            font-size: 0.86rem;
        }

        .title-cell__row {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
            margin-bottom: 0.25rem;
        }

        .content-preview {
            display: inline-block;
            max-width: 32ch;
            color: var(--ink-700);
            line-height: 1.55;
        }

        .mini-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.22rem 0.55rem;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .mini-badge--published {
            color: #0f7a2e;
            background: var(--green-100);
        }

        .mini-badge--draft {
            color: #9a6700;
            background: var(--amber-100);
        }

        .status,
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            padding: 0.38rem 0.68rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .status--active {
            color: #0f7a2e;
            background: var(--green-100);
        }

        .status--inactive {
            color: #a01f25;
            background: var(--red-100);
        }

        .badge--warning {
            color: #a01f25;
            background: var(--red-100);
        }

        .badge--recommendation {
            color: #0f7a2e;
            background: var(--green-100);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
        }

        .actions a {
            padding: 0.56rem 0.8rem;
            border-radius: 10px;
            font-size: 0.87rem;
            font-weight: 800;
            color: #fff;
            background: var(--blue-800);
        }

        .actions .delete-link {
            background: var(--red-600);
        }

        .empty-state {
            padding: 1.35rem;
            border: 1px dashed var(--border-strong);
            border-radius: 16px;
            text-align: center;
            color: var(--ink-700);
            background: var(--surface-muted);
        }

        .empty-state h3 {
            margin: 0 0 0.45rem;
            color: var(--ink-950);
            font-size: 1rem;
        }

        .pagination {
            margin-top: 1rem;
            justify-content: space-between;
            align-items: center;
        }

        .pagination__pages {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
        }

        .pagination a,
        .pagination span {
            min-width: 42px;
            padding-inline: 0.8rem;
            text-decoration: none;
        }

        .pagination__current {
            background: var(--blue-900);
            color: #fff;
        }

        .pagination__muted {
            background: var(--surface-muted);
            color: var(--ink-600);
            border: 1px solid var(--border);
        }

        .form-panel {
            position: sticky;
            top: 1rem;
        }

        .helper-card {
            margin-top: 1rem;
            padding: 1rem 1.05rem;
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(234, 241, 255, 0.8), rgba(248, 250, 255, 0.95));
            border: 1px solid rgba(33, 70, 156, 0.12);
            color: var(--ink-700);
        }

        .helper-card h3 {
            margin: 0 0 0.35rem;
            color: var(--ink-950);
            font-size: 0.98rem;
        }

        .helper-card ul {
            margin: 0.65rem 0 0;
            padding-left: 1.1rem;
        }

        .helper-card li + li {
            margin-top: 0.35rem;
        }

        .edit-notice {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.55rem;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            background: var(--blue-100);
            color: var(--blue-800);
            font-size: 0.82rem;
            font-weight: 800;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 40;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: rgba(15, 23, 42, 0.56);
            backdrop-filter: blur(3px);
            animation: modalFadeIn 0.2s ease-out;
        }

        .modal {
            width: min(880px, 100%);
            max-height: calc(100vh - 2.5rem);
            overflow: auto;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.26);
            animation: modalScaleIn 0.22s ease-out;
        }

        .modal__header,
        .modal__body {
            padding: 1.2rem 1.25rem;
        }

        .modal__header {
            position: sticky;
            top: 0;
            z-index: 1;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(248, 250, 255, 0.98), rgba(255, 255, 255, 0.98));
        }

        .modal__header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .modal__close {
            min-width: 42px;
            min-height: 42px;
            padding: 0;
            border-radius: 999px;
            background: var(--surface-muted);
            color: var(--ink-700);
            border: 1px solid var(--border);
            font-size: 1.2rem;
            line-height: 1;
        }

        .modal__close:hover {
            background: var(--blue-100);
            color: var(--blue-800);
        }

        .modal__grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(260px, 0.8fr);
            gap: 1rem;
            align-items: start;
        }

        .preview-panel {
            position: sticky;
            top: 0;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(248, 250, 255, 0.95), rgba(255, 255, 255, 0.98));
        }

        .preview-panel h3 {
            margin: 0 0 0.35rem;
            font-size: 1rem;
            color: var(--ink-950);
        }

        .preview-panel p {
            margin: 0 0 0.85rem;
            color: var(--ink-600);
            font-size: 0.9rem;
            line-height: 1.55;
        }

        .preview-card {
            padding: 1rem;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(33, 70, 156, 0.1);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .preview-card__meta {
            display: flex;
            gap: 0.45rem;
            flex-wrap: wrap;
            margin-bottom: 0.8rem;
        }

        .preview-badge,
        .preview-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .preview-badge--warning {
            color: #a01f25;
            background: var(--red-100);
        }

        .preview-badge--recommendation {
            color: #0f7a2e;
            background: var(--green-100);
        }

        .preview-status--active {
            color: #0f7a2e;
            background: var(--green-100);
        }

        .preview-status--draft {
            color: #9a6700;
            background: var(--amber-100);
        }

        .preview-card__title {
            margin: 0 0 0.55rem;
            font-size: 1.05rem;
            line-height: 1.35;
            color: var(--ink-950);
        }

        .preview-card__body {
            margin: 0;
            color: var(--ink-700);
            line-height: 1.7;
            word-break: break-word;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes modalScaleIn {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.985);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 1120px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .form-panel {
                position: static;
            }

            .modal__grid {
                grid-template-columns: 1fr;
            }

            .preview-panel {
                position: static;
            }
        }

        @media (max-width: 860px) {
            .filters {
                grid-template-columns: 1fr;
            }

            .pagination {
                align-items: flex-start;
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

            .stats-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .panel__header,
            .panel__body {
                padding-inline: 1rem;
            }

            .modal-backdrop {
                padding: 0.75rem;
            }

            .modal__header,
            .modal__body {
                padding-inline: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <h1>Admin Avisos</h1>
                <p>Gestiona avisos publicos para <code>/avisos</code> con busqueda, filtros y paginacion.</p>
            </div>
            <div class="topbar__actions">
                <a class="button-link button-link--secondary" href="<?= avisos_e($listReturnPath) ?>">Nuevo aviso</a>
                <a class="button-link" href="<?= avisos_e(avisos_base_path() . '/avisos.php') ?>" target="_blank" rel="noopener">Ver pagina publica</a>
                <a class="button-link button-link--danger" href="<?= avisos_e(avisos_admin_url('logout.php')) ?>">Salir</a>
            </div>
        </header>

        <section class="stats-grid" aria-label="Resumen de avisos">
            <article class="stat-card">
                <span class="stat-card__label">Total</span>
                <div class="stat-card__value"><?= $stats['total'] ?></div>
                <div class="stat-card__hint">Todos los avisos registrados</div>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Activos</span>
                <div class="stat-card__value"><?= $stats['active'] ?></div>
                <div class="stat-card__hint">Visibles en la pagina publica</div>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Advertencias</span>
                <div class="stat-card__value"><?= $stats['warnings'] ?></div>
                <div class="stat-card__hint">Mensajes de riesgo o alerta</div>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Recomendaciones</span>
                <div class="stat-card__value"><?= $stats['recommendations'] ?></div>
                <div class="stat-card__hint">Mensajes preventivos y guias</div>
            </article>
        </section>

        <?php if (is_array($flash) && isset($flash['message'], $flash['type'])): ?>
            <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= avisos_e((string) $flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <section class="panel">
                <div class="panel__header panel__header--stacked">
                    <div class="panel__title-block">
                        <div class="panel__eyebrow">Listado</div>
                        <div>
                            <h2>Explorar avisos</h2>
                            <p class="panel__subtitle">Filtra por texto, tipo o estado. Los avisos con estado <strong>Activo</strong> ya están publicados y pueden editarse desde esta misma tabla.</p>
                        </div>
                    </div>
                    <div class="panel__header--split">
                        <div class="pill-row">
                            <span class="pill">Pagina <?= $currentPage ?> de <?= $totalPages ?></span>
                            <span class="pill">Mostrando <?= $rangeStart ?>-<?= $rangeEnd ?> de <?= $totalFiltered ?></span>
                        </div>
                    </div>
                </div>
                <div class="panel__body">
                    <form method="get" action="<?= avisos_e(avisos_admin_url('dashboard.php')) ?>" class="filters">
                        <label class="field">
                            <span class="field__label">Buscar</span>
                            <input type="text" name="q" value="<?= avisos_e($searchQuery) ?>" placeholder="Titulo, contenido o ID">
                        </label>

                        <label class="field">
                            <span class="field__label">Tipo</span>
                            <select name="tipo">
                                <option value="all">Todos</option>
                                <?php foreach (avisos_allowed_types() as $tipo): ?>
                                    <option value="<?= avisos_e($tipo) ?>" <?= $tipoFilter === $tipo ? 'selected' : '' ?>>
                                        <?= avisos_e(ucfirst($tipo)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="field">
                            <span class="field__label">Estado</span>
                            <select name="estado">
                                <option value="all" <?= $estadoFilter === 'all' ? 'selected' : '' ?>>Todos</option>
                                <option value="active" <?= $estadoFilter === 'active' ? 'selected' : '' ?>>Activos</option>
                                <option value="inactive" <?= $estadoFilter === 'inactive' ? 'selected' : '' ?>>Inactivos</option>
                            </select>
                        </label>

                        <div class="filters__actions">
                            <button class="button" type="submit">Aplicar</button>
                            <?php if ($filtersActive): ?>
                                <a class="button-link button-link--ghost" href="<?= avisos_e(avisos_admin_url('dashboard.php')) ?>">Limpiar</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-meta">
                        <div class="table-meta__count">
                            <?= $filtersActive ? 'Resultados filtrados' : 'Todos los avisos' ?>
                        </div>
                        <div class="filters__hint">
                            <?= $filtersActive ? 'Los filtros afectan paginacion y acciones del listado.' : 'Usa filtros si necesitas ubicar avisos rapidamente.' ?>
                        </div>
                    </div>

                    <?php if ($pageAvisos === []): ?>
                        <div class="empty-state">
                            <h3>No hay avisos para esta vista</h3>
                            <p><?= $filtersActive ? 'Prueba ajustando o limpiando los filtros actuales.' : 'Todavia no hay avisos registrados.' ?></p>
                            <?php if ($filtersActive): ?>
                                <div class="form-actions" style="justify-content:center; margin-top:1rem;">
                                    <a class="button-link button-link--ghost" href="<?= avisos_e(avisos_admin_url('dashboard.php')) ?>">Quitar filtros</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="col-id">ID</th>
                                        <th class="col-title">Titulo</th>
                                        <th>Tipo</th>
                                        <th>Contenido</th>
                                        <th class="col-order">Orden</th>
                                        <th class="col-status">Estado</th>
                                        <th class="col-created">Creado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pageAvisos as $aviso): ?>
                                        <?php
                                        $editPath = avisos_dashboard_path([
                                            'q' => $searchQuery,
                                            'tipo' => $tipoFilter,
                                            'estado' => $estadoFilter,
                                            'page' => $currentPage,
                                            'edit' => (int) $aviso['id'],
                                        ]);
                                        $deletePath = 'delete.php?' . http_build_query([
                                            'id' => (int) $aviso['id'],
                                            'token' => avisos_csrf_token(),
                                            'return' => $listReturnPath,
                                        ]);
                                        ?>
                                        <tr>
                                            <td>#<?= (int) $aviso['id'] ?></td>
                                            <td>
                                                <span class="title-cell__row">
                                                    <span class="title-cell__title"><?= avisos_e((string) $aviso['titulo']) ?></span>
                                                    <span class="mini-badge <?= (int) $aviso['activo'] === 1 ? 'mini-badge--published' : 'mini-badge--draft' ?>">
                                                        <?= (int) $aviso['activo'] === 1 ? 'Publicado' : 'Borrador' ?>
                                                    </span>
                                                </span>
                                                <span class="title-cell__meta">Prioridad <?= (int) $aviso['orden'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $aviso['tipo'] === 'advertencia' ? 'badge--warning' : 'badge--recommendation' ?>">
                                                    <?= avisos_e((string) $aviso['tipo']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="content-preview"><?= avisos_e(avisos_preview((string) $aviso['contenido'])) ?></span>
                                            </td>
                                            <td><?= (int) $aviso['orden'] ?></td>
                                            <td>
                                                <span class="status <?= (int) $aviso['activo'] === 1 ? 'status--active' : 'status--inactive' ?>">
                                                    <?= (int) $aviso['activo'] === 1 ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td><?= avisos_e(avisos_format_datetime((string) $aviso['created_at'])) ?></td>
                                            <td>
                                                <div class="actions">
                                                    <a href="<?= avisos_e($editPath) ?>"><?= (int) $aviso['activo'] === 1 ? 'Editar publicado' : 'Editar' ?></a>
                                                    <a class="delete-link" href="<?= avisos_e($deletePath) ?>" onclick="return confirm('¿Eliminar este aviso?');">Eliminar</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPages > 1): ?>
                            <nav class="pagination" aria-label="Paginacion de avisos">
                                <div class="pagination__pages">
                                    <?php if ($currentPage > 1): ?>
                                        <a href="<?= avisos_e(avisos_dashboard_path(['q' => $searchQuery, 'tipo' => $tipoFilter, 'estado' => $estadoFilter, 'page' => $currentPage - 1])) ?>">Anterior</a>
                                    <?php else: ?>
                                        <span class="pagination__muted">Anterior</span>
                                    <?php endif; ?>

                                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                        <?php if ($page === $currentPage): ?>
                                            <span class="pagination__current"><?= $page ?></span>
                                        <?php elseif ($page === 1 || $page === $totalPages || abs($page - $currentPage) <= 1): ?>
                                            <a href="<?= avisos_e(avisos_dashboard_path(['q' => $searchQuery, 'tipo' => $tipoFilter, 'estado' => $estadoFilter, 'page' => $page])) ?>"><?= $page ?></a>
                                        <?php elseif ($page === 2 && $currentPage > 4): ?>
                                            <span class="pagination__muted">…</span>
                                        <?php elseif ($page === $totalPages - 1 && $currentPage < $totalPages - 3): ?>
                                            <span class="pagination__muted">…</span>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <?php if ($currentPage < $totalPages): ?>
                                        <a href="<?= avisos_e(avisos_dashboard_path(['q' => $searchQuery, 'tipo' => $tipoFilter, 'estado' => $estadoFilter, 'page' => $currentPage + 1])) ?>">Siguiente</a>
                                    <?php else: ?>
                                        <span class="pagination__muted">Siguiente</span>
                                    <?php endif; ?>
                                </div>
                                <div class="filters__hint">Mostrando <?= $rangeStart ?>-<?= $rangeEnd ?> de <?= $totalFiltered ?> avisos</div>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="form-panel">
                <section class="panel">
                    <div class="panel__header">
                        <div class="panel__eyebrow">Nuevo registro</div>
                        <div class="panel__header--split">
                            <div>
                                <h2>Agregar nuevo aviso</h2>
                                <p class="panel__subtitle">Crea un aviso nuevo. Para modificar uno existente, usa el botón <strong>Editar</strong> en la tabla.</p>
                            </div>
                            <a class="button-link button-link--ghost" href="<?= avisos_e($listReturnPath) ?>">Limpiar</a>
                        </div>
                    </div>
                    <div class="panel__body">
                        <form method="post" action="<?= avisos_e(avisos_admin_url('save.php')) ?>">
                            <input type="hidden" name="csrf_token" value="<?= avisos_e(avisos_csrf_token()) ?>">
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="redirect_to" value="<?= avisos_e($listReturnPath) ?>">

                            <label class="field">
                                <span class="field__label">Titulo</span>
                                <input type="text" name="titulo" maxlength="255" value="<?= avisos_e((string) $createFormValues['titulo']) ?>" placeholder="Ej. Pago fuera de horario" required>
                                <?php if (isset($createFormErrors['titulo'])): ?>
                                    <small class="field-error"><?= avisos_e((string) $createFormErrors['titulo']) ?></small>
                                <?php endif; ?>
                            </label>

                            <div class="form-grid">
                                <label class="field">
                                    <span class="field__label">Tipo</span>
                                    <select name="tipo" required>
                                        <?php foreach (avisos_allowed_types() as $tipo): ?>
                                            <option value="<?= avisos_e($tipo) ?>" <?= $createFormValues['tipo'] === $tipo ? 'selected' : '' ?>>
                                                <?= avisos_e(ucfirst($tipo)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($createFormErrors['tipo'])): ?>
                                        <small class="field-error"><?= avisos_e((string) $createFormErrors['tipo']) ?></small>
                                    <?php endif; ?>
                                </label>

                                <label class="field">
                                    <span class="field__label">Orden</span>
                                    <input type="number" name="orden" step="1" value="<?= avisos_e((string) $createFormValues['orden']) ?>" placeholder="0">
                                </label>
                            </div>

                            <label class="field">
                                <span class="field__label">Contenido</span>
                                <textarea name="contenido" placeholder="Escribe el mensaje que vera el usuario final." required><?= avisos_e((string) $createFormValues['contenido']) ?></textarea>
                                <?php if (isset($createFormErrors['contenido'])): ?>
                                    <small class="field-error"><?= avisos_e((string) $createFormErrors['contenido']) ?></small>
                                <?php endif; ?>
                            </label>

                            <label class="checkbox-row">
                                <input type="checkbox" name="activo" value="1" <?= (int) $createFormValues['activo'] === 1 ? 'checked' : '' ?>>
                                <span>Publicar este aviso</span>
                            </label>

                            <div class="form-actions">
                                <button class="button" type="submit">Agregar aviso</button>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="helper-card" aria-label="Ayuda rapida">
                    <h3>Buenas practicas</h3>
                    <ul>
                        <li>Usa <strong>orden</strong> bajo para fijar avisos importantes arriba.</li>
                        <li>Desactiva un aviso si debe conservarse sin mostrarse al publico.</li>
                        <li>Las acciones del listado conservan busqueda, filtros y pagina actual.</li>
                    </ul>
                </section>
            </aside>
        </div>

        <?php if ($isEditing): ?>
            <div class="modal-backdrop" id="editor" onclick="if (event.target === this) { window.location.href = '<?= avisos_e($listReturnPath) ?>'; }">
                <section class="modal" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
                    <div class="modal__header">
                        <div class="modal__header-row">
                            <div>
                                <div class="panel__eyebrow">Edicion</div>
                                <h2 id="edit-modal-title">Editar aviso</h2>
                                <p class="panel__subtitle">Actualiza el contenido de un aviso ya registrado y define si debe seguir publicado.</p>
                                <div class="edit-notice">Editando aviso <?= avisos_e($editingNoticeLabel) ?></div>
                            </div>
                            <a class="button-link button-link--ghost modal__close" href="<?= avisos_e($listReturnPath) ?>" aria-label="Cerrar editor">×</a>
                        </div>
                    </div>
                    <div class="modal__body">
                        <div class="modal__grid">
                            <form method="post" action="<?= avisos_e(avisos_admin_url('save.php')) ?>">
                                <input type="hidden" name="csrf_token" value="<?= avisos_e(avisos_csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= avisos_e((string) $editFormValues['id']) ?>">
                                <input type="hidden" name="redirect_to" value="<?= avisos_e($listReturnPath) ?>">

                                <label class="field">
                                    <span class="field__label">Titulo</span>
                                    <input type="text" name="titulo" maxlength="255" value="<?= avisos_e((string) $editFormValues['titulo']) ?>" placeholder="Ej. Pago fuera de horario" required autofocus>
                                    <?php if (isset($editFormErrors['titulo'])): ?>
                                        <small class="field-error"><?= avisos_e((string) $editFormErrors['titulo']) ?></small>
                                    <?php endif; ?>
                                </label>

                                <div class="form-grid">
                                    <label class="field">
                                        <span class="field__label">Tipo</span>
                                        <select name="tipo" required>
                                            <?php foreach (avisos_allowed_types() as $tipo): ?>
                                                <option value="<?= avisos_e($tipo) ?>" <?= $editFormValues['tipo'] === $tipo ? 'selected' : '' ?>>
                                                    <?= avisos_e(ucfirst($tipo)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset($editFormErrors['tipo'])): ?>
                                            <small class="field-error"><?= avisos_e((string) $editFormErrors['tipo']) ?></small>
                                        <?php endif; ?>
                                    </label>

                                    <label class="field">
                                        <span class="field__label">Orden</span>
                                        <input type="number" name="orden" step="1" value="<?= avisos_e((string) $editFormValues['orden']) ?>" placeholder="0">
                                    </label>
                                </div>

                                <label class="field">
                                    <span class="field__label">Contenido</span>
                                    <textarea name="contenido" placeholder="Escribe el mensaje que vera el usuario final." required><?= avisos_e((string) $editFormValues['contenido']) ?></textarea>
                                    <?php if (isset($editFormErrors['contenido'])): ?>
                                        <small class="field-error"><?= avisos_e((string) $editFormErrors['contenido']) ?></small>
                                    <?php endif; ?>
                                </label>

                                <label class="checkbox-row">
                                    <input type="checkbox" name="activo" value="1" <?= (int) $editFormValues['activo'] === 1 ? 'checked' : '' ?>>
                                    <span>Publicar este aviso</span>
                                </label>

                                <div class="form-actions">
                                    <button class="button" type="submit">Guardar cambios</button>
                                    <a class="button-link button-link--ghost" href="<?= avisos_e($listReturnPath) ?>">Cancelar</a>
                                </div>
                            </form>

                            <aside class="preview-panel" aria-label="Vista previa del aviso">
                                <h3>Vista previa</h3>
                                <p>Así se verá el aviso con los datos cargados actualmente antes de guardar los cambios.</p>
                                <?= avisos_render_preview_html($editFormValues) ?>
                            </aside>
                        </div>
                    </div>
                </section>
            </div>

            <script>
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        window.location.href = '<?= avisos_e($listReturnPath) ?>';
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
