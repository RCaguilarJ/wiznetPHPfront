<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$welcomeImagePath = file_exists(__DIR__ . '/assets/img/hXuRlodtAmkwodk-800x450-noPad.jpg')
    ? asset_url('img/hXuRlodtAmkwodk-800x450-noPad.jpg')
    : asset_url('images/welcome-network.svg');

$pageContext = [
    'page_title' => 'Inicio',
    'meta_description' => 'Servicio de Internet vía Antena y Fibra Óptica en Jalisco. Paquetes residenciales y comerciales. WIZNET.',
    'body_class' => 'page-home',
];

require __DIR__ . '/includes/header.php';
?>

<section class="home-hero">
    <video class="home-hero__video" autoplay muted loop playsinline preload="auto" aria-hidden="true">
        <source src="<?= asset_url('uploads/Network01.mp4') ?>" type="video/mp4">
    </video>
    <div class="home-hero__overlay" aria-hidden="true"></div>
    <div class="container home-hero__inner">
        <div class="home-hero__copy">
            <h1><?= e($site['hero']['home_title']) ?></h1>
        </div>
    </div>
</section>

<section class="welcome-section">
    <div class="container welcome-grid">
        <div class="welcome-copy">
            <h2><?= e($site['hero']['welcome_title']) ?></h2>
            <span class="section-line"></span>
            <p><?= e($site['hero']['welcome_copy']) ?></p>
            <a class="button" href="<?= e(page_url($site['cta']['client_zone_url'])) ?>"><?= e($site['cta']['client_zone_label']) ?></a>
        </div>
        <div class="welcome-visual" aria-hidden="true">
            <img src="<?= e($welcomeImagePath) ?>" alt="">
        </div>
    </div>
</section>

<section class="section surface-pattern package-showcase">
    <div class="container package-showcase__shell">
        <?php render_section_title($site['packages']['residential']['title'], 'paquetes'); ?>
        <?php render_package_cards($site['packages']['residential']['items']); ?>
    </div>
</section>

<section class="section surface-pattern package-showcase">
    <div class="container package-showcase__shell">
        <?php render_section_title($site['packages']['commercial']['title'], 'paquetes-comerciales'); ?>
        <?php render_package_cards($site['packages']['commercial']['items']); ?>
    </div>
</section>

<section class="section process-section">
    <div class="container process-shell">
        <?php render_section_title('PROCESO DE CONTRATACIÓN'); ?>
        <div class="process-grid">
            <?php foreach ($site['steps'] as $step): ?>
                <article class="process-card">
                    <div class="process-card__icon">
                        <?= render_icon($step['icon'] ?? 'globe') ?>
                    </div>
                    <span class="section-line"></span>
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['copy']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section faq-section">
    <div class="container faq-wrap">
        <?php render_section_title('Preguntas Frecuentes'); ?>
        <div class="accordion">
            <?php foreach ($site['faq'] as $item): ?>
                <details>
                    <summary><?= e($item['question']) ?></summary>
                    <div class="accordion__content">
                        <?php if (is_array($item['answer'])): ?>
                            <?php foreach ($item['answer'] as $block): ?>
                                <?php
                                $text = is_array($block) ? ($block['text'] ?? '') : (string) $block;
                                $isStrong = is_array($block) && !empty($block['strong']);
                                ?>
                                <?php if ($isStrong): ?>
                                    <p><strong><?= e($text) ?></strong></p>
                                <?php else: ?>
                                    <p><?= e($text) ?></p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p><?= e($item['answer']) ?></p>
                        <?php endif; ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
