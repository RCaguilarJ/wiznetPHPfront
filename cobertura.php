<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$pageContext = [
    'page_title' => 'Cobertura',
    'meta_description' => 'Consulta la cobertura de servicio de Internet WIZNET en Zapotlanejo, Juanacatlán, El Salto y localidades cercanas en Jalisco.',
    'body_class' => 'page-inner',
];

require __DIR__ . '/includes/header.php';

render_page_header('COBERTURA DE SERVICIO', 'Nuestra Cobertura', 'page-hero--network page-hero--coverage');
?>

<section class="section">
    <div class="container coverage-stack">
        <?php foreach ($site['coverage'] as $zone): ?>
            <article class="coverage-card">
                <div class="coverage-card__intro">
                    <div class="coverage-card__icon">
                        <?= render_icon('pin') ?>
                    </div>
                    <span class="section-line"></span>
                    <h2><?= e($zone['zone']) ?></h2>
                    <p>Localidades con servicio activo:</p>
                </div>
                <ul class="coverage-card__list">
                    <?php foreach ($zone['locations'] as $location): ?>
                        <li>
                            <?= render_icon('check', 'icon--tiny') ?>
                            <span><?= e($location) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
