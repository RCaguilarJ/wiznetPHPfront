<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$selectedPlan = sanitize_string($_GET['plan'] ?? '');
$result = form_result();
$result['old']['plan'] = $selectedPlan;

if (is_post() && ($_POST['form_type'] ?? '') === 'contact') {
    $result = process_contact_submission();
    if (($result['old']['plan'] ?? '') === '') {
        $result['old']['plan'] = $selectedPlan;
    }
}

$pageContext = [
    'page_title' => 'Contacto',
    'meta_description' => 'Contáctanos para atención, contratación y dudas sobre el servicio de Internet WIZNET en Jalisco.',
    'body_class' => 'page-inner',
];

require __DIR__ . '/includes/header.php';

render_page_header('Informacion de Contacto', 'contacto', 'page-hero--network page-hero--support');
?>

<section class="contact-map">
    <iframe
        src="https://www.google.com/maps?q=<?= urlencode($site['contact']['map_address']) ?>&output=embed"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Ubicacion de WIZNET"></iframe>
</section>

<section class="section contact-section">
    <div class="container">
        <div class="contact-card">
            <?php render_section_title('Informacion de Contacto'); ?>
            <div class="contact-card__meta">
                <p><?= render_icon('pin', 'icon--small') ?> <span><?= e($site['contact']['address']) ?></span></p>
                <p><?= render_icon('mail', 'icon--small') ?> <span><?= e($site['contact']['email']) ?></span></p>
                <p><?= render_icon('phone', 'icon--small') ?> <span><?= e($site['contact']['phone']) ?></span></p>
            </div>
            <?php if (($result['old']['plan'] ?? '') !== ''): ?>
                <div class="plan-chip">Interes en: <?= e($result['old']['plan']) ?></div>
            <?php endif; ?>
            <?php render_alert($result); ?>
            <form class="site-form" method="post" novalidate>
                <input type="hidden" name="form_type" value="contact">
                <input type="hidden" name="plan" value="<?= e($result['old']['plan'] ?? '') ?>">
                <div class="form-row">
                    <label>
                        <span>Nombre completo <strong>*</strong></span>
                        <input type="text" name="name" value="<?= e(old_value($result, 'name')) ?>">
                        <?php if (has_error($result, 'name')): ?><small class="field-error"><?= e(field_error($result, 'name')) ?></small><?php endif; ?>
                    </label>
                </div>
                <div class="form-row">
                    <label>
                        <span>Numero telefonico <strong>*</strong></span>
                        <input type="text" name="phone" value="<?= e(old_value($result, 'phone')) ?>">
                        <?php if (has_error($result, 'phone')): ?><small class="field-error"><?= e(field_error($result, 'phone')) ?></small><?php endif; ?>
                    </label>
                </div>
                <div class="form-row">
                    <label>
                        <span>Correo electronico <strong>*</strong></span>
                        <input type="email" name="email" value="<?= e(old_value($result, 'email')) ?>">
                        <?php if (has_error($result, 'email')): ?><small class="field-error"><?= e(field_error($result, 'email')) ?></small><?php endif; ?>
                    </label>
                </div>
                <div class="form-row">
                    <label>
                        <span>Escribe tus comentarios <strong>*</strong></span>
                        <textarea name="comments" rows="6"><?= e(old_value($result, 'comments')) ?></textarea>
                        <?php if (has_error($result, 'comments')): ?><small class="field-error"><?= e(field_error($result, 'comments')) ?></small><?php endif; ?>
                    </label>
                </div>
                <div class="form-actions">
                    <button class="button" type="submit">Enviar Mensaje</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
