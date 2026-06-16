<?php

declare(strict_types=1);

$selectedPlan = (string) ($_GET['plan'] ?? '');
$selectedType = (string) ($_GET['type'] ?? 'residencial');
?>
<section class="shell form-page-section">
    <div class="section-heading section-heading-large">
        <h2>Contrata tu Servicio</h2>
        <p>Completa el formulario para programar la instalación de tu nuevo servicio.</p>
    </div>

    <div class="form-surface">
        <form class="site-form" method="post" action="<?= wiznet_url($config, 'contratar-servicio'); ?>">
            <input type="hidden" name="csrf_token" value="<?= wiznet_escape(wiznet_csrf_token()); ?>">
            <input type="hidden" name="form_type" value="service-request">
            <input type="hidden" name="page_slug" value="contratar-servicio">

            <div class="form-grid two-columns">
                <label>
                    <span>Nombre completo *</span>
                    <input type="text" name="name" placeholder="Escribe tu nombre completo" required>
                </label>

                <label>
                    <span>Correo electrónico *</span>
                    <input type="email" name="email" placeholder="nombre@correo.com" required>
                </label>

                <label>
                    <span>Teléfono *</span>
                    <input type="tel" name="phone" placeholder="Ingresa tu numero telefónico" required>
                </label>

                <label>
                    <span>Selecciona tu oficina *</span>
                    <select name="office" required>
                        <option value="">Selecciona una oficina</option>
                        <?php foreach ($siteContent['offices'] as $office): ?>
                            <option value="<?= wiznet_escape($office); ?>"><?= wiznet_escape($office); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Número de cliente (si ya existe)</span>
                    <input type="text" name="client_number" placeholder="Opcional">
                </label>

                <label>
                    <span>Tipo de servicio *</span>
                    <select name="service_type" required>
                        <option value="Residencial" <?= $selectedType !== 'comercial' ? 'selected' : ''; ?>>Residencial</option>
                        <option value="Comercial" <?= $selectedType === 'comercial' ? 'selected' : ''; ?>>Comercial</option>
                    </select>
                </label>

                <label class="full-span">
                    <span>Dirección de instalación *</span>
                    <input type="text" name="address" placeholder="Escribe la direccion completa del servicio" required>
                </label>

                <label>
                    <span>Plan de interés *</span>
                    <input type="text" name="plan_name" value="<?= wiznet_escape($selectedPlan); ?>" data-plan-field placeholder="Ej. paq-basico-residencial" required>
                </label>

                <label>
                    <span>Horario preferido de visita</span>
                    <input type="text" name="preferred_visit" placeholder="Ej. 4:00 PM a 7:00 PM">
                </label>

                <label class="full-span">
                    <span>Comentarios adicionales</span>
                    <textarea name="message" rows="5" placeholder="Comparte referencias del domicilio, puntos de acceso o dudas adicionales"></textarea>
                </label>
            </div>

            <button class="primary-button submit-button" type="submit">Enviar Solicitud</button>
        </form>
    </div>
</section>
