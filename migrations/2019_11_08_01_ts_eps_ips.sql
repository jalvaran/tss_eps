ALTER TABLE `registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS Municipio VARCHAR(100) AFTER `DepartamentoRadicacion`;
ALTER TABLE `registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DiasLMA DOUBLE AFTER `MesServicio`;
ALTER TABLE `registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS ValorAPagarLMA DOUBLE AFTER `DiasLMA`;
ALTER TABLE `registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DescuentoReconocimientoBDUA DOUBLE AFTER `ValorAPagarLMA`;
ALTER TABLE `registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DescuentosConciliadosAFavorASMET DOUBLE AFTER `DescuentoReconocimientoBDUA`;
ALTER TABLE `registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DescuentoInicial DOUBLE AFTER `DescuentosConciliadosAFavorASMET`;
ALTER TABLE `temporal_registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS Municipio VARCHAR(100) AFTER `DepartamentoRadicacion`;
ALTER TABLE `temporal_registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DiasLMA DOUBLE AFTER `MesServicio`;
ALTER TABLE `temporal_registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS ValorAPagarLMA DOUBLE AFTER `DiasLMA`;
ALTER TABLE `temporal_registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DescuentoReconocimientoBDUA DOUBLE AFTER `ValorAPagarLMA`;
ALTER TABLE `temporal_registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DescuentosConciliadosAFavorASMET DOUBLE AFTER `DescuentoReconocimientoBDUA`;
ALTER TABLE `temporal_registro_liquidacion_contratos_items` ADD COLUMN IF NOT EXISTS DescuentoInicial DOUBLE AFTER `DescuentosConciliadosAFavorASMET`;