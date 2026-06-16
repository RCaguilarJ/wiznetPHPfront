<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$result = form_result();
if (is_post() && ($_POST['form_type'] ?? '') === 'payment') {
    $result = process_payment_submission($site);
}

$pageContext = [
    'page_title' => 'Registro de Pagos',
    'meta_description' => 'Registra tu pago de servicio WIZNET y envía tu comprobante de forma rápida y segura.',
    'body_class' => 'page-inner',
];

require __DIR__ . '/includes/header.php';

render_page_header('REGISTRO DE PAGOS', 'Registro de Pagos', 'page-hero--network page-hero--support');
?>

<section class="section form-hero-section payment-form-section">
    <div class="container">
        <?php render_section_title('REGISTRO DE PAGOS'); ?>
        <p class="section-subtitle">Por favor completa el siguiente formulario</p>
        <?php render_alert($result); ?>

        <div class="form-shell form-shell--laptop">
            <form class="site-form site-form--wide" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="form_type" value="payment">

                <div class="form-row">
                    <label>
                        <span>Ingresa tu Nombre <strong>*</strong></span>
                        <input type="text" name="name" value="<?= e(old_value($result, 'name')) ?>">
                        <?php if (has_error($result, 'name')): ?><small class="field-error"><?= e(field_error($result, 'name')) ?></small><?php endif; ?>
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        <span>Selecciona tu oficina <strong>*</strong></span>
                        <select name="office">
                            <option value="">Selecciona tu oficina</option>
                            <?php foreach ($site['offices'] as $office): ?>
                                <option value="<?= e($office) ?>" <?= old_value($result, 'office') === $office ? 'selected' : '' ?>><?= e($office) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (has_error($result, 'office')): ?><small class="field-error"><?= e(field_error($result, 'office')) ?></small><?php endif; ?>
                    </label>
                </div>

                <div class="form-grid">
                    <label>
                        <span>Cliente <strong>*</strong></span>
                        <input type="text" name="client_number" value="<?= e(old_value($result, 'client_number')) ?>" placeholder="Ingresa tu numero de Cliente">
                        <?php if (has_error($result, 'client_number')): ?><small class="field-error"><?= e(field_error($result, 'client_number')) ?></small><?php endif; ?>
                    </label>

                    <label>
                        <span>Correo electronico <strong>*</strong></span>
                        <input type="email" name="email" value="<?= e(old_value($result, 'email')) ?>">
                        <?php if (has_error($result, 'email')): ?><small class="field-error"><?= e(field_error($result, 'email')) ?></small><?php endif; ?>
                    </label>
                </div>

                <div class="form-grid form-grid--upload">
                    <label class="upload-field">
                        <span>Subir Archivos <strong>*</strong></span>
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                        <div class="upload-dropzone">
                            <?= render_icon('upload') ?>
                            <strong>Suelta un archivo aqui o haz clic para subir</strong>
                            <span>Maximum file size: 20MB</span>
                        </div>
                        <?php if (has_error($result, 'attachment')): ?><small class="field-error"><?= e(field_error($result, 'attachment')) ?></small><?php endif; ?>
                    </label>

                    <div class="form-illustration" aria-hidden="true"></div>
                </div>

                <div class="form-row">
                    <label>
                        <span>Telefono <strong>*</strong></span>
                        <input type="text" name="phone" value="<?= e(old_value($result, 'phone')) ?>" placeholder="Ingresa tu numero telefonico">
                        <?php if (has_error($result, 'phone')): ?><small class="field-error"><?= e(field_error($result, 'phone')) ?></small><?php endif; ?>
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        <span>Comentarios</span>
                        <textarea name="comments" rows="6"><?= e(old_value($result, 'comments')) ?></textarea>
                    </label>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Enviar Registro</button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="section process-section">
    <div class="container process-shell">
        <?php render_section_title('PROCESO DE CONTRATACION'); ?>
        <div class="process-grid">
            <?php
            $paymentSteps = [
                ['icon' => 'globe', 'title' => 'Selecciona tu Paquete', 'copy' => $site['steps'][0]['copy'] ?? ''],
                ['icon' => 'calendar-check', 'title' => 'Agenda tu Visita', 'copy' => $site['steps'][1]['copy'] ?? ''],
                ['icon' => 'smile', 'title' => 'Disfruta tu Servicio', 'copy' => $site['steps'][2]['copy'] ?? ''],
            ];
            ?>
            <?php foreach ($paymentSteps as $step): ?>
                <article class="process-card">
                    <div class="process-card__icon">
                        <?= render_icon($step['icon']) ?>
                    </div>
                    <span class="section-line"></span>
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['copy']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
