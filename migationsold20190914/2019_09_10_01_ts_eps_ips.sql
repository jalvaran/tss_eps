DROP VIEW IF EXISTS `vista_facturasdvueltas_anticiposvscxp`;
CREATE VIEW vista_facturasdvueltas_anticiposvscxp AS 
SELECT (SELECT notas_db_cr_2.NumeroTransaccion) AS Numero_concidencia,
       (SELECT anticipos2.NumeroFactura) AS Numerofactura_anticipos,
	   (SELECT notas_db_cr_2.NumeroFactura) AS Numerofactura_cxp,
	   (SELECT notas_db_cr_2.TipoOperacion) AS TipoOperacioncxp,
	   (SELECT anticipos2.NumeroInterno) AS TipoOperacionanticipos,
	   (SELECT anticipos2.ValorAnticipado) AS ValorDevuelto
FROM anticipos2 INNER JOIN notas_db_cr_2 ON notas_db_cr_2.NumeroTransaccion=anticipos2.NumeroAnticipo AND notas_db_cr_2.TipoOperacion=anticipos2.NumeroInterno /*WHERE notas_db_cr_2.TipoOperacion='2259'*/;