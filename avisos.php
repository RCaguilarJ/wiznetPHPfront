<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/avisos_support.php';
require_once __DIR__ . '/includes/avisos_defaults.php';

$pageContext = [
    'page_title' => 'Avisos',
    'meta_description' => 'Avisos importantes y recomendaciones de servicio de WIZNET.',
    'body_class' => 'page-avisos',
];

$groupLabels = [
    'advertencia' => [
        'title' => 'Advertencias',
        'icon' => '&#10006;',
        'icon_class' => 'avisos-item__icon--warning',
        'empty' => 'No hay advertencias activas por el momento.',
    ],
    'recomendacion' => [
        'title' => 'Recomendaciones',
        'icon' => '&#10004;',
        'icon_class' => 'avisos-item__icon--recommendation',
        'empty' => 'No hay recomendaciones activas por el momento.',
    ],
];

$avisosPorTipo = [
    'advertencia' => [],
    'recomendacion' => [],
];
$loadSource = 'database';

try {
    $pdo = wiznet_avisos_pdo();
    $statement = $pdo->prepare(
        "SELECT id, titulo, tipo, contenido
        FROM avisos
        WHERE activo = :activo
        ORDER BY CASE WHEN tipo = 'advertencia' THEN 0 ELSE 1 END, orden ASC, created_at DESC"
    );
    $statement->execute(['activo' => 1]);

    foreach ($statement->fetchAll() as $aviso) {
        $tipo = $aviso['tipo'];

        if (!isset($avisosPorTipo[$tipo])) {
            continue;
        }

        $avisosPorTipo[$tipo][] = $aviso;
    }
} catch (Throwable $exception) {
    $avisosPorTipo = wiznet_default_avisos();
    $loadSource = 'defaults';
}

if ($avisosPorTipo['advertencia'] === [] && $avisosPorTipo['recomendacion'] === []) {
    $avisosPorTipo = wiznet_default_avisos();
    $loadSource = 'defaults';
}

require __DIR__ . '/includes/header.php';
render_page_header('Avisos', 'Avisos');
?>
<style>
    .page-avisos .faq-section {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 253, 0.98)),
            radial-gradient(circle at top right, rgba(33, 70, 156, 0.08), transparent 32%);
    }

    .avisos-shell {
        max-width: 980px;
        margin: 0 auto;
    }

    .avisos-accordion {
        overflow: hidden;
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid rgba(33, 70, 156, 0.12);
        box-shadow: var(--shadow);
    }

    .avisos-accordion details + details {
        border-top: 1px solid rgba(33, 70, 156, 0.1);
    }

    .avisos-accordion summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.2rem;
    }

    .avisos-summary__title {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        color: var(--ink-900);
        font-size: clamp(1rem, 0.95rem + 0.2vw, 1.1rem);
    }

    .avisos-summary__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        min-height: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: rgba(33, 70, 156, 0.08);
        color: var(--blue-800);
        font-size: 0.9rem;
        font-weight: 700;
    }

    .avisos-list {
        display: grid;
        gap: 0;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .avisos-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 0.9rem;
        padding: 1rem 0;
        border-top: 1px solid #eceff4;
    }

    .avisos-item:first-child {
        border-top: 0;
        padding-top: 0.15rem;
    }

    .avisos-item__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        font-size: 1rem;
        font-weight: 700;
        line-height: 1;
    }

    .avisos-item__icon--warning {
        color: #fff;
        background: var(--red-600);
    }

    .avisos-item__icon--recommendation {
        color: #fff;
        background: var(--green-600);
    }

    .avisos-item h3 {
        margin: 0 0 0.35rem;
        font-size: clamp(1rem, 0.94rem + 0.18vw, 1.08rem);
    }

    .avisos-item p {
        margin: 0;
        color: var(--ink-700);
        line-height: 1.7;
    }

    .avisos-empty {
        margin: 0;
        padding: 0.2rem 0 0.6rem;
        color: var(--ink-700);
    }

    .avisos-callout {
        padding: clamp(1.25rem, 2.5vw, 1.75rem);
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(33, 70, 156, 0.12);
        box-shadow: var(--shadow);
    }

    @media (max-width: 767px) {
        .avisos-accordion summary {
            align-items: flex-start;
        }

        .avisos-item {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="section faq-section">
    <div class="container faq-wrap avisos-shell">
        <?php render_section_title('Avisos y recomendaciones'); ?>
        <p class="section-subtitle">Consulta aqui los mensajes importantes y las mejores practicas para tu servicio.</p>

        <div class="avisos-callout">
            <div class="accordion avisos-accordion">
                <?php $groupIndex = 0; ?>
                <?php foreach ($groupLabels as $tipo => $config): ?>
                    <details <?= $groupIndex === 0 ? 'open' : '' ?>>
                        <summary>
                            <span class="avisos-summary__title">
                                <span class="avisos-item__icon <?= e($config['icon_class']) ?>" aria-hidden="true"><?= $config['icon'] ?></span>
                                <span><?= e($config['title']) ?></span>
                            </span>
                            <span class="avisos-summary__badge"><?= count($avisosPorTipo[$tipo]) ?></span>
                        </summary>
                        <div class="accordion__content">
                            <?php if ($avisosPorTipo[$tipo] === []): ?>
                                <p class="avisos-empty"><?= e($config['empty']) ?></p>
                            <?php else: ?>
                                <ul class="avisos-list">
                                    <?php foreach ($avisosPorTipo[$tipo] as $aviso): ?>
                                        <li class="avisos-item">
                                            <span class="avisos-item__icon <?= e($config['icon_class']) ?>" aria-hidden="true"><?= $config['icon'] ?></span>
                                            <div>
                                                <h3><?= e($aviso['titulo']) ?></h3>
                                                <p><?= nl2br(e($aviso['contenido'])) ?></p>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </details>
                    <?php $groupIndex++; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($loadSource === 'defaults'): ?>
            <p class="section-subtitle">Se muestran avisos predeterminados mientras se cargan los registros del panel.</p>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
