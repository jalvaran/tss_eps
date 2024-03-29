DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t2.Estado,
        
        t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS Pendientes,
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,t2.MesServicio,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t1.NumeroFactura ),0)) AS ImpuestosSegunASMET,
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089') ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039') AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0)) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2086'  LIMIT 1),0)) AS ValorRegistradoFacturas,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' LIMIT 1),0)) AS ValorRegistradoDevoluciones,
        ((SELECT ValorRegistradoFacturas) - (SELECT ABS(ValorRegistradoDevoluciones))) AS DiferenciaFacturasDevoluciones,
        (IF((SELECT DiferenciaFacturasDevoluciones)<>(SELECT ValorMenosImpuestos),'SI','NO')) AS PresentaDiferenciasContables,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura ),0)) AS TotalConciliaciones,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,
        ((SELECT ValorSegunEPS) + (SELECT ConciliacionesAFavorIPS) - (SELECT ConciliacionesAFavorEPS)) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;