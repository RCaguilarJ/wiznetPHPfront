<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/db.php';

function site_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../data/site.php';
    }

    return $config;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function asset_url(string $path): string
{
    return site_base_path() . '/assets/' . ltrim($path, '/');
}

function page_url(string $path): string
{
    $normalizedPath = ltrim($path, '/');

    if ($normalizedPath === 'index.php' || $normalizedPath === 'index') {
        return site_base_path() . '/';
    }

    if (str_starts_with($normalizedPath, 'index.php#')) {
        return site_base_path() . '/' . ltrim(substr($normalizedPath, strlen('index.php')), '/');
    }

    return site_base_path() . '/' . $normalizedPath;
}

function site_base_path(): string
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $projectRoot = realpath(dirname(__DIR__));
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string) $_SERVER['DOCUMENT_ROOT']) : false;

    if ($projectRoot && $documentRoot) {
        $normalizedProjectRoot = str_replace('\\', '/', $projectRoot);
        $normalizedDocumentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');

        if (substr($normalizedProjectRoot, 0, strlen($normalizedDocumentRoot)) === $normalizedDocumentRoot) {
            $relativePath = substr($normalizedProjectRoot, strlen($normalizedDocumentRoot));
            $relativePath = $relativePath === false ? '' : $relativePath;
            $basePath = $relativePath !== '' ? rtrim(str_replace('\\', '/', $relativePath), '/') : '';

            return $basePath;
        }
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $fallback = dirname($scriptName);
    $basePath = $fallback === '/' || $fallback === '.' ? '' : rtrim($fallback, '/');

    return $basePath;
}

function current_request_path(): string
{
    $requestUri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $basePath = site_base_path();

    if ($basePath !== '' && substr($requestUri, 0, strlen($basePath)) === $basePath) {
        $requestUri = substr($requestUri, strlen($basePath)) ?: '/';
    }

    return trim($requestUri, '/');
}

function current_page_name(): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
}

function is_active_page(string $key): bool
{
    $map = [
        'home' => ['index.php', ''],
        'packages' => ['internet-residencial.php', 'internet-comercial.php'],
        'coverage' => 'cobertura.php',
        'support' => 'soporte.php',
        'payments' => ['registro-pagos.php', 'pago-web'],
        'contact' => 'contacto.php',
    ];

    $target = $map[$key] ?? '';
    $currentRequestPath = current_request_path();

    if (is_array($target)) {
        return in_array(current_page_name(), $target, true) || in_array($currentRequestPath, $target, true);
    }

    return $target === current_page_name() || $target === $currentRequestPath;
}

function page_title(array $context): string
{
    $brand = site_config()['brand']['name'];
    return isset($context['page_title']) ? $context['page_title'] . ' | ' . $brand : $brand;
}

function env_value(string $key, string $default = ''): string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null) {
        return $default;
    }

    return (string) $value;
}

function load_mail_config(): array
{
    $config = [
        'host' => env_value('MAIL_HOST'),
        'port' => (int) env_value('MAIL_PORT', '465'),
        'encryption' => strtolower(env_value('MAIL_ENCRYPTION', 'ssl')),
        'user' => env_value('MAIL_USER'),
        'pass' => env_value('MAIL_PASS'),
        'from' => env_value('MAIL_FROM'),
        'from_name' => env_value('MAIL_FROM_NAME', 'WIZNET'),
    ];

    foreach (['host', 'user', 'pass', 'from', 'from_name'] as $key) {
        if ($config[$key] === '' || str_starts_with($config[$key], 'COMPLETAR_')) {
            return ['error' => 'La configuracion de correo no esta disponible.'];
        }
    }

    if ($config['port'] <= 0) {
        return ['error' => 'La configuracion de correo no esta disponible.'];
    }

    return ['config' => $config];
}

function cleanup_temporary_file(?string $path): void
{
    if (!is_string($path) || $path === '' || !is_file($path)) {
        return;
    }

    @unlink($path);
}

