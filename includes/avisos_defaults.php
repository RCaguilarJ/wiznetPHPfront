<?php

declare(strict_types=1);

function wiznet_default_avisos(): array
{
    return [
        'advertencia' => [
            ['id' => 0, 'titulo' => 'Pago fuera de horario', 'tipo' => 'advertencia', 'contenido' => 'Si realiza el pago de su servicio fuera del horario de labores, en dias inhabiles y festivos contara como pago tardio.'],
            ['id' => 0, 'titulo' => 'Factura en fecha de corte', 'tipo' => 'advertencia', 'contenido' => 'Si requiere factura de su servicio y su pago se realiza en dias inhabiles o festivos en la fecha de corte no sera facturado.'],
            ['id' => 0, 'titulo' => 'Evite pago tardio', 'tipo' => 'advertencia', 'contenido' => 'Se recomienda realizar su pago con dias de anticipacion para evitar el cobro de $30 extra por pago tardio.'],
            ['id' => 0, 'titulo' => 'Conserve sus comprobantes', 'tipo' => 'advertencia', 'contenido' => 'No utilice mensajes temporales y conserve sus comprobantes de pago ya que la verificacion de los pagos puede tardar hasta 10 dias habiles.'],
            ['id' => 0, 'titulo' => 'WhatsApp sin soporte tecnico', 'tipo' => 'advertencia', 'contenido' => 'Le recordamos que el numero de WhatsApp no atiende reportes de fallas, para soporte tecnico llame a servicio al cliente al numero 3337352050.'],
            ['id' => 0, 'titulo' => 'Verifique medios de pago', 'tipo' => 'advertencia', 'contenido' => 'IMPORTANTE!! No realice pagos de servicio sin antes verificar la informacion de los medios de pago vigentes.'],
        ],
        'recomendacion' => [
            ['id' => 0, 'titulo' => 'Revise medios de pago', 'tipo' => 'recomendacion', 'contenido' => 'Seguir las indicaciones que se proporcionan al utilizar los medios de pago disponibles, revisar frecuentemente.'],
            ['id' => 0, 'titulo' => 'Prepago con visita con costo', 'tipo' => 'recomendacion', 'contenido' => 'Se le recuerda que a todo servicio en prepago se le brinda soporte tecnico con costo de visita y material por parte del cliente.'],
            ['id' => 0, 'titulo' => 'Fecha limite de pago', 'tipo' => 'recomendacion', 'contenido' => 'Fecha de limite de pago puntual 1 de cada mes, evite cargos extra o corte de servicio realizando sus pagos con anticipacion en horarios de labores.'],
            ['id' => 0, 'titulo' => 'Anticipe su pago', 'tipo' => 'recomendacion', 'contenido' => 'Se recomienda realizar su pago con dias de anticipacion para evitar el cobro de $30 extra por pago tardio.'],
            ['id' => 0, 'titulo' => 'Revise errores de pago', 'tipo' => 'recomendacion', 'contenido' => 'Revise la informacion enviada para evitar errores, retrasos o rechazos en sus pagos.'],
            ['id' => 0, 'titulo' => 'Comprobantes validos', 'tipo' => 'recomendacion', 'contenido' => 'Los comprobantes de pago ilegibles, incompletos, sin referencia y no originales pueden tardar hasta 72 horas habiles en ser aplicados.'],
            ['id' => 0, 'titulo' => 'Reactivacion por suspension', 'tipo' => 'recomendacion', 'contenido' => 'Si su servicio se encuentra suspendido, se reactivara en el transcurso de 24 horas habiles posteriores al registro de su pago.'],
            ['id' => 0, 'titulo' => 'Cargo extra por reconexion', 'tipo' => 'recomendacion', 'contenido' => 'Si su servicio se suspendio por 1 mes o mas debera pagar un cargo extra para reactivar su servicio, llamar a servicio al cliente para mas informacion.'],
            ['id' => 0, 'titulo' => 'Factura con anticipacion', 'tipo' => 'recomendacion', 'contenido' => 'Si requiere factura de su servicio realice sus pagos con 1 dia habil de anticipacion al cierre del mes, enviar nombre y RFC al enviar sus pagos.'],
            ['id' => 0, 'titulo' => 'No use mensajes temporales', 'tipo' => 'recomendacion', 'contenido' => 'No utilice mensajes temporales y conserve sus comprobantes de pago, la verificacion de los pagos puede tardar hasta 10 dias habiles.'],
            ['id' => 0, 'titulo' => 'Use la app WIZNET', 'tipo' => 'recomendacion', 'contenido' => 'Descargue nuestra app movil "WIZNET" desde la tienda de aplicaciones, desde ahi podra consultar cuentas bancarias, registrar sus pagos y realizar soportes de fallas en su servicio en cualquier horario.'],
            ['id' => 0, 'titulo' => 'Telefono para soporte tecnico', 'tipo' => 'recomendacion', 'contenido' => 'Le recordamos que para soporte tecnico general debe llamar a servicio al cliente al numero 3337352050.'],
            ['id' => 0, 'titulo' => 'Verifique pagos aplicados', 'tipo' => 'recomendacion', 'contenido' => 'IMPORTANTE!! verificar la informacion de sus pagos siempre ya que despues de 72 horas no es posible corregir errores de pagos mal aplicados.'],
        ],
    ];
}
