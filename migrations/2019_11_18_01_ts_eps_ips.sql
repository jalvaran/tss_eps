ALTER TABLE `actas_conciliaciones_items` ADD COLUMN IF NOT EXISTS NumeroDiasLMA DOUBLE AFTER `NumeroFactura`;
ALTER TABLE `actas_conciliaciones_items` ADD COLUMN IF NOT EXISTS ValorAPagarLMA DOUBLE AFTER `NumeroDiasLMA`;
ALTER TABLE `actas_liquidaciones_items` ADD COLUMN IF NOT EXISTS NumeroDiasLMA DOUBLE AFTER `NumeroFactura`;
ALTER TABLE `actas_liquidaciones_items` ADD COLUMN IF NOT EXISTS ValorAPagarLMA DOUBLE AFTER `NumeroDiasLMA`;