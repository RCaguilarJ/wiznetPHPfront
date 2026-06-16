<?php

declare(strict_types=1);
?>
<section class="shell page-section">
    <div class="section-heading section-heading-large">
        <h2>Paquetes Internet Residencial</h2>
    </div>

    <div class="package-grid package-grid-wide">
        <?php foreach ($siteContent['residential_packages'] as $package): ?>
            <article class="package-card package-card-wide">
                <div class="package-icon"><?= wiznet_icon('wifi'); ?></div>
                <h3><?= wiznet_escape($package['name']); ?></h3>
                <p><?= wiznet_escape($package['description']); ?></p>
                <p><?= wiznet_escape($package['details']); ?></p>
                <strong>*Consulte gastos de activacion de servicio y instalacion.</strong>
                <div class="price-block">
                    <span class="price"><?= wiznet_escape($package['price']); ?></span>
                    <small><?= wiznet_escape($package['price_note']); ?></small>
                </div>
                <a class="success-button" href="<?= wiznet_url($config, 'contratar-servicio', ['plan' => $package['slug']]); ?>">Contratar</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="process-section process-section-alt">
    <div class="shell">
        <div class="section-heading section-heading-large">
            <h2>PROCESO DE CONTRATACIÓN</h2>
        </div>

        <div class="process-grid">
            <article class="process-card numbered-card">
                <div class="number-badge">1</div>
                <h3>Selecciona tu Paquete</h3>
                <p>Selecciona un paquete de internet de acuerdo a tus necesidades para continuar con el proceso de contratacion.</p>
            </article>
            <article class="process-card numbered-card">
                <div class="number-badge">2</div>
                <h3>Agenda tu Visita</h3>
                <p>Contacta a unos de nuestros agentes de servicio para indicar los datos y agendar una visita de nuestro tecnico.</p>
            </article>
            <article class="process-card numbered-card">
                <div class="number-badge">3</div>
                <h3>Disfruta tu Servicio</h3>
                <p>En un periodo máximo de 5 días hábiles contarás con nuestro servicio en el domicilio de contratación.</p>
            </article>
        </div>
    </div>
</section>
