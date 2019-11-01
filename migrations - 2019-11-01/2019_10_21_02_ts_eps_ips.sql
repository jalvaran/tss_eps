ALTER TABLE `historial_carteracargada_eps` ADD COLUMN IF NOT EXISTS ValidaFactura BIGINT AFTER `NumeroFactura`;
ALTER TABLE `temporalcarguecarteraeps` ADD COLUMN IF NOT EXISTS ValidaFactura BIGINT AFTER `NumeroFactura`;
UPDATE `historial_carteracargada_eps` SET ValidaFactura=NumeroFactura;