function form_result(): array
{
    return [
        'success' => false,
        'message' => '',
        'errors' => [],
        'old' => [],
    ];
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function is_post(): bool
{
    return request_method() === 'POST';
}

function old_value(array $result, string $field, string $fallback = ''): string
{
    return (string) ($result['old'][$field] ?? $fallback);
}

function field_error(array $result, string $field): string
{
    return (string) ($result['errors'][$field] ?? '');
}

function has_error(array $result, string $field): bool
{
    return field_error($result, $field) !== '';
}

function sanitize_string(string $value): string
{
    $value = trim($value);
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
    return $value;
}

function sanitize_multiline(string $value): string
{
    $value = trim($value);
    $value = preg_replace("/\r\n|\r/u", "\n", $value) ?? $value;
    return $value;
}

function sanitize_phone(string $value): string
{
    $value = preg_replace('/[^\d+\s()-]/', '', trim($value)) ?? '';
    return preg_replace('/\s+/u', ' ', $value) ?? $value;
}

function normalize_digits(string $value): string
{
    return preg_replace('/\D/', '', $value) ?? '';
}

function validate_required(array &$errors, string $field, string $value, string $label): void
{
    if ($value === '') {
        $errors[$field] = sprintf('El campo %s es obligatorio.', $label);
    }
}

function validate_email(array &$errors, string $field, string $value): void
{
    if ($value === '' || filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return;
    }

    $errors[$field] = 'Ingresa un correo electrónico válido.';
}

function validate_phone(array &$errors, string $field, string $value): void
{
    if ($value === '') {
        return;
    }

    if (strlen(normalize_digits($value)) < 10) {
        $errors[$field] = 'Ingresa un número telefónico válido.';
    }
}

function validate_office(array &$errors, string $field, string $value, array $options): void
{
    if ($value === '') {
        return;
    }

    if (!in_array($value, $options, true)) {
        $errors[$field] = 'Selecciona una oficina válida.';
    }
}

function uploaded_file_present(string $field): bool
{
    return isset($_FILES[$field]) && (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
}

function handle_upload(string $field, string $targetGroup, bool $required = false): array
{
    $file = $_FILES[$field] ?? null;

    if ($file === null || (int) $file['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            return ['error' => 'Debes adjuntar un archivo.'];
        }

        return ['path' => null, 'name' => null];
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'No fue posible subir el archivo.'];
    }

    $maxBytes = 20 * 1024 * 1024;
    if ((int) $file['size'] > $maxBytes) {
        return ['error' => 'El archivo supera el tamaño máximo permitido de 20MB.'];
    }

    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
    if (!in_array($extension, $allowedExtensions, true)) {
        return ['error' => 'Solo se permiten archivos JPG, PNG, WEBP o PDF.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, (string) $file['tmp_name']) : null;
    if ($finfo) {
        finfo_close($finfo);
    }

    $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
    if ($mime !== null && !in_array($mime, $allowedMime, true)) {
        return ['error' => 'El archivo adjunto no tiene un formato válido.'];
    }

    $targetDir = __DIR__ . '/../storage/uploads/' . $targetGroup;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
        return ['error' => 'No fue posible preparar el almacenamiento del archivo.'];
    }

    $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '-', pathinfo((string) $file['name'], PATHINFO_FILENAME)) ?: 'archivo';
    $finalName = sprintf('%s-%s.%s', date('Ymd-His'), bin2hex(random_bytes(4)), $extension);
    $finalPath = $targetDir . '/' . $finalName;

    if (!move_uploaded_file((string) $file['tmp_name'], $finalPath)) {
        return ['error' => 'No fue posible guardar el archivo en el servidor.'];
    }

    return [
        'path' => 'storage/uploads/' . $targetGroup . '/' . $finalName,
        'name' => $safeBaseName . '.' . $extension,
    ];
}

function store_submission(string $type, array $payload): bool
{
    $targetDir = __DIR__ . '/../storage/submissions';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
        return false;
    }

    $record = [
        'id' => bin2hex(random_bytes(8)),
        'type' => $type,
        'created_at' => date('c'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'payload' => $payload,
    ];

    $targetFile = $targetDir . '/' . $type . '.jsonl';
    return file_put_contents($targetFile, json_encode($record, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}

function load_payment_db_config(): array
{
    $configFile = __DIR__ . '/../config/db.php';
    if (!is_file($configFile)) {
        return ['error' => 'No se encontro la configuracion de base de datos para Registro de Pagos.'];
    }

    require_once $configFile;

    $required = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'];
    foreach ($required as $constantName) {
        if (!defined($constantName)) {
            return ['error' => sprintf('Falta definir la constante %s en config/db.php.', $constantName)];
        }
    }

    $config = [
        'host' => (string) DB_HOST,
        'user' => (string) DB_USER,
        'pass' => (string) DB_PASS,
        'name' => (string) DB_NAME,
        'port' => defined('DB_PORT') ? (int) DB_PORT : 3306,
        'charset' => defined('DB_CHARSET') ? (string) DB_CHARSET : 'utf8mb4',
    ];

    foreach (['host', 'user', 'pass', 'name'] as $key) {
        if (strpos($config[$key], 'COMPLETAR_DESPUES_DE_CREAR_BD_EN_CPANEL') !== false) {
            return ['error' => 'La configuracion de base de datos aun tiene valores placeholder.'];
        }
    }

    return ['config' => $config];
}

function handle_payment_receipt_upload(string $field): array
{
    $file = $_FILES[$field] ?? null;

    if ($file === null || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['error' => 'Debes adjuntar un archivo.'];
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'No fue posible subir el archivo.'];
    }

    $maxBytes = 20 * 1024 * 1024;
    if ((int) $file['size'] > $maxBytes) {
        return ['error' => 'El archivo supera el tamano maximo permitido de 20MB.'];
    }

    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($extension, $allowedExtensions, true)) {
        return ['error' => 'Solo se permiten archivos PDF, JPG o PNG.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, (string) $file['tmp_name']) : null;
    if ($finfo) {
        finfo_close($finfo);
    }

    $allowedMime = ['image/jpeg', 'image/png', 'application/pdf'];
    if ($mime !== null && !in_array($mime, $allowedMime, true)) {
        return ['error' => 'El archivo adjunto no tiene un formato valido.'];
    }

    $targetDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'wiznet-comprobantes';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
        return ['error' => 'No fue posible preparar la carpeta temporal de comprobantes.'];
    }

    $originalName = (string) $file['name'];
    $safeOriginalName = preg_replace('/[^a-zA-Z0-9._-]/', '-', basename($originalName)) ?: 'comprobante.' . $extension;
    $finalName = sprintf('%s-%s', date('YmdHis'), $safeOriginalName);
    $finalPath = $targetDir . '/' . $finalName;

    if (!move_uploaded_file((string) $file['tmp_name'], $finalPath)) {
        return ['error' => 'No fue posible guardar el archivo en el servidor.'];
    }

    return [
        'name' => $safeOriginalName,
        'absolute_path' => $finalPath,
        'temporary' => true,
    ];
}

function insert_payment_record(array $paymentData): array
{
    $dbConfigResult = load_payment_db_config();
    if (isset($dbConfigResult['error'])) {
        return ['success' => false, 'error' => $dbConfigResult['error']];
    }

    $connectionResult = wiznet_db_connect($dbConfigResult['config']);
    if ($connectionResult['error'] !== null) {
        return ['success' => false, 'error' => $connectionResult['error']];
    }

    /** @var mysqli $connection */
    $connection = $connectionResult['connection'];

    $sql = 'INSERT INTO registro_pagos 
        (nombre, oficina, numero_cliente, correo, telefono, comentarios, archivo_nombre, archivo_ruta)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

    $statement = $connection->prepare($sql);
    if ($statement === false) {
        $error = $connection->error ?: 'No fue posible preparar el INSERT.';
        $connection->close();
        return ['success' => false, 'error' => $error];
    }

    $statement->bind_param(
        'ssssssss',
        $paymentData['name'],
        $paymentData['office'],
        $paymentData['client_number'],
        $paymentData['email'],
        $paymentData['phone'],
        $paymentData['comments'],
        $paymentData['attachment_name'],
        $paymentData['attachment_path']
    );

    $saved = $statement->execute();
    $error = $saved ? null : ($statement->error ?: 'No fue posible guardar el registro en la base de datos.');

    $statement->close();
    $connection->close();

    return ['success' => $saved, 'error' => $error];
}

function send_payment_notification_email(array $paymentData): array
{
    if (!class_exists(PHPMailer::class)) {
        error_log('PHPMailer no esta disponible en el proyecto.');
        return ['success' => false, 'error' => 'No fue posible enviar el correo de notificacion.'];
    }

    $mailConfigResult = load_mail_config();
    if (isset($mailConfigResult['error'])) {
        error_log($mailConfigResult['error']);
        return ['success' => false, 'error' => 'No fue posible enviar el correo de notificacion.'];
    }

    $mailConfig = $mailConfigResult['config'];
    $subject = sprintf(
        'Nuevo Registro de Pago - %s - Cliente: %s',
        $paymentData['name'],
        $paymentData['client_number']
    );
    $comments = $paymentData['comments'] !== '' ? $paymentData['comments'] : '(sin comentarios)';

    $htmlBody = sprintf(
        '<html><body><p><strong>Nombre:</strong> %s</p><p><strong>Oficina:</strong> %s</p><p><strong>Número de Cliente:</strong> %s</p><p><strong>Correo:</strong> %s</p><p><strong>Teléfono:</strong> %s</p><p><strong>Comentarios:</strong><br>%s</p></body></html>',
        e($paymentData['name']),
        e($paymentData['office']),
        e($paymentData['client_number']),
        e($paymentData['email']),
        e($paymentData['phone']),
        nl2br(e($comments))
    );

    $textBody = implode("\n", [
        'Nombre: ' . $paymentData['name'],
        'Oficina: ' . $paymentData['office'],
        'Número de Cliente: ' . $paymentData['client_number'],
        'Correo: ' . $paymentData['email'],
        'Teléfono: ' . $paymentData['phone'],
        'Comentarios: ' . $comments,
    ]);

    try {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $mailConfig['host'];
        $mailer->Port = $mailConfig['port'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $mailConfig['user'];
        $mailer->Password = $mailConfig['pass'];
        $mailer->CharSet = 'UTF-8';

        if ($mailConfig['encryption'] === 'ssl') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($mailConfig['encryption'] === 'tls') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mailer->setFrom($mailConfig['from'], $mailConfig['from_name']);
        $mailer->addAddress('pagos@wiznet.mx');
        if ($paymentData['email'] !== '') {
            $mailer->addReplyTo($paymentData['email'], $paymentData['name']);
        }

        $mailer->Subject = $subject;
        $mailer->isHTML(true);
        $mailer->Body = $htmlBody;
        $mailer->AltBody = $textBody;
        $mailer->addAttachment($paymentData['attachment_absolute_path'], $paymentData['attachment_name']);
        $mailer->send();

        return ['success' => true, 'error' => null];
    } catch (PHPMailerException $exception) {
        error_log('PHPMailer Error: ' . $exception->getMessage());
        error_log('Error enviando registro de pago: ' . $exception->getMessage());
        return ['success' => false, 'error' => 'No fue posible enviar el correo de notificacion.'];
    }
}

function send_support_notification_email(array $supportData): array
{
    if (!class_exists(PHPMailer::class)) {
        error_log('PHPMailer no esta disponible en el proyecto.');
        return ['success' => false, 'error' => 'No fue posible enviar el correo de notificacion.'];
    }

    $mailConfigResult = load_mail_config();
    if (isset($mailConfigResult['error'])) {
        error_log($mailConfigResult['error']);
        return ['success' => false, 'error' => 'No fue posible enviar el correo de notificacion.'];
    }

    $mailConfig = $mailConfigResult['config'];
    $subject = sprintf(
        'Nueva Solicitud de Soporte - %s - Cliente: %s',
        $supportData['name'],
        $supportData['client_number']
    );
    $comments = $supportData['comments'] !== '' ? $supportData['comments'] : '(sin comentarios)';

    $htmlBody = sprintf(
        '<html><body><p><strong>Nombre:</strong> %s</p><p><strong>Oficina:</strong> %s</p><p><strong>Numero de Cliente:</strong> %s</p><p><strong>Correo:</strong> %s</p><p><strong>Telefono:</strong> %s</p><p><strong>Comentarios:</strong><br>%s</p></body></html>',
        e($supportData['name']),
        e($supportData['office']),
        e($supportData['client_number']),
        e($supportData['email']),
        e($supportData['phone']),
        nl2br(e($comments))
    );

    $textBody = implode("\n", [
        'Nombre: ' . $supportData['name'],
        'Oficina: ' . $supportData['office'],
        'Numero de Cliente: ' . $supportData['client_number'],
        'Correo: ' . $supportData['email'],
        'Telefono: ' . $supportData['phone'],
        'Comentarios: ' . $comments,
    ]);

    try {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $mailConfig['host'];
        $mailer->Port = $mailConfig['port'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $mailConfig['user'];
        $mailer->Password = $mailConfig['pass'];
        $mailer->CharSet = 'UTF-8';

        if ($mailConfig['encryption'] === 'ssl') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($mailConfig['encryption'] === 'tls') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mailer->setFrom($mailConfig['from'], $mailConfig['from_name']);
        $mailer->addAddress('soporte@wiznet.mx');
        if ($supportData['email'] !== '') {
            $mailer->addReplyTo($supportData['email'], $supportData['name']);
        }

        $mailer->Subject = $subject;
        $mailer->isHTML(true);
        $mailer->Body = $htmlBody;
        $mailer->AltBody = $textBody;

        if (($supportData['attachment_absolute_path'] ?? '') !== '' && is_file($supportData['attachment_absolute_path'])) {
            $mailer->addAttachment($supportData['attachment_absolute_path'], $supportData['attachment_name'] ?? 'adjunto');
        }

        $mailer->send();

        return ['success' => true, 'error' => null];
    } catch (PHPMailerException $exception) {
        error_log('PHPMailer Error: ' . $exception->getMessage());
        error_log('Error enviando solicitud de soporte: ' . $exception->getMessage());
        return ['success' => false, 'error' => 'No fue posible enviar el correo de notificacion.'];
    }
}

function process_contact_submission(): array
{
    $result = form_result();
    $result['old'] = [
        'name' => sanitize_string($_POST['name'] ?? ''),
        'phone' => sanitize_phone($_POST['phone'] ?? ''),
        'email' => sanitize_string($_POST['email'] ?? ''),
        'comments' => sanitize_multiline($_POST['comments'] ?? ''),
        'plan' => sanitize_string($_POST['plan'] ?? ''),
    ];

    validate_required($result['errors'], 'name', $result['old']['name'], 'Nombre completo');
    validate_required($result['errors'], 'phone', $result['old']['phone'], 'Número telefónico');
    validate_required($result['errors'], 'email', $result['old']['email'], 'Correo electrónico');
    validate_required($result['errors'], 'comments', $result['old']['comments'], 'Comentarios');
    validate_email($result['errors'], 'email', $result['old']['email']);
    validate_phone($result['errors'], 'phone', $result['old']['phone']);

    if ($result['errors'] !== []) {
        return $result;
    }

    $saved = store_submission('contact', $result['old']);
    $result['success'] = $saved;
    $result['message'] = $saved
        ? 'Tu mensaje fue enviado correctamente. Te contactaremos a la brevedad.'
        : 'No fue posible guardar tu mensaje. Intenta nuevamente.';

    if ($saved) {
        $result['old'] = ['plan' => $result['old']['plan']];
    }

    return $result;
}

function process_support_submission(array $site): array
{
    $result = form_result();
    $result['old'] = [
        'name' => sanitize_string($_POST['name'] ?? ''),
        'office' => sanitize_string($_POST['office'] ?? ''),
        'client_number' => sanitize_string($_POST['client_number'] ?? ''),
        'email' => sanitize_string($_POST['email'] ?? ''),
        'phone' => sanitize_phone($_POST['phone'] ?? ''),
        'comments' => sanitize_multiline($_POST['comments'] ?? ''),
    ];

    validate_required($result['errors'], 'name', $result['old']['name'], 'Nombre');
    validate_required($result['errors'], 'office', $result['old']['office'], 'Oficina');
    validate_required($result['errors'], 'client_number', $result['old']['client_number'], 'Cliente');
    validate_required($result['errors'], 'email', $result['old']['email'], 'Correo electrónico');
    validate_required($result['errors'], 'phone', $result['old']['phone'], 'Teléfono');
    validate_email($result['errors'], 'email', $result['old']['email']);
    validate_phone($result['errors'], 'phone', $result['old']['phone']);
    validate_office($result['errors'], 'office', $result['old']['office'], $site['offices']);

    $upload = handle_upload('attachment', 'support', false);
    if (isset($upload['error'])) {
        $result['errors']['attachment'] = $upload['error'];
    }

    if ($result['errors'] !== []) {
        return $result;
    }

    $payload = $result['old'];
    $payload['attachment'] = $upload['path'] ?? null;
    $payload['attachment_name'] = $upload['name'] ?? null;

    $saved = store_submission('support', $payload);
    if (!$saved) {
        $result['message'] = 'No fue posible registrar tu solicitud de soporte.';
        return $result;
    }

    $mailPayload = $result['old'];
    $mailPayload['attachment_name'] = $upload['name'] ?? '';
    $mailPayload['attachment_absolute_path'] = isset($upload['path'])
        ? dirname(__DIR__) . '/' . ltrim((string) $upload['path'], '/')
        : '';

    $mailResult = send_support_notification_email($mailPayload);
    if (!$mailResult['success']) {
        $result['message'] = 'Tu solicitud de soporte fue registrada, pero no fue posible enviarla por correo al area de soporte.';
        return $result;
    }

    $result['success'] = true;
    $result['message'] = 'Tu solicitud de soporte fue registrada correctamente.';
    $result['old'] = [];

    return $result;
}

function process_payment_submission(array $site): array
{
    $result = form_result();
    $result['old'] = [
        'name' => sanitize_string($_POST['name'] ?? ''),
        'office' => sanitize_string($_POST['office'] ?? ''),
        'client_number' => sanitize_string($_POST['client_number'] ?? ''),
        'email' => sanitize_string($_POST['email'] ?? ''),
        'phone' => sanitize_phone($_POST['phone'] ?? ''),
        'comments' => sanitize_multiline($_POST['comments'] ?? ($_POST['message'] ?? '')),
    ];

    validate_required($result['errors'], 'name', $result['old']['name'], 'Nombre');
    validate_required($result['errors'], 'office', $result['old']['office'], 'Oficina');
    validate_required($result['errors'], 'client_number', $result['old']['client_number'], 'Cliente');
    validate_required($result['errors'], 'email', $result['old']['email'], 'Correo electrónico');
    validate_required($result['errors'], 'phone', $result['old']['phone'], 'Teléfono');
    validate_email($result['errors'], 'email', $result['old']['email']);
    validate_phone($result['errors'], 'phone', $result['old']['phone']);
    validate_office($result['errors'], 'office', $result['old']['office'], $site['offices']);

    $upload = handle_payment_receipt_upload('attachment');
    if (isset($upload['error'])) {
        $result['errors']['attachment'] = $upload['error'];
    }

    if ($result['errors'] !== []) {
        return $result;
    }

    $mailPayload = $result['old'];
    $mailPayload['attachment_name'] = $upload['name'] ?? '';
    $mailPayload['attachment_absolute_path'] = $upload['absolute_path'] ?? '';

    $mailResult = send_payment_notification_email($mailPayload);
    cleanup_temporary_file($mailPayload['attachment_absolute_path']);

    if (!$mailResult['success']) {
        $result['message'] = 'No fue posible enviar tu registro de pago. Intenta nuevamente en unos minutos.';
        return $result;
    }

    $result['success'] = true;
    $result['message'] = 'Tu registro de pago fue enviado correctamente.';
    $result['old'] = [];

    return $result;
}

function render_alert(array $result): void
{
    if ($result['message'] === '') {
        return;
    }

    $class = $result['success'] ? 'alert-success' : 'alert-error';
    printf('<div class="alert %s">%s</div>', e($class), e($result['message']));
}

function render_page_header(string $title, string $breadcrumb, string $pageClass = ''): void
{
    ?>
    <section class="page-hero <?= e($pageClass) ?>">
        <div class="container page-hero__content">
            <div>
                <p class="breadcrumb">Inicio / <?= e($breadcrumb) ?></p>
                <h1 class="sr-only"><?= e($title) ?></h1>
            </div>
            <div class="page-hero__icon">
                <?= render_icon('globe') ?>
            </div>
        </div>
    </section>
    <?php
}

function render_package_cards(array $items, bool $compact = false): void
{
    ?>
    <div class="package-grid<?= $compact ? ' package-grid--compact' : '' ?>">
        <?php foreach ($items as $package): ?>
            <article class="package-card">
                <div class="package-card__icon">
                    <?= render_icon('wifi') ?>
                </div>
                <h3><?= e($package['name']) ?></h3>
                <div class="divider"></div>
                <p><?= e($package['summary']) ?></p>
                <p><?= e($package['details']) ?></p>
                <p class="package-card__note"><?= e($package['note']) ?></p>
                <div class="package-card__price"><?= e($package['price']) ?></div>
                <p class="package-card__price-label">Costo Mensual</p>
                <a class="button button--success" href="<?= e(page_url('contacto.php?plan=' . urlencode($package['name']))) ?>">Contratar</a>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
}

function render_section_title(string $title, ?string $id = null, string $class = ''): void
{
    printf(
        '<div class="section-heading %s"%s><h2>%s</h2><span></span></div>',
        e($class),
        $id ? ' id="' . e($id) . '"' : '',
        e($title)
    );
}
