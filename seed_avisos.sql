INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Pago fuera de horario', 'advertencia', 'Si realiza el pago de su servicio fuera del horario de labores, en dias inhabiles y festivos contara como pago tardio.', 1, 10
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'advertencia' AND titulo = 'Pago fuera de horario'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Factura en fecha de corte', 'advertencia', 'Si requiere factura de su servicio y su pago se realiza en dias inhabiles o festivos en la fecha de corte no sera facturado.', 1, 20
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'advertencia' AND titulo = 'Factura en fecha de corte'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Evite pago tardio', 'advertencia', 'Se recomienda realizar su pago con dias de anticipacion para evitar el cobro de $30 extra por pago tardio.', 1, 30
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'advertencia' AND titulo = 'Evite pago tardio'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Conserve sus comprobantes', 'advertencia', 'No utilice mensajes temporales y conserve sus comprobantes de pago ya que la verificacion de los pagos puede tardar hasta 10 dias habiles.', 1, 40
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'advertencia' AND titulo = 'Conserve sus comprobantes'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'WhatsApp sin soporte tecnico', 'advertencia', 'Le recordamos que el numero de WhatsApp no atiende reportes de fallas, para soporte tecnico llame a servicio al cliente al numero 3337352050.', 1, 50
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'advertencia' AND titulo = 'WhatsApp sin soporte tecnico'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Verifique medios de pago', 'advertencia', 'IMPORTANTE!! No realice pagos de servicio sin antes verificar la informacion de los medios de pago vigentes.', 1, 60
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'advertencia' AND titulo = 'Verifique medios de pago'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Revise medios de pago', 'recomendacion', 'Seguir las indicaciones que se proporcionan al utilizar los medios de pago disponibles, revisar frecuentemente.', 1, 10
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Revise medios de pago'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Prepago con visita con costo', 'recomendacion', 'Se le recuerda que a todo servicio en prepago se le brinda soporte tecnico con costo de visita y material por parte del cliente.', 1, 20
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Prepago con visita con costo'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Fecha limite de pago', 'recomendacion', 'Fecha de limite de pago puntual 1 de cada mes, evite cargos extra o corte de servicio realizando sus pagos con anticipacion en horarios de labores.', 1, 30
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Fecha limite de pago'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Anticipe su pago', 'recomendacion', 'Se recomienda realizar su pago con dias de anticipacion para evitar el cobro de $30 extra por pago tardio.', 1, 40
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Anticipe su pago'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Revise errores de pago', 'recomendacion', 'Revise la informacion enviada para evitar errores, retrasos o rechazos en sus pagos.', 1, 50
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Revise errores de pago'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Comprobantes validos', 'recomendacion', 'Los comprobantes de pago ilegibles, incompletos, sin referencia y no originales pueden tardar hasta 72 horas habiles en ser aplicados.', 1, 60
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Comprobantes validos'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Reactivacion por suspension', 'recomendacion', 'Si su servicio se encuentra suspendido, se reactivara en el transcurso de 24 horas habiles posteriores al registro de su pago.', 1, 70
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Reactivacion por suspension'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Cargo extra por reconexion', 'recomendacion', 'Si su servicio se suspendio por 1 mes o mas debera pagar un cargo extra para reactivar su servicio, llamar a servicio al cliente para mas informacion.', 1, 80
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Cargo extra por reconexion'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Factura con anticipacion', 'recomendacion', 'Si requiere factura de su servicio realice sus pagos con 1 dia habil de anticipacion al cierre del mes, enviar nombre y RFC al enviar sus pagos.', 1, 90
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Factura con anticipacion'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'No use mensajes temporales', 'recomendacion', 'No utilice mensajes temporales y conserve sus comprobantes de pago, la verificacion de los pagos puede tardar hasta 10 dias habiles.', 1, 100
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'No use mensajes temporales'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Use la app WIZNET', 'recomendacion', 'Descargue nuestra app movil "WIZNET" desde la tienda de aplicaciones, desde ahi podra consultar cuentas bancarias, registrar sus pagos y realizar soportes de fallas en su servicio en cualquier horario.', 1, 110
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Use la app WIZNET'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Telefono para soporte tecnico', 'recomendacion', 'Le recordamos que para soporte tecnico general debe llamar a servicio al cliente al numero 3337352050.', 1, 120
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Telefono para soporte tecnico'
);

INSERT INTO avisos (titulo, tipo, contenido, activo, orden)
SELECT 'Verifique pagos aplicados', 'recomendacion', 'IMPORTANTE!! verificar la informacion de sus pagos siempre ya que despues de 72 horas no es posible corregir errores de pagos mal aplicados.', 1, 130
WHERE NOT EXISTS (
    SELECT 1 FROM avisos
    WHERE tipo = 'recomendacion' AND titulo = 'Verifique pagos aplicados'
);
