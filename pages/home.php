<?php

declare(strict_types=1);
?>
<section class="shell intro-section">
    <div class="intro-copy">
        <h2>¡Bienvenido a WIZNET!</h2>
        <p>Hay mucho para ver aquí. Así que, tómate tu tiempo, mira alrededor y entérate de todo lo que hay para saber sobre nosotros. Esperamos que disfrutes nuestro sitio web y tómate un momento para tomar nota de nuestros servicios.</p>
        <a class="primary-button" href="<?= wiznet_url($config, 'soporte'); ?>">N° de Cliente</a>
    </div>
    <div class="intro-visual network-visual">
        <div class="network-core"></div>
        <div class="network-node node-a"></div>
        <div class="network-node node-b"></div>
        <div class="network-node node-c"></div>
        <div class="network-node node-d"></div>
        <div class="network-line line-a"></div>
        <div class="network-line line-b"></div>
        <div class="network-line line-c"></div>
        <div class="network-line line-d"></div>
    </div>
</section>

<section class="pattern-section surface-pattern package-showcase">
    <div class="shell package-showcase__shell">
        <div class="section-heading">
            <h2>Paquetes Internet Residencial</h2>
            <span></span>
        </div>
        <div class="package-grid package-grid--showcase">
            <?php foreach ($siteContent['featured_residential_packages'] as $package): ?>
                <article class="package-card package-card--showcase">
                    <div class="package-card__icon"><?= wiznet_icon('wifi'); ?></div>
                    <h3><?= wiznet_escape($package['name']); ?></h3>
                    <div class="divider"></div>
                    <p><?= wiznet_escape($package['description']); ?></p>
                    <p><?= wiznet_escape($package['details']); ?></p>
                    <strong class="package-card__note">*Consulte gastos de activacion de servicio y instalacion.</strong>
                    <div class="price-block">
                        <span class="package-card__price"><?= wiznet_escape($package['price']); ?></span>
                        <small class="package-card__price-label"><?= wiznet_escape($package['price_note']); ?></small>
                    </div>
                    <a class="button button--success" href="<?= wiznet_url($config, 'contratar-servicio', ['plan' => $package['slug']]); ?>">Contratar</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="pattern-section">
    <div class="shell">
        <div class="section-heading">
            <h2>Paquetes Internet Comercial</h2>
        </div>
        <div class="package-grid">
            <?php foreach ($siteContent['commercial_packages'] as $package): ?>
                <article class="package-card">
                    <div class="package-icon"><?= wiznet_icon('wifi'); ?></div>
                    <h3><?= wiznet_escape($package['name']); ?></h3>
                    <p><?= wiznet_escape($package['description']); ?></p>
                    <p><?= wiznet_escape($package['details']); ?></p>
                    <strong>*Consulte gastos de activacion de servicio y instalacion.</strong>
                    <div class="price-block">
                        <span class="price"><?= wiznet_escape($package['price']); ?></span>
                        <small><?= wiznet_escape($package['price_note']); ?></small>
                    </div>
                    <a class="success-button" href="<?= wiznet_url($config, 'contratar-servicio', ['plan' => $package['slug'], 'type' => 'comercial']); ?>">Contratar</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="shell process-section">
    <div class="section-heading">
        <h2>PROCESO DE CONTRATACIÓN</h2>
    </div>

    <div class="process-grid">
        <article class="process-card">
            <div class="process-icon"><?= wiznet_icon('globe'); ?></div>
            <h3>Selecciona tu Paquete</h3>
            <p>Selecciona un paquete de internet de acuerdo a tus necesidades para continuar con el proceso de contratacion.</p>
        </article>
        <article class="process-card">
            <div class="process-icon"><?= wiznet_icon('calendar'); ?></div>
            <h3>Agenda tu Visita</h3>
            <p>Contacta a unos de nuestros agentes de servicio para indicar los datos y agendar una visita de nuestro tecnico.</p>
        </article>
        <article class="process-card">
            <div class="process-icon"><?= wiznet_icon('smile'); ?></div>
            <h3>Disfruta tu Servicio</h3>
            <p>En un periodo máximo de 5 días hábiles contarás con nuestro servicio en el domicilio de contratación.</p>
        </article>
    </div>
</section>

<section class="faq-section">
    <div class="shell">
        <div class="section-heading">
            <h2>Preguntas Frecuentes</h2>
        </div>

        <div class="faq-list">
            <?php foreach ($siteContent['faqs'] as $faq): ?>
                <details>
                    <summary><?= wiznet_escape($faq); ?></summary>
                    <p>Comunícate con nuestro equipo de atención al cliente para recibir la respuesta exacta según tu caso y tu localidad.</p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
