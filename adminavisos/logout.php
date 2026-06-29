<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
avisos_require_auth();

avisos_logout_user();
header('Location: ' . avisos_admin_url('login.php'), true, 303);
exit;
