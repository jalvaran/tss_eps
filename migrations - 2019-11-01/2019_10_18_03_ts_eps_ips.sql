ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXPagos1 varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXPagos2 varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXGlosas1 varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXCopagos varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXImpuestos varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXGlosas2 varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXPagos varchar(10);
ALTER TABLE `hoja_de_trabajo` ADD COLUMN IF NOT EXISTS ConciliacionEPSXGlosas varchar(10);