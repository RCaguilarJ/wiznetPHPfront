<?php

declare(strict_types=1);
?>
<section class="contact-map-section">
    <iframe src="<?= wiznet_escape($siteContent['site']['map_embed']); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mapa WIZNET"></iframe>
</section>

<section class="shell contact-card-shell">
    <div class="contact-card">
        <div class="section-heading section-heading-large">
            <h2>Información de Contacto</h2>
        </div>

        <div class="contact-meta">
            <span><?= wiznet_icon('pin'); ?><?= wiznet_escape($siteContent['site']['address']); ?></span>
            <span><?= wiznet_icon('mail'); ?><?= wiznet_escape($siteContent['site']['email']); ?></span>
            <span><?= wiznet_icon('phone'); ?><?= wiznet_escape($siteContent['site']['phone']); ?></span>
        </div>

        <form class="site-form" method="post" action="<?= wiznet_url($config, 'contacto'); ?>">
            <input type="hidden" name="csrf_token" value="<?= wiznet_escape(wiznet_csrf_token()); ?>">
            <input type="hidden" name="form_type" value="contact">
            <input type="hidden" name="page_slug" value="contacto">

            <div class="form-grid single-column">
                <label>
                    <span>Nombre completo *</span>
                    <input type="text" name="name" required>
                </label>

                <label>
                    <span>Número telefónico *</span>
                    <input type="tel" name="phone" required>
                </label>

                <label>
                    <span>Correo electrónico *</span>
                    <input type="email" name="email" required>
                </label>

                <label>
                    <span>Escribe tus comentarios *</span>
                    <textarea name="message" rows="7" required></textarea>
                </label>
            </div>

            <button class="primary-button submit-button" type="submit">Enviar Mensaje</button>
        </form>
    </div>
</section>
