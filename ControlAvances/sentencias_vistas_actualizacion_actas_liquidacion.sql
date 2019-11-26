DROP VIEW IF EXISTS `vista_pagos_actas_liquidaciones`;
CREATE VIEW `vista_pagos_actas_liquidaciones` AS
SELECT t2.*, (SELECT t1.FechaFirma FROM ts_eps.actas_liquidaciones t1 WHERE t1.ID=t2.idActaLiquidacion LIMIT 1) AS FechaFirmaActa, 
        
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS TotalPagosPosteriores,
        (SELECT IFNULL((SELECT GROUP_CONCAT(ValorPago SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS ValoresPagados,
        (SELECT IFNULL((SELECT GROUP_CONCAT(FechaTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS FechasTransacciones,
        (SELECT IFNULL((SELECT GROUP_CONCAT(NumeroTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS Transacciones,
        (t2.ValorSegunEPS -(SELECT TotalPagosPosteriores) ) AS SaldoFinal
        
        FROM `actas_liquidaciones_items` t2 ;


DROP VIEW IF EXISTS `vista_pagos_actas_liquidaciones_manuales`;
CREATE VIEW `vista_pagos_actas_liquidaciones_manuales` AS
SELECT t2.*, '2018-01-01' AS FechaFirmaActa, 
        
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS TotalPagosPosteriores,
        (SELECT IFNULL((SELECT GROUP_CONCAT(ValorPago SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS ValoresPagados,
        (SELECT IFNULL((SELECT GROUP_CONCAT(FechaTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS FechasTransacciones,
        (SELECT IFNULL((SELECT GROUP_CONCAT(NumeroTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS Transacciones,
        (t2.Saldo -(SELECT TotalPagosPosteriores) ) AS SaldoFinal
        
        FROM `registro_liquidacion_contratos_items` t2 ;
