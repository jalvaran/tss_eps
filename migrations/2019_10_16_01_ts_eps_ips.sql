ALTER TABLE `hoja_de_trabajo` ADD `PedientesPorRadicados` VARCHAR(2) NOT NULL AFTER `DiferenciaVariada`, ADD `PedientesPorDevoluciones` VARCHAR(2) NOT NULL AFTER `PedientesPorRadicados`, ADD `PedientesPorNotas` VARCHAR(2) NOT NULL AFTER `PedientesPorDevoluciones`, ADD `PedientesPorCopagos` VARCHAR(2) NOT NULL AFTER `PedientesPorNotas`;