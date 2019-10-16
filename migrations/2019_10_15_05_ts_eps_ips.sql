ALTER TABLE `actas_conciliaciones_items` ADD `NumeroOperacion` BIGINT NOT NULL AFTER `NumeroRadicado`;
ALTER TABLE `actas_conciliaciones_items` ADD `ImpuestosPorRecuperar` DOUBLE NOT NULL AFTER `Impuestos`;
ALTER TABLE `actas_liquidaciones_items` ADD `NumeroOperacion` BIGINT NOT NULL AFTER `NumeroRadicado`;
ALTER TABLE `actas_liquidaciones_items` ADD `ImpuestosPorRecuperar` DOUBLE NOT NULL AFTER `Impuestos`;