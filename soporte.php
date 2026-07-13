<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['support_csrf'])) {
    $_SESSION['support_csrf'] = bin2hex(random_bytes(32));
}

$result = form_result();
if (is_post() && ($_POST['form_type'] ?? '') === 'support') {
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');
    $storedCsrfToken = $_SESSION['support_csrf'] ?? '';
    $honeypot = trim((string) ($_POST['website'] ?? ''));

    if (!is_string($storedCsrfToken) || $storedCsrfToken === '' || !hash_equals($storedCsrfToken, $csrfToken) || $honeypot !== '') {
        http_response_code(400);
        $result['message'] = 'No fue posible procesar la solicitud.';
    } elseif (!support_rate_limit_allows((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'))) {
        http_response_code(429);
        $result['message'] = 'Demasiados intentos. Intenta nuevamente más tarde.';
    } else {
        $result = process_support_submission($site);
    }
}

$pageContext = [
    'page_title' => 'Soporte',
    'meta_description' => 'Solicita soporte técnico de WIZNET para tu servicio de Internet y recibe asistencia personalizada.',
    'body_class' => 'page-inner',
];

require __DIR__ . '/includes/header.php';

render_page_header('Soporte', 'Soporte', 'page-hero--network page-hero--support');
?>

<section class="section">
    <div class="container support-grid">
        <?php foreach ($site['support_tips'] as $tip): ?>
            <article class="support-card">
                <div class="support-card__icon">
                    <?= render_icon($tip['icon']) ?>
                </div>
                <span class="section-line"></span>
                <h3><?= e($tip['title']) ?></h3>
                <p><?= e($tip['copy']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section form-hero-section support-form-section">
    <div class="container">
        <?php render_section_title('ENVIENOS UN MENSAJE PARA RECIBIR ASISTENCIA'); ?>
        <p class="section-subtitle">Por favor completa el siguiente formulario</p>
        <?php render_alert($result); ?>

        <div class="form-shell form-shell--laptop">
            <form class="site-form site-form--wide" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="form_type" value="support">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['support_csrf']) ?>">
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" hidden>

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
                        <span>Subir Archivos</span>
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.webp">
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

<?php require __DIR__ . '/includes/footer.php'; ?>
