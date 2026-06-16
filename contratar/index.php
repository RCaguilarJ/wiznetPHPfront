<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$pageContext = [
    'page_title' => 'Contratar Servicio',
    'meta_description' => 'Consulta información para contratar el servicio de Internet WIZNET y conoce el equipo incluido.',
    'body_class' => 'page-inner',
];

$contractColumns = [
    [
        'title' => 'EQUIPO INCLUIDO (MICROONDAS)',
        'equipment' => [
            'Antena exterior.',
            'Cableado de red.',
            'Conectores de red.',
            'Router inalambrico 2.4GHZ.',
        ],
        'sections' => [
            [
                'title' => 'COSTO POR ACTIVACION',
                'items' => [
                    '$2,350.00 IVA incluido',
                    'Equipos disponibles',
                    '¿Tiene dudas? Llamenos',
                ],
            ],
            [
                'title' => 'TERMINOS DE CONTRATACION',
                'items' => [
                    'Permanencia obligatoria de 0 meses a partir de la instalacion del servicio.',
                    'Paquete de internet a eleccion del cliente desde el momento de su instalacion.',
                    'Cambio de paquete permitido a partir del 2º mes de su instalacion en prepago libre.',
                    'Puede realizar el pago de su servicio con transferencia a nuestras cuentas bancarias.',
                    'Pago en efectivo al instalar su servicio.',
                    'Cualquier persona puede recibir al tecnico para la instalacion, no requiere firma de contrato.',
                    'Consulte paquetes y formas de pago mas abajo de esta informacion.',
                    'Suspension de servicio permitido hasta por 90 dias maximo, posteriormente se inactiva su cuenta.',
                    'Equipos son propiedad del cliente al instalar.',
                    'Instalacion de 1 a 5 dias habiles a partir de la recepcion de sus datos personales.',
                    'Facturamos instalacion y servicio de internet.',
                    'Pago con tarjeta de credito y/o debito.',
                    'Cargo adicional por reactivacion de servicio en caso de inactividad por mas de 90 dias.',
                ],
            ],
            [
                'title' => '*IMPORTANTE*',
                'items' => [
                    'Si realiza su contratacion con alguna promocion por medio de contrato debera seguir la permanencia obligatoria y cubrir el costo de activacion de acuerdo al paquete que se ofresca en el momento de la contratacion del servicio. (*servicio por microondas*)',
                ],
            ],
        ],
    ],
    [
        'title' => 'EQUIPO INCLUIDO (FIBRA OPTICA)',
        'equipment' => [
            'Cableado de fibra optica.',
            'Conectores de FTTH.',
            'Router inalambrico 2.4 y 5GHZ.',
            'Accesorios varios.',
        ],
        'sections' => [
            [
                'title' => 'COSTO POR ACTIVACION',
                'items' => [
                    '$2,350.00 IVA incluido',
                    'Equipos disponibles',
                    '¿Tiene dudas? Llamenos',
                ],
            ],
            [
                'title' => 'TERMINOS DE CONTRATACION',
                'items' => [
                    'Permanencia obligatoria de 0 meses a partir de la instalacion del servicio.',
                    'Paquete de internet a eleccion del cliente desde el momento de su instalacion.',
                    'Cambio de paquete permitido a partir del 2º mes de su instalacion en prepago libre.',
                    'Puede realizar el pago de su servicio con transferencia a nuestras cuentas bancarias.',
                    'Pago en efectivo al instalar su servicio.',
                    'Cualquier persona puede recibir al tecnico para la instalacion, no requiere firma de contrato.',
                    'Consulte paquetes y formas de pago mas abajo de esta informacion.',
                    'Suspension de servicio permitido hasta por 30 dias maximo, posteriormente se inactiva su cuenta.',
                    'Equipos son propiedad del cliente al instalar.',
                    'Instalacion de 1 a 5 dias habiles a partir de la recepcion de sus datos personales.',
                    'Facturamos instalacion y servicio de internet.',
                    'Pago con tarjeta de credito y/o debito.',
                    'Cargo adicional por reactivacion de servicio en caso de inactividad por mas de 30 dias.',
                ],
            ],
            [
                'title' => '*IMPORTANTE*',
                'items' => [
                    'Si realiza su contratacion con alguna promocion por medio de contrato debera seguir la permanencia obligatoria y cubrir el costo de activacion de acuerdo al paquete que se ofresca en el momento de la contratacion del servicio. (*servicio por fibra optica*)',
                ],
            ],
        ],
    ],
];

$contractNotes = [
    [
        'title' => 'NOTA',
        'items' => [
            'Los paquetes de Internet estan disponibles segun la forma de contrato para la cotizacion en contrato o sin contrato.',
        ],
    ],
    [
        'title' => 'IMPORTANTE',
        'items' => [
            'Favor de enviarnos su direccion completa o una ubicacion desde su movil para verificar cobertura en el domicilio solicitado.',
        ],
    ],
    [
        'title' => 'NOTA',
        'items' => [
            'Lunes a viernes de 9 am a 6 pm',
            'Sabado de 9 am a 2 pm',
            'Quedamos a sus ordenes para continuar con su tramite y resolver cualquier duda del servicio via telefonica.',
        ],
    ],
];

