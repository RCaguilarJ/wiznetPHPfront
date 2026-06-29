<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

if (avisos_is_authenticated()) {
    avisos_redirect('dashboard.php');
}

avisos_redirect('');
