<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$wiznetComposerAutoload = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_readable($wiznetComposerAutoload)) {
    require_once $wiznetComposerAutoload;
}

function wiznet_base_path(array $config): string
{
    if (!empty($config['site']['base_path'])) {
        return rtrim($config['site']['base_path'], '/');
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/wiznetPHP/index.php';
    $dir = str_replace('\\', '/', dirname($scriptName));

    return $dir === '/' ? '' : rtrim($dir, '/');
}

function wiznet_url(array $config, string $page = 'home', array $params = []): string
{
    $basePath = wiznet_base_path($config);
    $query = $params;

    if ($page === 'home') {
        return $basePath . '/';
    }

    $basePath .= '/index.php';
    $query['page'] = $page;

    return $basePath . '?' . http_build_query($query);
}

function wiznet_asset(array $config, string $path): string
{
    return wiznet_base_path($config) . '/' . ltrim($path, '/');
}

function wiznet_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function wiznet_active($targets, string $currentPage): string
{
    $targets = (array) $targets;

    return in_array($currentPage, $targets, true) ? 'is-active' : '';
}

function wiznet_get_routes(): array
{
    return [
        'home' => [
            'label' => 'Inicio',
            'title' => 'WIZNET | Internet por Antena y Fibra Optica',
            'view' => 'home.php',
            'hero' => 'home',
            'crumb' => 'Inicio',
        ],
        'internet-residencial' => [
            'label' => 'Internet Residencial',
            'title' => 'Paquetes Internet Residencial | WIZNET',
            'view' => 'internet-residencial.php',
            'hero' => 'inner',
            'crumb' => 'Internet Residencial',
        ],
        'contratar-servicio' => [
            'label' => 'Contratar Servicio',
            'title' => 'Contratar Servicio | WIZNET',
            'view' => 'contratar-servicio.php',
            'hero' => 'inner',
            'crumb' => 'Contratar Servicio',
        ],
        'cobertura' => [
            'label' => 'Cobertura',
            'title' => 'Cobertura de Servicio | WIZNET',
            'view' => 'cobertura.php',
            'hero' => 'inner',
            'crumb' => 'Nuestra Cobertura',
        ],
        'soporte' => [
            'label' => 'Soporte',
            'title' => 'Soporte | WIZNET',
            'view' => 'soporte.php',
            'hero' => 'inner',
            'crumb' => 'Soporte',
        ],
        'registro-pagos' => [
            'label' => 'Registro de Pagos',
            'title' => 'Registro de Pagos | WIZNET',
            'view' => 'registro-pagos.php',
            'hero' => 'inner',
            'crumb' => 'Registro de Pagos',
        ],
        'contacto' => [
            'label' => 'Contacto',
            'title' => 'Contacto | WIZNET',
            'view' => 'contacto.php',
            'hero' => 'inner',
            'crumb' => 'contacto',
        ],
    ];
}

function wiznet_csrf_token(): string
{
    if (empty($_SESSION['wiznet_csrf'])) {
        $_SESSION['wiznet_csrf'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['wiznet_csrf'];
}

function wiznet_check_csrf(?string $token): bool
{
    return isset($_SESSION['wiznet_csrf']) && is_string($token) && hash_equals($_SESSION['wiznet_csrf'], $token);
}

function wiznet_set_flash(string $type, string $message): void
{
    $_SESSION['wiznet_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function wiznet_get_flash(): ?array
{
    if (!isset($_SESSION['wiznet_flash'])) {
        return null;
    }

    $flash = $_SESSION['wiznet_flash'];
    unset($_SESSION['wiznet_flash']);

    return $flash;
}

function wiznet_clean(string $key): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    $value = preg_replace('/\s+/', ' ', $value);

    return trim((string) $value);
}

function wiznet_normalize_phone(string $phone): string
{
    return preg_replace('/[^\d+\s()-]/', '', $phone) ?? $phone;
}

function wiznet_ensure_tables(?mysqli $db): bool
{
    if (!$db) {
        return false;
    }

    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS website_requests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(40) NOT NULL,
    request_type VARCHAR(30) NOT NULL,
    service_type VARCHAR(50) DEFAULT NULL,
    office VARCHAR(80) DEFAULT NULL,
    client_name VARCHAR(150) NOT NULL,
    client_number VARCHAR(50) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    subject VARCHAR(180) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    plan_name VARCHAR(150) DEFAULT NULL,
    attachment_path VARCHAR(255) DEFAULT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'Nuevo',
    metadata_json LONGTEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_request_type (request_type),
    INDEX idx_created_at (created_at),
    INDEX idx_client_number (client_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

    return $db->query($sql) === true;
}

function wiznet_generate_folio(string $prefix): string
{
    return strtoupper($prefix) . '-' . date('Ymd-His') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

function wiznet_env_value(string $key, string $default = ''): string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null) {
        return $default;
    }

    return (string) $value;
}

function wiznet_load_mail_config(): array
{
    $config = [
        'host' => wiznet_env_value('MAIL_HOST'),
        'port' => (int) wiznet_env_value('MAIL_PORT', '465'),
        'encryption' => strtolower(wiznet_env_value('MAIL_ENCRYPTION', 'ssl')),
        'user' => wiznet_env_value('MAIL_USER'),
        'pass' => wiznet_env_value('MAIL_PASS'),
        'from' => wiznet_env_value('MAIL_FROM'),
        'from_name' => wiznet_env_value('MAIL_FROM_NAME', 'WIZNET'),
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

function wiznet_cleanup_file(?string $path): void
{
    if (!is_string($path) || $path === '' || !is_file($path)) {
        return;
    }

    @unlink($path);
}

function wiznet_handle_upload(string $fieldName, string $targetDir, array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'webp']): array
{
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return ['path' => null, 'name' => null, 'mime' => null, 'error' => null];
    }

    $file = $_FILES[$fieldName];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'name' => null, 'mime' => null, 'error' => null];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['path' => null, 'name' => null, 'error' => 'No fue posible cargar el archivo adjunto.'];
    }

    if (($file['size'] ?? 0) > 20 * 1024 * 1024) {
        return ['path' => null, 'name' => null, 'error' => 'El archivo excede el limite de 20MB.'];
    }

    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        $labels = array_map(static fn(string $value): string => strtoupper($value), $allowedExtensions);
        return ['path' => null, 'name' => null, 'error' => 'El archivo debe ser ' . implode(', ', $labels) . '.'];
    }

    $allowedMimeMap = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'pdf' => ['application/pdf'],
        'webp' => ['image/webp'],
    ];
    $allowedMime = [];
    foreach ($allowedExtensions as $allowedExtension) {
        foreach ($allowedMimeMap[$allowedExtension] ?? [] as $mimeType) {
            $allowedMime[] = $mimeType;
        }
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, (string) $file['tmp_name']) : null;
    if ($finfo) {
        finfo_close($finfo);
    }

    if ($mime !== null && !in_array($mime, $allowedMime, true)) {
        return ['path' => null, 'name' => null, 'error' => 'El archivo adjunto no tiene un formato valido.'];
    }

    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        return ['path' => null, 'name' => null, 'error' => 'No fue posible preparar la carpeta de archivos.'];
    }

    $originalName = (string) $file['name'];
    $safeOriginalName = preg_replace('/[^a-zA-Z0-9._-]/', '-', basename($originalName)) ?: ('archivo.' . $extension);
    $safeName = date('YmdHis') . '-' . $safeOriginalName;
    $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safeName;

    if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
        return ['path' => null, 'name' => null, 'error' => 'No fue posible guardar el archivo subido.'];
    }

    if (!is_file($destination) || !is_readable($destination)) {
        return ['path' => null, 'name' => null, 'mime' => null, 'error' => 'No fue posible preparar el archivo adjunto para su envio.'];
    }

    return ['path' => $destination, 'name' => $safeOriginalName, 'mime' => $mime ?: 'application/octet-stream', 'error' => null];
}

