<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$pageContext = [
    'page_title' => 'Pago en Linea',
    'meta_description' => 'Realiza el pago en línea de tu servicio WIZNET y consulta las condiciones de registro de pagos.',
    'body_class' => 'page-inner',
];

$introPoints = [
    'Ahora puede realizar el pago de su servicio de internet con cargo a tarjeta de credito y debito.',
    'Al realizar su pago se registrara su tarjeta en nuestro sistema de cargos recurrentes con fecha 1º de cada mes.',
];

$paymentNotes = [
    'Los pagos realizados por este medio deberan ser registrados por whatsapp o via web para su aplicacion final.',
    'Los pagos anticipados con cargo a tarjeta de credito y debito no participan para las promociones de meses gratis.',
    'El seguimiento a la aplicacion de sus pagos recurrentes es responsabilidad del cliente mientras permanezca la recurrencia.',
];

require __DIR__ . '/../includes/header.php';

render_page_header('PAGO EN LINEA', 'pagos en linea', 'page-hero--network page-hero--support');
?>

<section class="section payment-web-page">
    <div class="container payment-web-page__shell">
        <?php render_section_title('Pago en Linea'); ?>

        <ul class="payment-web-list payment-web-list--intro">
            <?php foreach ($introPoints as $point): ?>
                <li>
                    <?= render_icon('check') ?>
                    <span><?= e($point) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="payment-web-divider" aria-hidden="true"></div>

        <div class="payment-web-brands" aria-label="Tarjetas aceptadas">
            <img src="<?= e(asset_url('img/logo-visa.png')) ?>" alt="Visa y Mastercard" class="payment-web-brands__image">
        </div>

        <div class="payment-web-divider" aria-hidden="true"></div>

        <div class="payment-web-cta">
            <a class="button button--success payment-web-button" href="<?= e(page_url('registro-pagos.php')) ?>">
                <?= render_icon('clipboard') ?>
                <span>Pagar servicios (WIZNET)</span>
            </a>
        </div>

        <ul class="payment-web-list">
            <?php foreach ($paymentNotes as $point): ?>
                <li>
                    <?= render_icon('check') ?>
                    <span><?= e($point) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
