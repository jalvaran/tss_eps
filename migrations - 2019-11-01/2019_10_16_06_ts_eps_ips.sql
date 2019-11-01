ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS PendientesPorRadicados varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS PendientesPorDevoluciones  varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS PendientesPorNotas  varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS PendientesPorCopagos   varchar(10);