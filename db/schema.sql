-- Tabla para almacenar los registros enviados desde el formulario de Registro de Pagos.
CREATE TABLE `registro_pagos` (
  -- Identificador unico del registro.
  `id` INT NOT NULL AUTO_INCREMENT,
  -- Nombre completo del cliente que registra el pago.
  `nombre` VARCHAR(150) NOT NULL,
  -- Oficina seleccionada por el cliente.
  `oficina` VARCHAR(120) NOT NULL,
  -- Numero de cliente capturado en el formulario.
  `numero_cliente` VARCHAR(80) NOT NULL,
  -- Correo electronico del cliente.
  `correo` VARCHAR(150) NOT NULL,
  -- Telefono de contacto del cliente.
  `telefono` VARCHAR(60) NOT NULL,
  -- Comentarios adicionales del formulario.
  `comentarios` TEXT NULL,
  -- Nombre original del archivo subido por el cliente.
  `archivo_nombre` VARCHAR(255) NOT NULL,
  -- Ruta relativa donde se guarda el comprobante dentro del proyecto.
  `archivo_ruta` VARCHAR(255) NOT NULL,
  -- Fecha y hora en que se registro el pago.
  `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
