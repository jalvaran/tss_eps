DROP VIEW IF EXISTS `vista_copagos_asmet`;
CREATE VIEW vista_copagos_asmet AS 
SELECT NumeroFactura,SUM(ABS(ValorTotal)) AS ValorTotal FROM notas_db_cr_2 WHERE TipoOperacion='2258' 
OR notas_db_cr_2.TipoOperacion='2225' OR notas_db_cr_2.TipoOperacion='2214' GROUP BY NumeroFactura;


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t2.NumeroRadicado,t2.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,t2.MesServicio,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM Anticipos2 WHERE Anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' AND notas_db_cr_2.FechaTransaccion>t2.FechaRadicado LIMIT 1),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades

FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;