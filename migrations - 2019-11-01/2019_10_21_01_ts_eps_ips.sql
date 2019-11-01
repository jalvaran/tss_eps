DROP VIEW IF EXISTS `vista_retenciones_facturas`;
CREATE VIEW vista_retenciones_facturas AS 

SELECT NumeroFactura, SUM(ValorDebito) as Debitos, SUM(ValorCredito) as Creditos FROM retenciones WHERE Cuentacontable LIKE '2365%' GROUP BY NumeroFactura;