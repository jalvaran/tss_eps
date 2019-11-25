DROP VIEW IF EXISTS `vista_actualizacion_saldo_actas_liquidacion`;
CREATE VIEW vista_actualizacion_saldo_actas_liquidacion AS 
SELECT t1.*,(SELECT @SaldoInicial:=CONCAT('ts_eps_',t1.NIT_IPS))

FROM ts_eps.actas_liquidaciones t1;


(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 t2 WHERE t2.NumeroFactura=t1.NumeroFactura AND t2.FechaTransaccion>t1.FechaFirma AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')  ),0)) AS TotalPagosPosterioresAFirma,
