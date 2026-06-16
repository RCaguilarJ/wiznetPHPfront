<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$pageContext = [
    'page_title' => 'Internet Residencial',
    'meta_description' => 'Conoce los paquetes de Internet residencial WIZNET con servicio por antena y fibra óptica en Jalisco.',
    'body_class' => 'page-inner',
];

require __DIR__ . '/includes/header.php';

render_page_header('Paquetes Internet Residencial', 'Internet Residencial', 'page-hero--network page-hero--support');
?>

<section class="section">
    <div class="container process-shell">
        <?php render_section_title($site['packages']['residential']['title']); ?>
        <?php render_package_cards($site['packages']['residential']['items'], true); ?>
    </div>
</section>

<section class="section process-section">
    <div class="container process-shell">
        <?php render_section_title('PROCESO DE CONTRATACION'); ?>
        <div class="process-grid">
            <?php
            $packageSteps = [
                ['icon' => 'globe', 'title' => 'Selecciona tu Paquete', 'copy' => $site['steps'][0]['copy'] ?? ''],
                ['icon' => 'calendar-check', 'title' => 'Agenda tu Visita', 'copy' => $site['steps'][1]['copy'] ?? ''],
                ['icon' => 'smile', 'title' => 'Disfruta tu Servicio', 'copy' => $site['steps'][2]['copy'] ?? ''],
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
