DROP VIEW IF EXISTS `vista_facturas_sr_eps_2`;
CREATE VIEW vista_facturas_sr_eps_2 AS 
SELECT *,
    (SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' ) ) AS TotalDevoluciones,
    (SELECT SUM(ValorCredito - ValorDebito) FROM retenciones WHERE retenciones.NumeroFactura=t1.NumeroFactura ) AS TotalRetenciones,
    (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089') ),0)) AS TotalPagos,
    (t1.ValorOriginal -(SELECT TotalPagos)-(SELECT TotalRetenciones)) AS Saldo,
    ((t1.ValorOriginal-t1.ValorMenosImpuestos) ) AS ValorImpuestosCalculados
 FROM carteraeps t1
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura);