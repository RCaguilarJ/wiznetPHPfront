<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

if (avisos_is_authenticated()) {
    avisos_redirect('dashboard');
}

$error = '';

if (avisos_request_method() === 'POST') {
    avisos_require_csrf($_POST['csrf_token'] ?? null);

    $username = avisos_normalize_string((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (
        hash_equals(AVISOS_ADMIN_USERNAME, $username)
        && password_verify($password, AVISOS_ADMIN_PASSWORD_HASH)
    ) {
        avisos_login_user();
        avisos_set_flash('success', 'Sesion iniciada correctamente.');
        avisos_redirect('dashboard');
    }

    $error = 'Credenciales invalidas.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Avisos | Acceso</title>
    <style>
        :root {
            --blue-800: #21469c;
            --blue-700: #274db2;
            --green-600: #10a735;
            --red-600: #cf171f;
            --ink-900: #121212;
            --ink-700: #4b5563;
            --surface: #ffffff;
            --border: #d9dfe8;
            --shadow: 0 18px 42px rgba(15, 33, 63, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink-900);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98)),
                radial-gradient(circle at top left, rgba(33, 70, 156, 0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(16, 167, 53, 0.12), transparent 35%);
        }

        .login-card {
            width: min(100%, 420px);
            padding: 2rem;
            background: var(--surface);
            border: 1px solid rgba(33, 70, 156, 0.1);
            box-shadow: var(--shadow);
        }

        h1 {
            margin: 0 0 0.75rem;
            font-size: 1.8rem;
        }

        p {
            margin: 0 0 1.5rem;
            color: var(--ink-700);
            line-height: 1.6;
        }

        label {
            display: block;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        span {
            display: block;
            margin-bottom: 0.45rem;
        }

        input {
            width: 100%;
            padding: 0.85rem 0.95rem;
            border: 1px solid var(--border);
        }

        button {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 0;
            background: var(--blue-800);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: var(--blue-700);
        }

        .alert {
            margin-bottom: 1rem;
            padding: 0.9rem 1rem;
            font-weight: 600;
        }

        .alert-error {
            color: #9d1e25;
            background: rgba(207, 23, 31, 0.1);
            border: 1px solid rgba(207, 23, 31, 0.16);
        }

        .login-help {
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <section class="login-card">
        <h1>Admin Avisos</h1>
        <p>Panel oculto para administrar avisos publicos.</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= avisos_e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= avisos_e(avisos_admin_url('')) ?>">
            <input type="hidden" name="csrf_token" value="<?= avisos_e(avisos_csrf_token()) ?>">

            <label>
                <span>Usuario</span>
                <input type="text" name="username" autocomplete="username" required>
            </label>

            <label>
                <span>Contrasena</span>
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <button type="submit">Entrar</button>
        </form>

        <p class="login-help">Usuario configurado: <strong><?= avisos_e(AVISOS_ADMIN_USERNAME) ?></strong>.</p>
    </section>
</body>
</html>
