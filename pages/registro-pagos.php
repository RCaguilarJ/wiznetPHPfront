<?php

declare(strict_types=1);
?>
<section class="form-hero-section form-hero-section-payment">
    <div class="shell">
        <div class="section-heading section-heading-large">
            <h2>REGISTRO DE PAGOS</h2>
            <p>Por favor completa el siguiente formulario</p>
        </div>

        <div class="form-surface form-surface-visual">
            <form class="site-form" method="post" action="<?= wiznet_url($config, 'registro-pagos'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= wiznet_escape(wiznet_csrf_token()); ?>">
                <input type="hidden" name="form_type" value="payment">
                <input type="hidden" name="page_slug" value="registro-pagos">

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
                        <span>Subir Archivos *</span>
                        <div class="file-dropzone">
                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.webp" data-file-input required>
                            <strong data-file-name>Suelta un archivo aquí o haz clic para subir</strong>
                            <small>Maximum file size: 20MB</small>
                        </div>
                    </label>

                    <label class="full-span">
                        <span>Teléfono *</span>
                        <input type="tel" name="phone" placeholder="Ingresa tu numero telefónico" required>
                    </label>

                    <label class="full-span">
                        <span>Referencia de pago *</span>
                        <input type="text" name="reference" placeholder="Ej. SPEI-123456 o folio del deposito" required>
                    </label>

                    <label class="full-span">
                        <span>Comentarios</span>
                        <textarea name="message" rows="7" placeholder="Comparte monto, fecha y observaciones del pago"></textarea>
                    </label>
                </div>

                <button class="primary-button submit-button" type="submit">Enviar Registro</button>
            </form>
        </div>
    </div>
</section>
