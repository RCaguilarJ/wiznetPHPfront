<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$pageContext = [
    'page_title' => 'Internet Comercial',
    'meta_description' => 'Explora los paquetes de Internet comercial WIZNET para negocios y empresas en Jalisco.',
    'body_class' => 'page-inner',
];

require __DIR__ . '/includes/header.php';

render_page_header('Paquetes Internet Comercial', 'Internet Comercial', 'page-hero--network page-hero--support');
?>

<section class="section">
    <div class="container process-shell">
        <?php render_section_title($site['packages']['commercial']['title']); ?>
        <?php render_package_cards($site['packages']['commercial']['items'], true); ?>
    </div>
</section>

<section class="section process-section">
    <div class="container process-shell">
        <?php render_section_title('PROCESO DE CONTRATACION'); ?>
        <div class="process-grid">
            <?php
            $packageSteps = [
                ['icon' => 'wifi', 'title' => 'Selecciona tu Paquete', 'copy' => $site['steps'][0]['copy'] ?? ''],
                ['icon' => 'calendar-pin', 'title' => 'Agenda tu Visita', 'copy' => $site['steps'][1]['copy'] ?? ''],
                ['icon' => 'wifi-heart', 'title' => 'Disfruta tu Servicio', 'copy' => $site['steps'][2]['copy'] ?? ''],
            ];
            ?>
            <?php foreach ($packageSteps as $step): ?>
                <article class="process-card">
                    <div class="process-card__icon">
                        <?= render_icon($step['icon']) ?>
                    </div>
                    <span class="section-line"></span>
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['copy']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
