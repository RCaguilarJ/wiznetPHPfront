<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/php_compat.php';

$avisosDbCandidates = [
    __DIR__ . '/../../includes/db.php',
    __DIR__ . '/../includes/db.php',
];

$avisosDbLoaded = false;
foreach ($avisosDbCandidates as $avisosDbPath) {
    if (is_file($avisosDbPath)) {
        require_once $avisosDbPath;
        $avisosDbLoaded = true;
        break;
    }
}

if (!$avisosDbLoaded) {
    throw new RuntimeException('No se encontro el archivo includes/db.php requerido por adminavisos.');
}

require_once __DIR__ . '/../includes/avisos_support.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const AVISOS_ADMIN_USERNAME = 'wiznetadmin';
const AVISOS_ADMIN_PASSWORD_HASH = '$2y$12$wNKT5rmtuDr1Zxl45CNvY.KpIKBMrFDkBg3l5MQEJ4XisfWdrtH7y';

/*
 * Genera un hash bcrypt nuevo con:
 * php -r "echo password_hash('TuPasswordSeguro', PASSWORD_BCRYPT), PHP_EOL;"
 */

function avisos_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function avisos_base_path(): string
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

        if (str_starts_with($normalizedProjectRoot, $normalizedDocumentRoot)) {
            $relativePath = substr($normalizedProjectRoot, strlen($normalizedDocumentRoot));
            $basePath = $relativePath === false || $relativePath === '' ? '' : rtrim($relativePath, '/');

            return $basePath;
        }
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $fallback = dirname(dirname($scriptName));
    $basePath = $fallback === '/' || $fallback === '.' ? '' : rtrim($fallback, '/');

    return $basePath;
}

function avisos_admin_url(string $path = 'dashboard.php'): string
{
    $normalizedPath = ltrim($path, '/');

    return avisos_base_path() . '/adminavisos/' . $normalizedPath;
}

function avisos_redirect(string $path): void
{
    header('Location: ' . avisos_admin_url($path), true, 303);
    exit;
}

function avisos_request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function avisos_is_authenticated(): bool
{
    return !empty($_SESSION['avisos_admin_authenticated']);
}

function avisos_login_user(): void
{
    session_regenerate_id(true);
    $_SESSION['avisos_admin_authenticated'] = true;
    $_SESSION['avisos_admin_username'] = AVISOS_ADMIN_USERNAME;
}

function avisos_require_auth(): void
{
    if (!avisos_is_authenticated()) {
        avisos_redirect('login.php');
    }
}

function avisos_logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function avisos_csrf_token(): string
{
    if (empty($_SESSION['avisos_csrf_token'])) {
        $_SESSION['avisos_csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['avisos_csrf_token'];
}

function avisos_validate_csrf(?string $token): bool
{
    if (!is_string($token) || $token === '') {
        return false;
    }

    $sessionToken = $_SESSION['avisos_csrf_token'] ?? '';

    return is_string($sessionToken) && hash_equals($sessionToken, $token);
}

function avisos_require_csrf(?string $token): void
{
    if (!avisos_validate_csrf($token)) {
        http_response_code(400);
        exit('Token CSRF invalido.');
    }
}

function avisos_set_flash(string $type, string $message): void
{
    $_SESSION['avisos_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function avisos_pull_flash(): ?array
{
    if (!isset($_SESSION['avisos_flash']) || !is_array($_SESSION['avisos_flash'])) {
        return null;
    }

    $flash = $_SESSION['avisos_flash'];
    unset($_SESSION['avisos_flash']);

    return $flash;
}

function avisos_set_form_state(array $state): void
{
    $_SESSION['avisos_form_state'] = $state;
}

function avisos_pull_form_state(): ?array
{
    if (!isset($_SESSION['avisos_form_state']) || !is_array($_SESSION['avisos_form_state'])) {
        return null;
    }

    $state = $_SESSION['avisos_form_state'];
    unset($_SESSION['avisos_form_state']);

    return $state;
}

function avisos_normalize_string(string $value): string
{
    $value = trim($value);
    return preg_replace('/\s+/u', ' ', $value) ?? $value;
}

function avisos_normalize_multiline(string $value): string
{
    $value = trim($value);
    return preg_replace("/\r\n|\r/u", "\n", $value) ?? $value;
}

function avisos_allowed_types(): array
{
    return ['advertencia', 'recomendacion'];
}

function avisos_pdo(): PDO
{
    return wiznet_avisos_pdo();
}