require __DIR__ . '/../includes/header.php';

render_page_header('CONTRATAR SERVICIO', 'Contratar', 'page-hero--network page-hero--support');
?>

<section class="section contract-page">
    <div class="container contract-page__shell">
        <div class="contract-page__badge">Servicios Disponibles</div>

        <div class="contract-page__columns">
            <?php foreach ($contractColumns as $column): ?>
                <article class="contract-card-stack">
                    <section class="contract-panel">
                        <div class="contract-panel__head"><?= e($column['title']) ?></div>
                        <div class="contract-panel__body">
                            <ul class="contract-list">
                                <?php foreach ($column['equipment'] as $item): ?>
                                    <li><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </section>

                    <?php foreach ($column['sections'] as $section): ?>
                        <section class="contract-panel">
                            <div class="contract-panel__head"><?= e($section['title']) ?></div>
                            <div class="contract-panel__body">
                                <ul class="contract-list">
                                    <?php foreach ($section['items'] as $item): ?>
                                        <li><?= e($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="contract-actions">
            <button class="button" type="button" data-contract-modal-open="packages">Consultar Paquetes</button>
            <button class="button" type="button" data-contract-modal-open="request">Contratar Servicio</button>
            <a class="button" href="https://api.whatsapp.com/send?phone=5213318333058&amp;text=Hola,%20Estoy%20interesado%20en%20sus%20servicios,%20pueden%20proporcionarme%20mas%20informaci%C3%B3n." target="_blank" rel="noopener noreferrer">Enviar Mensaje</a>
        </div>

        <div class="contract-notes">
            <?php foreach ($contractNotes as $note): ?>
                <article class="contract-note">
                    <h3><?= e($note['title']) ?></h3>
                    <?php foreach ($note['items'] as $item): ?>
                        <p><?= e($item) ?></p>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="contract-modal" data-contract-modal="packages" aria-hidden="true">
        <div class="contract-modal__backdrop" data-contract-modal-close></div>
        <div class="contract-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="contract-modal-title">
            <div class="contract-modal__header">
                <h2 id="contract-modal-title">Consultar Paquetes</h2>
                <button class="contract-modal__close" type="button" aria-label="Cerrar ventana" data-contract-modal-close>
                    <span></span>
                    <span></span>
                </button>
            </div>
            <div class="contract-modal__body">
                <img src="<?= e(asset_url('img/WIZNET-PAQUETES-V3popO.jpg')) ?>" alt="Paquetes vigentes de internet WIZNET" class="contract-modal__image">
            </div>
        </div>
    </div>

    <div class="contract-modal" data-contract-modal="request" aria-hidden="true">
        <div class="contract-modal__backdrop" data-contract-modal-close></div>
        <div class="contract-modal__dialog contract-modal__dialog--form" role="dialog" aria-modal="true" aria-labelledby="contract-request-title">
            <div class="contract-modal__header">
                <h2 id="contract-request-title">Contratar Paquetes</h2>
                <button class="contract-modal__close" type="button" aria-label="Cerrar ventana" data-contract-modal-close>
                    <span></span>
                    <span></span>
                </button>
            </div>
            <div class="contract-modal__body contract-modal__body--form">
                <div class="contract-modal__copy">
                    <p>Si alguno de los datos solicitados anteriormente no es completado la solicitud no puede ser procesada. Una vez recibida esta informacion correctamente se agenda la visita de 1 a 5 dias habiles de parte del area de instalaciones.</p>
                    <p>Quedamos a sus ordenes para continuar con su tramite y resolver cualquier duda del servicio via telefonica.</p>
                </div>

                <form class="site-form contract-request-form" novalidate>
                    <div class="form-row">
                        <label>
                            <span>Nombre completo del solicitante</span>
                            <input type="text" name="request_name">
                        </label>
                    </div>

                    <div class="form-row">
                        <label>
                            <span>Domicilio completo a visitar (Calle y Numero)</span>
                            <input type="text" name="request_address">
                        </label>
                    </div>

                    <div class="form-grid contract-request-form__grid">
                        <label>
                            <span>Colonia / Delegacion</span>
                            <input type="text" name="request_colony">
                        </label>

                        <label>
                            <span>Municipio</span>
                            <input type="text" name="request_city">
                        </label>
                    </div>

                    <div class="form-row">
                        <label>
                            <span>Estado</span>
                            <input type="text" name="request_state">
                        </label>
                    </div>

                    <div class="form-row">
                        <label>
                            <span>Entre calles del domicilio</span>
                            <input type="text" name="request_cross_streets">
                        </label>
                    </div>

                    <div class="form-row">
                        <label>
                            <span>Telefonos de contacto</span>
                            <input type="text" name="request_phone">
                        </label>
                    </div>

                    <div class="form-row">
                        <label>
                            <span>Referencia (opcional)</span>
                            <input type="text" name="request_reference">
                        </label>
                    </div>

                    <div class="form-actions contract-request-form__actions">
                        <button class="button" type="button">Enviar Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
