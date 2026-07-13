<?php

declare(strict_types=1);

return [
    'brand' => [
        'name' => 'WIZNET',
        'tagline' => 'Internet al hogar',
    ],
    'contact' => [
        'phone' => '33 37 35 20 50',
        'email' => 'contacto@wiznet.mx',
        'address' => '16 de Septiembre #117 Loc. Corralillos Mpio. Zapotlanejo Jalisco.',
        'map_address' => '16 de Septiembre 117, Corralillos, Zapotlanejo, Jalisco, Mexico',
    ],
    'navigation' => [
        ['label' => 'Inicio', 'url' => 'index.php', 'key' => 'home'],
        [
            'label' => 'Paquetes',
            'url' => 'index.php#paquetes',
            'key' => 'packages',
            'children' => [
                ['label' => 'Internet Residencial', 'url' => 'internet-residencial.php'],
                ['label' => 'Paquetes Comercial', 'url' => 'internet-comercial.php'],
            ],
        ],
        ['label' => 'Cobertura', 'url' => 'cobertura.php', 'key' => 'coverage'],
        ['label' => 'Soporte', 'url' => 'soporte.php', 'key' => 'support'],
        ['label' => 'Registro de Pagos', 'url' => 'registro-pagos.php', 'key' => 'payments'],
    ],
    'hero' => [
        'home_title' => 'Servicio de Internet vía Antena y Fibra Óptica',
        'welcome_title' => '¡Bienvenido a WIZNET!',
        'welcome_copy' => 'Hay mucho para ver aquí. Así que, tómate tu tiempo, mira alrededor y entérate de todo lo que hay para saber sobre nosotros. Esperamos que disfrutes nuestro sitio web y tómate un momento para tomar nota de nuestros servicios.',
    ],
    'cta' => [
        'client_zone_label' => 'N° DE CLIENTE',
        'client_zone_url' => 'registro-pagos.php',
    ],
    'packages' => [
        'residential' => [
            'title' => 'Paquetes Internet Residencial',
            'items' => [
                [
                    'slug' => 'paq-basico-residencial',
                    'name' => 'PAQ. BASICO RESIDENCIAL',
                    'summary' => 'Paquete ideal para consumos bajos que requieren mensajeria y tareas de baja demanda.',
                    'details' => 'Este paquete le ofrece una navegacion ilimitada con 5MB de Descarga x 2MB de Carga durante 1 mes.',
                    'note' => '*Consulte gastos de activacion de servicio y instalacion.',
                    'price' => '$300',
                ],
                [
                    'slug' => 'paq-medio-residencial',
                    'name' => 'PAQ. MEDIO RESIDENCIAL',
                    'summary' => 'Paquete ideal para consumos medios que requieren mensajeria y entrenamiento.',
                    'details' => 'Este paquete le ofrece una navegacion ilimitada con 7MB de Descarga x 3MB de Carga durante 1 mes.',
                    'note' => '*Consulte gastos de activacion de servicio y instalacion.',
                    'price' => '$400',
                ],
                [
                    'slug' => 'paq-alto-residencial',
                    'name' => 'PAQ. ALTO RESIDENCIAL',
                    'summary' => 'Paquete ideal para consumos altos que requieren mensajeria, descargas y entretenimiento.',
                    'details' => 'Este paquete le ofrece una navegacion ilimitada con 10MB de Descarga x 5MB de Carga durante 1 mes.',
                    'note' => '*Consulte gastos de activacion de servicio y instalacion.',
                    'price' => '$550',
                ],
            ],
        ],
        'commercial' => [
            'title' => 'Paquetes Internet Comercial',
            'items' => [
                [
                    'slug' => 'paq-basico-comercial',
                    'name' => 'PAQ. BASICO COMERCIAL',
                    'summary' => 'Paquete ideal para consumos bajos que requieren mensajeria y tareas de baja demanda.',
                    'details' => 'Este paquete le ofrece una navegacion ilimitada con 15MB de Descarga x 15MB de Carga durante 1 mes.',
                    'note' => '*Consulte gastos de activacion de servicio y instalacion.',
                    'price' => '$1,500',
                ],
                [
                    'slug' => 'paq-medio-comercial',
                    'name' => 'PAQ. MEDIO COMERCIAL',
                    'summary' => 'Paquete ideal para consumos medios que requieren mensajeria y entretenimiento.',
                    'details' => 'Este paquete le ofrece una navegacion ilimitada con 30MB de Descarga x 30MB de Carga durante 1 mes.',
                    'note' => '*Consulte gastos de activacion de servicio y instalacion.',
                    'price' => '$2,500',
                ],
                [
                    'slug' => 'paq-alto-comercial',
                    'name' => 'PAQ. ALTO COMERCIAL',
                    'summary' => 'Paquete ideal para consumos altos que requieren mensajeria, descargas y entretenimiento.',
                    'details' => 'Este paquete le ofrece una navegacion ilimitada con 50MB de Descarga x 50MB de Carga durante 1 mes.',
                    'note' => '*Consulte gastos de activacion de servicio y instalacion.',
                    'price' => '$3,500',
                ],
            ],
        ],
    ],
    'steps' => [
        [
            'step' => '1',
            'icon' => 'wifi',
            'title' => 'Selecciona tu Paquete',
            'copy' => 'Selecciona un paquete de internet de acuerdo a tus necesidades para continuar con el proceso de contratacion.',
        ],
        [
            'step' => '2',
            'icon' => 'calendar-pin',
            'title' => 'Agenda tu Visita',
            'copy' => 'Contacta a uno de nuestros agentes de servicio para indicar los datos y agendar una visita de nuestro tecnico.',
        ],
        [
            'step' => '3',
            'icon' => 'wifi-heart',
            'title' => 'Disfruta tu Servicio',
            'copy' => 'En un periodo maximo de 5 dias habiles contaras con nuestro servicio en el domicilio de contratacion.',
        ],
    ],
    'faq' => [
        [
            'question' => 'Consultar su numero de cliente?',
            'answer' => [
                'Su numero de cliente corresponde a los numeros finales que aparecen al final de el nombre de su red wi-fi tal y como se deja configurado al momento de la instalacion, si no cuenta con su numero de cliente o tiene alguna duda llame a servicio al cliente.',
                'Si ya no cuenta con los ajustes de configuracion inicial es necesario consultarlo con atencion al cliente para recibir el soporte necesario.',
                'Si requiere ayuda contacte a servicio al cliente aqui.',
            ],
        ],
        [
            'question' => 'Donde puedo pagar su servicio?',
            'answer' => [
                'Sus pagos pueden ser realizados mediante cuentas bancarias o medios locales autorizados disponibles en algunas zonas donde contamos con cobertura de servicio.',
                'Cualquier informacion adicional contacte a servicio al cliente donde con gusto le resolveremos sus dudas y le indicaremos donde puede realizar sus pagos en efectivo.',
            ],
        ],
        [
            'question' => 'Puedo cambiar de paquete?',
            'answer' => [
                'Si, el paquete puede ser cambiado cuando el cliente lo decida sin penalizacion alguna siempre y cuando se programe con la fecha corte de servicio al dia 1o de cada mes y eligiendo un paquete del mismo sector segun la disponibilidad de la zona donde contrato su instalacion de servicio.',
                ['text' => '(no aplica para clientes en comodato dentro del periodo de 2 meses iniciales y no se puede cambiar de paquete despues del dia 1 del mes corriente).', 'strong' => true],
                'Si requiere mas informacion contacte a servicio al cliente aqui.',
            ],
        ],
        [
            'question' => 'Puedo suspender o cancelar mi servicio?',
            'answer' => [
                'Si, puede suspender su servicio por hasta 90 dias sin ningun cargo adicional de reactivacion, posterior a este lapso de tiempo se le cobrara la reactivacion de servicio.',
                ['text' => '(no aplica para clientes con contrato dentro del periodo de 12 meses iniciales)', 'strong' => true],
                'En el caso de la cancelacion de servicio esta debera ser notificada antes del dia 1o de cada mes y se le solicitara la entrega de los equipos en el transcurso de los prox. 5 dias habiles a la solicitud de baja del servicio.',
            ],
        ],
        [
            'question' => 'Puedo restablecer los ajustes de mis equipos?',
            'answer' => [
                'La restauracion de configuraciones no es recomendable en su caso ya que al aplicar este restablecimiento deja fuera de soporte a su equipo y esto le generaria gastos adicionales al momento de requerir cualquier asistencia en su servicio.',
                'Si requiere mas informacion contacte a servicio al cliente aqui.',
            ],
        ],
        [
            'question' => 'Si requiero soporte o una visita tiene algun costo?',
            'answer' => [
                'La visita de soporte por falla de servicio le generan costo, aplican gastos adicionales a las visitas que requieran de materiales adicionales necesarios para realizar la reparacion de su servicio, asi como reconfiguracion de equipos para su correcto funcionamiento.',
                'No aplica gastos por materiales adicionales si su servicio cuenta con contrato de servicio vigente, solo pagaria la visita del tecnico al domicilio.',
                'Si requiere mas informacion contacte a servicio al cliente aqui.',
            ],
        ],
    ],
    'coverage' => [
        [
            'zone' => 'Zapotlanejo',
            'locations' => ['La Laja', 'La Mezquitera', 'La Mora', 'Santa Maria (tacuache)', 'La Yerbabuena', 'El Canuto', 'Agua Escondida', 'Cuchillas', 'La Paz', 'Corralillos', 'El Saucillo', 'Buenos Aires', 'El Cerrito', 'El Salto (coyotes)'],
        ],
        [
            'zone' => 'Juanacatlan',
            'locations' => ['San Isidro', 'Miraflores', 'Exhacienda', 'Juanacatlan', 'San Antonio', 'Casa de Teja', 'Villas Andalucia', 'Estancia de Guadalupe'],
        ],
        [
            'zone' => 'El Salto',
            'locations' => ['El Muey', 'El Salto', 'Las Lilas', 'El Pedregal', 'La Azucena', 'Sima Serena', 'Los Laureles', 'Potrero Nuevo'],
        ],
        [
            'zone' => 'Tonala',
            'locations' => ['Tololotan', 'El Calaboso', 'Agua Blanca', 'Puente Grande'],
        ],
    ],
    'support_tips' => [
        [
            'icon' => 'refresh',
            'title' => 'Tienes Fallas en tu servicio?',
            'copy' => 'Por favor desconecte sus equipos de la electricidad por un par de minutos y vuelva a conectarlos para refrescar su conexion a Internet.',
        ],
        [
            'icon' => 'plug',
            'title' => 'Tienes alguna luz LED apagada en alguno de tus equipos?',
            'copy' => 'Por favor revisa que tus equipos se encuentren conectados a la energia o que la terminal cuente con el suministro de energia.',
        ],
        [
            'icon' => 'network',
            'title' => 'Tienes alguna duda de como van los cables de red?',
            'copy' => 'Si realizaste algun cambio de lugar o desconectaste tus equipos de red y no sabes como conectarlos de nuevo por favor comunicate con nosotros para ayudarte.',
        ],
        [
            'icon' => 'headset',
            'title' => 'Ya no encienden las luces LED de mi equipo(s)?',
            'copy' => 'Si ya realizaste las pruebas de los pasos 1,2 y 3 pero no logras tener servicio por favor comunicate con soporte para brindarte el apoyo necesario.',
        ],
    ],
    'offices' => [
        'OFICINA CORRALILLOS',
    ],
    'footer_links' => [
        'services' => [
            ['label' => 'Internet Residencial', 'url' => 'internet-residencial.php'],
            ['label' => 'Contratar Servicio', 'url' => 'contratar/'],
            ['label' => 'Realizar Pago en Linea', 'url' => 'pago-web/'],
        ],
        'interest' => [
            ['label' => 'Registro Tarifas IFT', 'url' => 'https://tarifas.ift.org.mx/ift_visor/'],
            ['label' => 'Aviso de Privacidad', 'url' => 'politica-privacidad/'],
            ['label' => 'Carta de Derechos Minimos', 'url' => 'https://wiznet.mx/wp-content/uploads/2025/08/CARTA-DERECHOS-MINIMOS-112023.pdf'],
            ['label' => 'Registro de Concesion', 'url' => 'https://wiznet.mx/wp-content/uploads/2025/08/Titulo-de-concesion-MBB.pdf'],
            ['label' => 'Registro de Profeco', 'url' => 'https://wiznet.mx/wp-content/uploads/2025/08/Contrato-profeco-wiznet.pdf'],
            ['label' => 'Politica de Gestion de Trafico', 'url' => 'https://wiznet.mx/wp-content/uploads/2025/08/Codigo-de-Politica-de-Gestion-de-Trafico-y-Administracion-de-Red.pdf'],
        ],
    ],
];