function wiznet_detect_attachment_mime(string $absolutePath, string $fallback = 'application/octet-stream'): string
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $absolutePath) : false;
    if ($finfo) {
        finfo_close($finfo);
    }

    return is_string($mime) && $mime !== '' ? $mime : $fallback;
}

function wiznet_build_mail_attachment(string $absolutePath, string $attachmentName, ?string $mime = null): array
{
    if ($absolutePath === '' || !is_file($absolutePath) || !is_readable($absolutePath)) {
        throw new RuntimeException('El archivo adjunto no esta disponible para lectura.');
    }

    $content = file_get_contents($absolutePath);
    if ($content === false) {
        throw new RuntimeException('No fue posible leer el archivo adjunto.');
    }

    return [
        'name' => $attachmentName !== '' ? $attachmentName : basename($absolutePath),
        'content' => $content,
        'mime' => $mime ?: wiznet_detect_attachment_mime($absolutePath),
    ];
}

function wiznet_send_payment_email(array $record, array $attachment): bool
{
    if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
        error_log('PHPMailer no esta disponible en el proyecto.');
        return false;
    }

    $mailConfigResult = wiznet_load_mail_config();
    if (isset($mailConfigResult['error'])) {
        error_log($mailConfigResult['error']);
        return false;
    }

    $mailConfig = $mailConfigResult['config'];
    $subject = sprintf(
        'Nuevo Registro de Pago - %s - Cliente: %s',
        $record['client_name'],
        $record['client_number']
    );
    $comments = $record['message'] !== '' ? $record['message'] : '(sin comentarios)';

    $htmlBody = sprintf(
        '<html><body><p><strong>Nombre:</strong> %s</p><p><strong>Oficina:</strong> %s</p><p><strong>Número de Cliente:</strong> %s</p><p><strong>Correo:</strong> %s</p><p><strong>Teléfono:</strong> %s</p><p><strong>Comentarios:</strong><br>%s</p></body></html>',
        wiznet_escape($record['client_name']),
        wiznet_escape((string) $record['office']),
        wiznet_escape((string) $record['client_number']),
        wiznet_escape((string) $record['email']),
        wiznet_escape((string) $record['phone']),
        nl2br(wiznet_escape($comments))
    );

    $textBody = implode("\n", [
        'Nombre: ' . $record['client_name'],
        'Oficina: ' . (string) $record['office'],
        'Número de Cliente: ' . (string) $record['client_number'],
        'Correo: ' . (string) $record['email'],
        'Teléfono: ' . (string) $record['phone'],
        'Comentarios: ' . $comments,
    ]);

    try {
        $mailAttachment = wiznet_build_mail_attachment(
            (string) $attachment['path'],
            (string) ($attachment['name'] ?? 'comprobante'),
            $attachment['mime'] ?? null
        );

        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $mailConfig['host'];
        $mailer->Port = $mailConfig['port'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $mailConfig['user'];
        $mailer->Password = $mailConfig['pass'];
        $mailer->CharSet = 'UTF-8';

        if ($mailConfig['encryption'] === 'ssl') {
            $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($mailConfig['encryption'] === 'tls') {
            $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mailer->setFrom($mailConfig['from'], $mailConfig['from_name']);
        $mailer->addAddress('pagos@wiznet.mx');
        $mailer->addBCC('carlagular800@gmail.com');
        if (!empty($record['email'])) {
            $mailer->addReplyTo((string) $record['email'], $record['client_name']);
        }

        $mailer->Subject = $subject;
        $mailer->isHTML(true);
        $mailer->Body = $htmlBody;
        $mailer->AltBody = $textBody;
        $mailer->addStringAttachment(
            $mailAttachment['content'],
            $mailAttachment['name'],
            \PHPMailer\PHPMailer\PHPMailer::ENCODING_BASE64,
            $mailAttachment['mime']
        );
        $mailer->send();

        return true;
    } catch (\Throwable $exception) {
        error_log('PHPMailer Error: ' . $exception->getMessage());
        error_log('Error enviando registro de pago compartido: ' . $exception->getMessage());
        return false;
    }
}

function wiznet_persist_request(?mysqli $db, array $record, string $fallbackPath): bool
{
    if ($db && wiznet_ensure_tables($db)) {
        $stmt = $db->prepare(
            'INSERT INTO website_requests
            (folio, request_type, service_type, office, client_name, client_number, email, phone, address, subject, message, plan_name, attachment_path, metadata_json)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt) {
            $metadataJson = json_encode($record['metadata'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $stmt->bind_param(
                'ssssssssssssss',
                $record['folio'],
                $record['request_type'],
                $record['service_type'],
                $record['office'],
                $record['client_name'],
                $record['client_number'],
                $record['email'],
                $record['phone'],
                $record['address'],
                $record['subject'],
                $record['message'],
                $record['plan_name'],
                $record['attachment_path'],
                $metadataJson
            );

            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) {
                return true;
            }
        }
    }

    $logLine = json_encode(
        $record + ['saved_at' => date(DATE_ATOM)],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    return file_put_contents($fallbackPath, $logLine . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}

function wiznet_redirect(array $config, string $page, array $params = []): void
{
    header('Location: ' . wiznet_url($config, $page, $params));
    exit;
}

function wiznet_handle_form_submission(array $config, ?mysqli $db): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $page = wiznet_clean('page_slug') ?: 'home';
    $formType = wiznet_clean('form_type');

    if (!wiznet_check_csrf($_POST['csrf_token'] ?? null)) {
        wiznet_set_flash('error', 'La sesion del formulario expiró. Intente nuevamente.');
        wiznet_redirect($config, $page);
    }

    $attachment = ['path' => null, 'name' => null, 'error' => null];
    if (in_array($formType, ['support', 'payment'], true)) {
        if ($formType === 'support') {
            $attachment = wiznet_handle_upload(
                'attachment',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'support'
            );
        } else {
            $attachment = wiznet_handle_upload(
                'attachment',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'payment',
                ['jpg', 'jpeg', 'png', 'pdf']
            );
        }

        if ($attachment['error']) {
            wiznet_set_flash('error', $attachment['error']);
            wiznet_redirect($config, $page);
        }
    }

    $record = [
        'folio' => '',
        'request_type' => $formType,
        'service_type' => null,
        'office' => null,
        'client_name' => '',
        'client_number' => null,
        'email' => null,
        'phone' => null,
        'address' => null,
        'subject' => null,
        'message' => null,
        'plan_name' => null,
        'attachment_path' => $attachment['path'],
        'metadata' => [],
    ];

    if ($formType === 'contact') {
        $record['folio'] = wiznet_generate_folio('CTO');
        $record['client_name'] = wiznet_clean('name');
        $record['email'] = wiznet_clean('email');
        $record['phone'] = wiznet_normalize_phone(wiznet_clean('phone'));
        $record['subject'] = 'Formulario de contacto';
        $record['message'] = wiznet_clean('message');

        if ($record['client_name'] === '' || $record['email'] === '' || $record['phone'] === '' || $record['message'] === '') {
            wiznet_set_flash('error', 'Completa todos los campos obligatorios del formulario de contacto.');
            wiznet_redirect($config, $page);
        }
    } elseif ($formType === 'support') {
        $record['folio'] = wiznet_generate_folio('SUP');
        $record['office'] = wiznet_clean('office');
        $record['client_name'] = wiznet_clean('name');
        $record['client_number'] = wiznet_clean('client_number');
        $record['email'] = wiznet_clean('email');
        $record['phone'] = wiznet_normalize_phone(wiznet_clean('phone'));
        $record['subject'] = wiznet_clean('issue_hint') ?: 'Solicitud de soporte';
        $record['message'] = wiznet_clean('message');
        $record['metadata'] = [
            'source' => 'public-support-form',
        ];

        if ($record['office'] === '' || $record['client_name'] === '' || $record['client_number'] === '' || $record['email'] === '' || $record['phone'] === '' || $record['message'] === '') {
            wiznet_set_flash('error', 'Completa todos los campos obligatorios del formulario de soporte.');
            wiznet_redirect($config, $page);
        }
    } elseif ($formType === 'payment') {
        $record['folio'] = wiznet_generate_folio('PAY');
        $record['office'] = wiznet_clean('office');
        $record['client_name'] = wiznet_clean('name');
        $record['client_number'] = wiznet_clean('client_number');
        $record['email'] = wiznet_clean('email');
        $record['phone'] = wiznet_normalize_phone(wiznet_clean('phone'));
        $record['subject'] = 'Registro de pago';
        $record['message'] = wiznet_clean('comments') ?: wiznet_clean('message');

        if ($record['office'] === '' || $record['client_name'] === '' || $record['client_number'] === '' || $record['email'] === '' || $record['phone'] === '' || $record['attachment_path'] === null) {
            wiznet_cleanup_file($attachment['path']);
            wiznet_set_flash('error', 'Completa los datos requeridos del registro de pago.');
            wiznet_redirect($config, $page);
        }

        if (!filter_var((string) $record['email'], FILTER_VALIDATE_EMAIL)) {
            wiznet_cleanup_file($attachment['path']);
            wiznet_set_flash('error', 'Ingresa un correo electronico valido.');
            wiznet_redirect($config, $page);
        }

        $phoneDigits = preg_replace('/\D/', '', (string) $record['phone']) ?? '';
        if (strlen($phoneDigits) < 10) {
            wiznet_cleanup_file($attachment['path']);
            wiznet_set_flash('error', 'Ingresa un numero telefonico valido.');
            wiznet_redirect($config, $page);
        }

        $sent = wiznet_send_payment_email($record, $attachment);
        wiznet_cleanup_file($attachment['path']);

        if (!$sent) {
            wiznet_set_flash('error', 'No fue posible enviar tu registro de pago. Intenta nuevamente en unos minutos.');
            wiznet_redirect($config, $page);
        }

        wiznet_set_flash('success', 'Tu registro de pago fue enviado correctamente.');
        wiznet_redirect($config, $page);
    } elseif ($formType === 'service-request') {
        $record['folio'] = wiznet_generate_folio('SRV');
        $record['service_type'] = wiznet_clean('service_type');
        $record['office'] = wiznet_clean('office');
        $record['client_name'] = wiznet_clean('name');
        $record['client_number'] = wiznet_clean('client_number');
        $record['email'] = wiznet_clean('email');
        $record['phone'] = wiznet_normalize_phone(wiznet_clean('phone'));
        $record['address'] = wiznet_clean('address');
        $record['subject'] = 'Solicitud de contratacion';
        $record['message'] = wiznet_clean('message');
        $record['plan_name'] = wiznet_clean('plan_name');
        $record['metadata'] = [
            'preferred_visit' => wiznet_clean('preferred_visit'),
        ];

        if ($record['service_type'] === '' || $record['office'] === '' || $record['client_name'] === '' || $record['email'] === '' || $record['phone'] === '' || $record['address'] === '' || $record['plan_name'] === '') {
            wiznet_set_flash('error', 'Completa los campos obligatorios de la contratacion.');
            wiznet_redirect($config, $page, ['plan' => $record['plan_name']]);
        }
    } else {
        wiznet_set_flash('error', 'Tipo de formulario no reconocido.');
        wiznet_redirect($config, $page);
    }

    $saved = wiznet_persist_request(
        $db,
        $record,
        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'form-submissions.log'
    );

    if (!$saved) {
        wiznet_set_flash('error', 'No fue posible guardar el registro. Revise la conexion o permisos del proyecto.');
        wiznet_redirect($config, $page);
    }

    wiznet_set_flash('success', 'Registro enviado correctamente. Folio de seguimiento: ' . $record['folio']);
    wiznet_redirect($config, $page);
}

function wiznet_icon(string $name): string
{
    $icons = [
        'wifi' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 48a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm0-15c-7.6 0-14.6 2.9-19.9 7.7l6.4 7.1A19.8 19.8 0 0 1 32 42c5.3 0 10.2 2.1 13.5 5.8l6.4-7.1A29.3 29.3 0 0 0 32 33Zm0-16C20.3 17 9.6 21.4 1.7 29l6.5 7.1A35.5 35.5 0 0 1 32 27c9.1 0 17.4 3.4 23.8 9.1l6.5-7.1A45.1 45.1 0 0 0 32 17Z" fill="currentColor"/></svg>',
        'globe' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6C17.6 6 6 17.6 6 32s11.6 26 26 26 26-11.6 26-26S46.4 6 32 6Zm17.5 22h-8.8a40.5 40.5 0 0 0-4.6-13A20.2 20.2 0 0 1 49.5 28ZM32 11.1c2.9 0 7.7 6 10 16.9H22c2.3-10.9 7.1-16.9 10-16.9ZM15 44a20.2 20.2 0 0 1-2.5-8h8.8a40.5 40.5 0 0 0 4.6 13A20.2 20.2 0 0 1 15 44Zm-2.5-16A20.2 20.2 0 0 1 15 20a20.2 20.2 0 0 1 10.9-5 40.5 40.5 0 0 0-4.6 13h-8.8Zm19.5 24.9c-2.9 0-7.7-6-10-16.9h20c-2.3 10.9-7.1 16.9-10 16.9ZM42.7 36h8.8A20.2 20.2 0 0 1 49 44a20.2 20.2 0 0 1-10.9 5 40.5 40.5 0 0 0 4.6-13Z" fill="currentColor"/></svg>',
        'calendar' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M18 6h6v8h16V6h6v8h8a6 6 0 0 1 6 6v32a6 6 0 0 1-6 6H10a6 6 0 0 1-6-6V20a6 6 0 0 1 6-6h8V6Zm34 20H12v24h40V26Zm-7.7 6.8 4.2 4.2-12.7 12.7-8.3-8.3 4.2-4.2 4.1 4.1 8.5-8.5Z" fill="currentColor"/></svg>',
        'smile' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6C17.6 6 6 17.6 6 32s11.6 26 26 26 26-11.6 26-26S46.4 6 32 6Zm-9 19a4 4 0 1 1 0 8 4 4 0 0 1 0-8Zm18 0a4 4 0 1 1 0 8 4 4 0 0 1 0-8ZM20 40h24a12 12 0 0 1-24 0Z" fill="currentColor"/></svg>',
        'pin' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6c-9.9 0-18 8.1-18 18 0 13.5 18 34 18 34s18-20.5 18-34c0-9.9-8.1-18-18-18Zm0 24a6 6 0 1 1 0-12 6 6 0 0 1 0 12Z" fill="currentColor"/></svg>',
        'wrench' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="m59 20-9 9-7-1-9 9 18 18-6 6-18-18-16 16L3 50l16-16-18-18 6-6 18 18 9-9-1-7 9-9a14 14 0 0 1 17 17Z" fill="currentColor"/></svg>',
        'bulb' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6c-11.6 0-21 9.4-21 21 0 8.1 4.7 15.2 11.5 18.7V52a6 6 0 0 0 6 6h7a6 6 0 0 0 6-6v-6.3A21 21 0 0 0 53 27C53 15.4 43.6 6 32 6Zm7 46h-14v-4h14v4Zm-2.3-12.3-1.7.9V44H29v-3.4l-1.7-.9A15 15 0 0 1 17 27a15 15 0 0 1 30 0 15 15 0 0 1-10.3 12.7Z" fill="currentColor"/></svg>',
        'question' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6C17.6 6 6 17.6 6 32s11.6 26 26 26 26-11.6 26-26S46.4 6 32 6Zm0 42a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm8.2-18.4-3.1 2.2c-1.8 1.3-2.6 2.4-2.6 4.8h-5.8c0-4.5 1.4-6.7 4.7-9.1l3.2-2.3a4.6 4.6 0 0 0-2.7-8.2c-2.4 0-4.3 1.4-5.2 3.6l-5.6-2.3A10.8 10.8 0 0 1 33.6 12c6.5 0 11.6 4.4 11.6 10.8 0 3.2-1.6 5.5-5 6.8Z" fill="currentColor"/></svg>',
        'idea' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6c-10.5 0-19 8.5-19 19 0 6.5 3.3 12.2 8.3 15.7V47h21.4v-6.3A19 19 0 0 0 51 25c0-10.5-8.5-19-19-19Zm8 47H24v5h16v-5Zm-8-41c-7.2 0-13 5.8-13 13h-4c0-9.4 7.6-17 17-17v4Z" fill="currentColor"/></svg>',
        'mail' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M8 14h48a6 6 0 0 1 6 6v24a6 6 0 0 1-6 6H8a6 6 0 0 1-6-6V20a6 6 0 0 1 6-6Zm0 6v2l24 15 24-15v-2H8Zm48 24V29L32 44 8 29v15h48Z" fill="currentColor"/></svg>',
        'phone' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M18.7 8c1.7 0 3.3.7 4.4 2l6 6a6 6 0 0 1 .8 7.4l-3.5 5.6a37.8 37.8 0 0 0 8.6 8.6l5.6-3.5a6 6 0 0 1 7.4.8l6 6a6 6 0 0 1 2 4.4V52a6 6 0 0 1-6 6C24.5 58 6 39.5 6 18a6 6 0 0 1 6-6h6.7Z" fill="currentColor"/></svg>',
    ];

    return $icons[$name] ?? '';
}
