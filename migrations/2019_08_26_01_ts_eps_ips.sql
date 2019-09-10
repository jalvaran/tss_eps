DROP VIEW IF EXISTS `vista_copagos_asmet`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_copagos_asmet` AS select `notas_db_cr_2`.`NumeroFactura` AS `NumeroFactura`,sum(abs(`notas_db_cr_2`.`ValorTotal`)) AS `ValorTotal` from `notas_db_cr_2` where ((`notas_db_cr_2`.`TipoOperacion` = '2258') or (`notas_db_cr_2`.`TipoOperacion` = '2225') or (`notas_db_cr_2`.`TipoOperacion` = '2260') or (`notas_db_cr_2`.`TipoOperacion` = '2254')) group by `notas_db_cr_2`.`NumeroFactura`;