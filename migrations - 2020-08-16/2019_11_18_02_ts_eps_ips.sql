ALTER TABLE `actas_conciliaciones_items` ADD COLUMN IF NOT EXISTS CodigoSucursal DOUBLE AFTER `ValorAPagarLMA`;
ALTER TABLE `actas_liquidaciones_items` ADD COLUMN IF NOT EXISTS CodigoSucursal DOUBLE AFTER `ValorAPagarLMA`;