DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS
SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,t1.NoRelacionada,

        (SELECT FechaFactura FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as FechaFactura,
        t2.MesServicio,
		t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS Pendientes,
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,
		t2.NumeroContrato,
		t2.ValorOriginal as ValorDocumento,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t2.NumeroFactura ),0)) AS ImpuestosSegunASMET,
		t2.ValorMenosImpuestos,
		(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3070' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089' OR notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3091' OR notas_db_cr_2.TipoOperacion2='2260') ),0)) AS TotalPagosNotas,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2299' ),0)) AS Capitalizacion,
        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) ) ) AS TotalPagos,
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2216' ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2275' ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS FacturasDevueltas,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS NumeroFacturasDevueltasAnticipos,
		(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0)) AS TotalCopagosNotas,
        ((SELECT ABS(FacturasDevueltas))+(SELECT ABS(TotalCopagosNotas) ) ) AS TotalCopagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND (NumeroInterno='2215' OR NumeroInterno='2601' OR NumeroInterno='2214') ),0)) AS OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2260' ),0)) AS AjustesCartera,
        (SELECT IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaContra,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' ) GROUP BY NumeroTransaccion ),0)) AS DevolucionesPresentadas,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND (notas_db_cr_2.TipoOperacion LIKE '20%' ) GROUP BY NumeroTransaccion ),0)) AS FacturasPresentadas,
        (SELECT IF(((SELECT FacturasPresentadas)>((SELECT DevolucionesPresentadas)+(SELECT NumeroFacturasDevueltasAnticipos) ) ),'SI','NO')) AS FacturaActiva,
        (SELECT IF(FacturaActiva='SI',0,(SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039') AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0))) ) AS TotalDevolucionesNotas,
        (SELECT IF(FacturaActiva='SI' ,0,((SELECT ABS(TotalDevolucionesNotas))+(SELECT ABS(FacturasDevueltas))) )) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
		(t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) ) AS ValorSegunEPS,
        (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,
        ((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura ),0)) AS TotalConciliaciones,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,
        ((SELECT ValorSegunEPS) + (SELECT ConciliacionesAFavorIPS) - (SELECT ConciliacionesAFavorEPS)) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes
        
        
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_eps`;
CREATE VIEW vista_cruce_cartera_eps AS
SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,(SELECT NoRelacionada FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as NoRelacionada,
        (SELECT FechaFactura FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as FechaFactura,
        t2.MesServicio,
		t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS Pendientes,
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,
		t2.NumeroContrato,
		t2.ValorOriginal as ValorDocumento,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t2.NumeroFactura ),0)) AS ImpuestosSegunASMET,
		t2.ValorMenosImpuestos,
		(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3070' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089' OR notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3091' OR notas_db_cr_2.TipoOperacion2='2260') ),0)) AS TotalPagosNotas,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2299' ),0)) AS Capitalizacion,
        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) ) ) AS TotalPagos,
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2216' ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2275' ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS FacturasDevueltas,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS NumeroFacturasDevueltasAnticipos,
		(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0)) AS TotalCopagosNotas,
        ((SELECT ABS(FacturasDevueltas))+(SELECT ABS(TotalCopagosNotas) ) ) AS TotalCopagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND (NumeroInterno='2215' OR NumeroInterno='2601' OR NumeroInterno='2214') ),0)) AS OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2260' ),0)) AS AjustesCartera,
        (SELECT IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaContra,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' ) GROUP BY NumeroTransaccion ),0)) AS DevolucionesPresentadas,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND (notas_db_cr_2.TipoOperacion LIKE '20%' ) GROUP BY NumeroTransaccion ),0)) AS FacturasPresentadas,
        (SELECT IF(((SELECT FacturasPresentadas)>((SELECT DevolucionesPresentadas)+(SELECT NumeroFacturasDevueltasAnticipos) ) ),'SI','NO')) AS FacturaActiva,
        (SELECT IF(FacturaActiva='SI',0,(SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039') AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0))) ) AS TotalDevolucionesNotas,
        (SELECT IF(FacturaActiva='SI' ,0,((SELECT ABS(TotalDevolucionesNotas))+(SELECT ABS(FacturasDevueltas))) )) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
		(t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) ) AS ValorSegunEPS,
        (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,
        ((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura ),0)) AS TotalConciliaciones,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,
        ((SELECT ValorSegunEPS) + (SELECT ConciliacionesAFavorIPS) - (SELECT ConciliacionesAFavorEPS)) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes,
        (SELECT IF( (SELECT ABS(TotalPagos)) = (SELECT ABS(Diferencia)),1,0)) as DiferenciaXPagos 
FROM carteraeps t2;

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_no_relacionadas_ips`;
CREATE VIEW vista_cruce_cartera_eps_no_relacionadas_ips AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura) AND ((FacturaActiva='NO') OR (DevolucionesPresentadas=0) ) AND ValorSegunEPS<0;

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_relacionadas_ips`;
CREATE VIEW vista_cruce_cartera_eps_relacionadas_ips AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_hoja_trabajo_cruce`;
CREATE VIEW vista_hoja_trabajo_cruce AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura) 
 OR (NOT EXISTS (SELECT 1 FROM carteracargadaips t3 WHERE t1.NumeroFactura=t3.NumeroFactura) AND ValorSegunEPS<0);

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_no_relacionadas_ips_completa`;
CREATE VIEW vista_cruce_cartera_eps_no_relacionadas_ips_completa AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_reporte_ips`;
CREATE VIEW vista_reporte_ips AS
SELECT  t2.FechaFactura AS 'Fecha Factura',
		t2.MesServicio AS 'Mes servicio',
        t2.NumeroRadicado AS 'Numero Radicado',
        t2.FechaRadicado AS 'Fecha Radicado',
		t2.NumeroContrato AS 'Numero Contrato',
		t2.NumeroFactura AS 'Numero factura',
		t2.ValorDocumento AS 'Valor de Factura',
        t2.Impuestos AS 'Impuestos Factura',
		t2.TotalPagos AS 'Pagos',
		t2.TotalAnticipos AS 'Anticipos',
        t2.TotalCopagos AS 'Copagos',
        t2.DescuentoPGP AS 'Descuentos PGP',
		t2.OtrosDescuentos AS 'Otros Descuentos',
        t2.AjustesCartera AS 'Ajustes Cartera',
        t2.TotalGlosaFavor AS 'Glosa Aceptada Ips',
        t2.TotalGlosaContra AS 'Glosa levantada EPS',
		t2.GlosaXConciliar AS 'Glosa X Conciliar',  
		t2.TotalDevoluciones AS 'Devoluciones',
        t2.ValorSegunEPS AS 'Saldo Eps',
        t2.ValorSegunIPS AS 'Saldo Ips',
		t2.Diferencia AS 'Valor Diferencia'
FROM vista_cruce_cartera_eps_relacionadas_ips t2 WHERE ValorIPSMenor='NO' AND Diferencia<>'0';


DROP VIEW IF EXISTS `vista_cruce_totales_actas_conciliaciones`;
CREATE VIEW vista_cruce_totales_actas_conciliaciones AS 
SELECT t1.NumeroFactura,t1.Diferencia,
        (SELECT IF( ( ABS(TotalPagos)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXPagos,
        (SELECT IF( ( ABS(TotalAnticipos)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXAnticipos,
        (SELECT IF( ( ABS(TotalCopagos)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXCopagos,
        (SELECT IF( ( ABS(DescuentoPGP)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXDescuentoPGP,
        (SELECT IF( ( ABS(OtrosDescuentos)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXOtrosDescuentos,
        (SELECT IF( ( ABS(AjustesCartera)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXAjustesCartera,
        (SELECT IF( ( ABS(TotalGlosaFavor)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXGlosaFavorEPS,
        (SELECT IF( ( ABS(TotalGlosaContra)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXGlosaContraEPS,
        (SELECT IF( ( ABS(GlosaXConciliar)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXGlosaXConciliar,
        (SELECT IF( ( ABS(TotalDevoluciones)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXDevoluciones,
        (SELECT IF( ( ABS(Impuestos)) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXImpuestos,
        (SELECT IF(  ValorSegunIPS > ValorDocumento,Diferencia,0)) as DiferenciaXValorFacturado,

        (   (SELECT DiferenciaXPagos) + 
            (SELECT DiferenciaXAnticipos) + 
            (SELECT DiferenciaXCopagos) + 
            (SELECT DiferenciaXDescuentoPGP) + 
            (SELECT DiferenciaXOtrosDescuentos) + 
            (SELECT DiferenciaXAjustesCartera) + 
            (SELECT DiferenciaXGlosaFavorEPS) + 
            (SELECT DiferenciaXGlosaContraEPS) + 
            (SELECT DiferenciaXGlosaXConciliar) + 
            (SELECT DiferenciaXDevoluciones) + 
            (SELECT DiferenciaXImpuestos) + 
            (SELECT DiferenciaXValorFacturado) ) AS TotalDiferenciasComunes,

       (SELECT IF( (SELECT TotalDiferenciasComunes)=0 AND ValorSegunEPS=0 AND ABS(TotalDevoluciones)>0,Diferencia,0)) as DiferenciaXDevolucionesNoIPS,
       (SELECT IF( (SELECT TotalDiferenciasComunes)=0 AND (SELECT DiferenciaXDevolucionesNoIPS)=0 AND ABS(TotalPagos)>0 AND ABS(GlosaXConciliar)>0,GlosaXConciliar,0)) as GlosasXConciliar2,
       (SELECT IF( (SELECT TotalDiferenciasComunes)=0 AND (SELECT DiferenciaXDevolucionesNoIPS)=0 AND ABS(TotalPagos)>0 AND ABS(GlosaXConciliar)>0,TotalPagos,0)) as XPagos2,
       
       (SELECT IF( (SELECT TotalDiferenciasComunes)=0 AND ABS(DiferenciaXDevolucionesNoIPS)=0 AND ABS(GlosasXConciliar2)=0,Diferencia,0)) as DiferenciaVariada 

FROM vista_cruce_cartera_asmet t1
WHERE Diferencia<>0;