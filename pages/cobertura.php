<?php

declare(strict_types=1);
?>
<section class="shell coverage-section">
    <div class="section-heading section-heading-large">
        <h2>COBERTURA DE SERVICIO</h2>
    </div>

    <div class="coverage-stack">
        <?php foreach ($siteContent['coverage'] as $coverage): ?>
            <article class="coverage-card">
                <div class="coverage-city">
                    <div class="pin-icon"><?= wiznet_icon('pin'); ?></div>
                    <h3><?= wiznet_escape($coverage['city']); ?></h3>
                    <p>Localidades con servicio activo:</p>
                </div>

                <ul class="coverage-list">
                    <?php foreach ($coverage['zones'] as $zone): ?>
                        <li><?= wiznet_escape($zone); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>
