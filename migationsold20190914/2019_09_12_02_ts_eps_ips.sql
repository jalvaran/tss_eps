DROP VIEW IF EXISTS `vista_copagos_asmet`;
CREATE VIEW vista_copagos_asmet AS 
SELECT NumeroFactura,SUM(ABS(ValorTotal)) AS ValorTotal FROM notas_db_cr_2 WHERE (TipoOperacion='2258' 
OR notas_db_cr_2.TipoOperacion='2225' OR notas_db_cr_2.TipoOperacion='2260' OR notas_db_cr_2.TipoOperacion='2254') AND (C13<>'N') GROUP BY NumeroFactura;

DROP VIEW IF EXISTS `vista_facturasdvueltas_anticiposvscxp`;
CREATE VIEW vista_facturasdvueltas_anticiposvscxp AS 
SELECT (SELECT notas_db_cr_2.NumeroTransaccion) AS Numero_concidencia,
       (SELECT anticipos2.NumeroFactura) AS Numerofactura_anticipos,
	   (SELECT notas_db_cr_2.NumeroFactura) AS Numerofactura_cxp,
	   (SELECT notas_db_cr_2.TipoOperacion) AS TipoOperacioncxp,
	   (SELECT anticipos2.NumeroInterno) AS TipoOperacionanticipos,
	   (SELECT anticipos2.ValorAnticipado) AS ValorDevuelto
