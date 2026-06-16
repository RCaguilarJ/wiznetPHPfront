<?php

declare(strict_types=1);
?>
<section class="shell support-tips-section">
    <div class="support-tip-grid">
        <?php foreach ($siteContent['support_tips'] as $tip): ?>
            <article class="support-tip-card">
                <div class="support-tip-icon"><?= wiznet_icon($tip['icon']); ?></div>
                <h3><?= wiznet_escape($tip['title']); ?></h3>
                <p><?= wiznet_escape($tip['message']); ?></p>
                <button class="secondary-button" type="button" data-issue-trigger="<?= wiznet_escape($tip['title']); ?>">Usar este motivo</button>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="form-hero-section" id="support-form">
    <div class="shell">
        <div class="section-heading section-heading-large">
            <h2>ENVÍENOS UN MENSAJE PARA RECIBIR ASISTENCIA</h2>
            <p>Por favor completa el siguiente formulario</p>
        </div>

        <div class="form-surface form-surface-visual">
            <form class="site-form" method="post" action="<?= wiznet_url($config, 'soporte'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= wiznet_escape(wiznet_csrf_token()); ?>">
                <input type="hidden" name="form_type" value="support">
                <input type="hidden" name="page_slug" value="soporte">

                <div class="form-grid two-columns">
                    <label class="full-span">
                        <span>Ingresa tu Nombre *</span>
                        <input type="text" name="name" placeholder="Escribe tu nombre completo" required>
                    </label>

                    <label class="full-span">
                        <span>Selecciona tu oficina *</span>
                        <select name="office" required>
                            <option value="">Selecciona tu oficina</option>
                            <?php foreach ($siteContent['offices'] as $office): ?>
                                <option value="<?= wiznet_escape($office); ?>"><?= wiznet_escape($office); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        <span>Cliente *</span>
                        <input type="text" name="client_number" placeholder="Ingresa tu numero de Cliente" required>
                    </label>

                    <label>
                        <span>Correo electrónico *</span>
                        <input type="email" name="email" required>
                    </label>

                    <label class="full-span">
                        <span>Asunto detectado</span>
                        <input type="text" name="issue_hint" data-issue-field placeholder="Selecciona un motivo o escribe uno manualmente">
                    </label>

                    <label class="full-span">
                        <span>Subir Archivos</span>
                        <div class="file-dropzone">
                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.webp" data-file-input>
                            <strong data-file-name>Suelta un archivo aquí o haz clic para subir</strong>
                            <small>Maximum file size: 20MB</small>
                        </div>
                    </label>

                    <label class="full-span">
                        <span>Teléfono *</span>
                        <input type="tel" name="phone" placeholder="Ingresa tu numero telefónico" required>
                    </label>

                    <label class="full-span">
                        <span>Comentarios *</span>
                        <textarea name="message" rows="7" placeholder="Describe el problema, lo que ya revisaste y cualquier detalle de apoyo" required></textarea>
                    </label>
                </div>

                <button class="primary-button submit-button" type="submit">Enviar Registro</button>
            </form>
        </div>
    </div>
</section>
