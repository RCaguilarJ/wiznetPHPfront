<?php

declare(strict_types=1);

$pageContext = $pageContext ?? [];
$site = $site ?? site_config();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageContext['page_title'] ?? 'WIZNET') ?> | WIZNET</title>
    <meta name="description" content="<?= e($pageContext['meta_description'] ?? 'Servicio de Internet vía Antena y Fibra Óptica en Jalisco.') ?>">
    <meta property="og:title" content="<?= e($pageContext['page_title'] ?? 'WIZNET') ?>">
    <meta property="og:description" content="<?= e($pageContext['meta_description'] ?? 'Servicio de Internet vía Antena y Fibra Óptica en Jalisco.') ?>">
    <meta property="og:url" content="https://wiznet.mx<?= $_SERVER['REQUEST_URI'] ?>">
    <link rel="icon" type="image/png" href="<?= e(asset_url('img/logo.png')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/styles.css')) ?>">
   <!-- Matomo -->
   <script>
     var _paq = window._paq = window._paq || [];
     _paq.push(['trackPageView']);
     _paq.push(['enableLinkTracking']);
     (function() {
       var u="https://estadisticas.desingsgdl.com/";
       _paq.push(['setTrackerUrl', u+'matomo.php']);
       _paq.push(['setSiteId', '163']);
       var d=document, g=d.createElement('script'), 
       s=d.getElementsByTagName('script')[0];
       g.async=true; g.src=u+'matomo.js'; 
       s.parentNode.insertBefore(g,s);
     })();
   </script>
   <!-- End Matomo Code -->
</head>
<body class="<?= e($pageContext['body_class'] ?? '') ?>">
    <header class="site-header">
        <div class="topbar">
            <div class="container topbar__content">
                <a href="tel:<?= e(str_replace(' ', '', $site['contact']['phone'])) ?>" class="topbar__item">
                    <?= render_icon('phone', 'icon--small') ?>
                    <span><?= e($site['contact']['phone']) ?></span>
                </a>
                <a href="mailto:<?= e($site['contact']['email']) ?>" class="topbar__item">
                    <?= render_icon('mail', 'icon--small') ?>
                    <span><?= e($site['contact']['email']) ?></span>
                </a>
            </div>
        </div>
        <div class="navbar">
            <div class="container navbar__content">
                <a href="<?= e(page_url('index.php')) ?>" class="brand" aria-label="Ir al inicio de WIZNET">
                    <img src="<?= e(asset_url('img/logo.png')) ?>" alt="Logo WIZNET" width="180" height="180" class="brand__icon">
                    <span class="brand__wordmark">WIZNET</span>
                </a>
                <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <nav id="site-menu" class="site-nav">
                    <ul>
                        <?php foreach ($site['navigation'] as $item): ?>
                            <?php $hasChildren = !empty($item['children']); ?>
                            <li class="<?= $hasChildren ? 'has-submenu' : '' ?> <?= is_active_page($item['key']) ? 'is-active' : '' ?>">
                                <a href="<?= e(page_url($item['url'])) ?>">
                                    <span><?= e($item['label']) ?></span>
                                    <?php if ($hasChildren): ?>
                                        <?= render_icon('caret', 'icon--tiny') ?>
                                    <?php endif; ?>
                                </a>
                                <?php if ($hasChildren): ?>
                                    <div class="submenu">
                                        <?php foreach ($item['children'] as $child): ?>
                                            <a href="<?= e(page_url($child['url'])) ?>"><?= e($child['label']) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        <li class="nav-cta <?= is_active_page('contact') ? 'is-active' : '' ?>">
                            <a href="<?= e(page_url('contacto.php')) ?>">Contacto</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main>