FROM anticipos2 INNER JOIN notas_db_cr_2 ON notas_db_cr_2.NumeroTransaccion=anticipos2.NumeroAnticipo AND notas_db_cr_2.TipoOperacion=anticipos2.NumeroInterno /*WHERE notas_db_cr_2.TipoOperacion='2259'*/;

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
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3070' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089' OR notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3091' OR notas_db_cr_2.TipoOperacion2='2260') AND (notas_db_cr_2.TipoOperacion!='2103') ),0)) AS TotalPagosNotas,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2299' ),0)) AS Capitalizacion,
        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) ) ) AS TotalPagos,
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2216' ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2275' ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS FacturasDevueltas,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS NumeroFacturasDevueltasAnticipos,
	   /* (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),(SELECT SUM(ValorDevuelto) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp))) AS FacturasDevueltas,*/
	    (SELECT IFNULL((SELECT SUM(ValorDevuelto) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp),0)) AS ValorFacturasDevueltascxpvsant,
       /*(SELECT IFNULL((SELECT SUM(ValorDevuelto) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp),0)) AS FacDevueltasCXPVSANT,*/
        (SELECT IFNULL((SELECT COUNT(Numerofactura_anticipos) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos!=vista_facturasdvueltas_anticiposvscxp.Numerofactura_cxp),0)) AS FacturasDevueltasCXPVSANT,
		(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0)) AS TotalCopagos,
        
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND (NumeroInterno='2215' OR NumeroInterno='2601' OR NumeroInterno='2214') ),0)) AS OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2260' ),0)) AS AjustesCartera,
        (SELECT IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaContra,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' ) ),0)) AS DevolucionesPresentadas,
        
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND notas_db_cr_2.TipoOperacion LIKE '20%'  ),0)) AS FacturasPresentadas,/*(SELECT COUNT(Numerofactura_anticipos) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp)*/
        (SELECT IF((((SELECT FacturasPresentadas)) > ((SELECT DevolucionesPresentadas)+(SELECT NumeroFacturasDevueltasAnticipos)+(SELECT FacturasDevueltasCXPVSANT) ) ),'SI','NO')) AS FacturaActiva,
        (SELECT IF(FacturaActiva='SI',0,(SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039') AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0))) ) AS TotalDevolucionesNotas,
        (SELECT IF(FacturaActiva='SI' ,0,((SELECT ABS(TotalDevolucionesNotas))+(SELECT ABS(FacturasDevueltas))+ (SELECT IF(FacturasDevueltasCXPVSANT=DevolucionesPresentadas,0,(SELECT ABS(ValorFacturasDevueltascxpvsant)))) ))) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,

	    (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones)/*+(FacturasDevueltas)*/)-(SELECT ABS(DescuentoPGP))-(SELECT ABS(ConciliacionesAFavorEPS))+(SELECT ABS(ConciliacionesAFavorIPS)) ) AS ValorSegunEPS,
        (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,
        ((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura ),0)) AS TotalConciliaciones,
        
        ((SELECT ValorSegunEPS)  ) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes,
        (SELECT IF( (SELECT ABS(TotalPagos)) = (SELECT ABS(Diferencia)),1,0)) as DiferenciaXPagos 
        
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura WHERE t2.Estado<2;

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
		(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3070' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089' OR notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3091' OR notas_db_cr_2.TipoOperacion2='2260') AND (notas_db_cr_2.TipoOperacion!='2103') ),0)) AS TotalPagosNotas,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2299' ),0)) AS Capitalizacion,
        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) ) ) AS TotalPagos,
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2216' ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2275' ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS FacturasDevueltas,
        (SELECT IFNULL((SELECT COUNT(NumeroFactura) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS NumeroFacturasDevueltasAnticipos,
	   /* (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),(SELECT SUM(ValorDevuelto) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp))) AS FacturasDevueltas,*/
	    (SELECT IFNULL((SELECT SUM(ValorDevuelto) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp),0)) AS ValorFacturasDevueltascxpvsant,
       /*(SELECT IFNULL((SELECT SUM(ValorDevuelto) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp),0)) AS FacDevueltasCXPVSANT,*/
        (SELECT IFNULL((SELECT COUNT(Numerofactura_anticipos) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos!=vista_facturasdvueltas_anticiposvscxp.Numerofactura_cxp),0)) AS FacturasDevueltasCXPVSANT,
		(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0)) AS TotalCopagos,
        
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND (NumeroInterno='2215' OR NumeroInterno='2601' OR NumeroInterno='2214') ),0)) AS OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2260' ),0)) AS AjustesCartera,
        (SELECT IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaContra,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' ) ),0)) AS DevolucionesPresentadas,
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND notas_db_cr_2.TipoOperacion LIKE '20%'  ),0)) AS FacturasPresentadas,/*(SELECT COUNT(Numerofactura_anticipos) FROM vista_facturasdvueltas_anticiposvscxp WHERE vista_facturasdvueltas_anticiposvscxp.Numerofactura_anticipos=t2.NumeroFactura AND TipoOperacionanticipos='2259' AND Numerofactura_anticipos!=Numerofactura_cxp)*/
        (SELECT IF((((SELECT FacturasPresentadas)) > ((SELECT DevolucionesPresentadas)+(SELECT NumeroFacturasDevueltasAnticipos)+(SELECT FacturasDevueltasCXPVSANT) ) ),'SI','NO')) AS FacturaActiva,
        (SELECT IF(FacturaActiva='SI',0,(SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039') AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0))) ) AS TotalDevolucionesNotas,
        (SELECT IF(FacturaActiva='SI' ,0,((SELECT ABS(TotalDevolucionesNotas))+(SELECT ABS(FacturasDevueltas))+ (SELECT IF(FacturasDevueltasCXPVSANT=DevolucionesPresentadas,0,(SELECT ABS(ValorFacturasDevueltascxpvsant)))) ))) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,

	    (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones)/*+(FacturasDevueltas)*/)-(SELECT ABS(DescuentoPGP))-(SELECT ABS(ConciliacionesAFavorEPS))+(SELECT ABS(ConciliacionesAFavorIPS)) ) AS ValorSegunEPS,
        (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,
        ((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura ),0)) AS TotalConciliaciones,
        
        ((SELECT ValorSegunEPS)  ) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes,
        (SELECT IF( (SELECT ABS(TotalPagos)) = (SELECT ABS(Diferencia)),1,0)) as DiferenciaXPagos    

FROM carteraeps t2 WHERE t2.Estado<2;

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

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_sin_relacion_segun_ags`;
CREATE VIEW vista_cruce_cartera_eps_sin_relacion_segun_ags AS 
SELECT 
        t2.FechaRadicado as FechaFactura ,
        t2.MesServicio ,
        t2.DepartamentoRadicacion ,
        t2.NumeroRadicado ,
        t2.FechaRadicado ,
		t2.NumeroContrato ,
		t2.NumeroFactura ,
		t2.ValorDocumento ,
        t2.Impuestos ,
		(t2.TotalPagos + ABS(t2.Diferencia)) as TotalPagos ,
		t2.TotalAnticipos ,
        t2.TotalCopagos ,
        t2.DescuentoPGP ,
		t2.OtrosDescuentos ,
        t2.AjustesCartera ,
        t2.TotalGlosaInicial ,
        t2.TotalGlosaFavor ,
        t2.TotalGlosaContra ,
	t2.GlosaXConciliar ,  
	t2.TotalDevoluciones ,
        '0' AS ValorSegunEPS ,
        '0' AS ValorSegunIPS ,
        '0' as Diferencia

FROM vista_cruce_cartera_eps t2 
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t1 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_reporte_ips`;
CREATE VIEW vista_reporte_ips AS
SELECT  t2.FechaFactura ,
        t2.MesServicio ,
        t2.DepartamentoRadicacion ,
        t2.NumeroRadicado ,
        t2.FechaRadicado ,
		t2.NumeroContrato ,
		t2.NumeroFactura ,
		t2.ValorDocumento ,
        t2.Impuestos ,
		t2.TotalPagos ,
		t2.TotalAnticipos ,
        t2.TotalCopagos ,
        t2.DescuentoPGP ,
		t2.OtrosDescuentos ,
        t2.AjustesCartera ,
        t2.TotalGlosaInicial ,
        t2.TotalGlosaFavor ,
        t2.TotalGlosaContra ,
		t2.GlosaXConciliar ,  
		t2.TotalDevoluciones ,
        t2.ValorSegunEPS ,
        t2.ValorSegunIPS ,
		t2.Diferencia 
FROM vista_cruce_cartera_eps_relacionadas_ips t2;

DROP VIEW IF EXISTS `vista_reporte_ips_completo`;
CREATE VIEW vista_reporte_ips_completo AS
SELECT * 
FROM vista_reporte_ips 
UNION ALL
SELECT * 
FROM vista_cruce_cartera_eps_sin_relacion_segun_ags;

DROP VIEW IF EXISTS `vista_cruce_totales_actas_conciliaciones`;
CREATE VIEW vista_cruce_totales_actas_conciliaciones AS 
SELECT t1.NumeroFactura,t1.Diferencia,MesServicio,
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
WHERE Diferencia<>0 
AND 
EXISTS (SELECT 1 FROM ts_eps.actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato);