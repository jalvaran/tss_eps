DROP VIEW IF EXISTS `vista_cruce_cartera_asmet_completa`;
CREATE VIEW vista_cruce_cartera_asmet_completa AS
SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,
        t2.CodigoSucursal,t2.NumeroOperacion,
	(SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacion,
        (SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacionContrato,
        (SELECT NoRelacionada FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as NoRelacionada,
		
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=12),0)) as ConciliacionEPSXPagos1,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=15),0)) as ConciliacionEPSXPagos2,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=16),0)) as ConciliacionEPSXGlosas1,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=13),0)) as ConciliacionEPSXCopagos,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=14),0)) as ConciliacionEPSXImpuestos,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=25),0)) as ConciliacionEPSXGlosas2,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=26),0)) as ConciliacionEPSXDevolucion,
        
        ((SELECT ConciliacionEPSXPagos1) + (SELECT ConciliacionEPSXPagos2) ) AS ConciliacionEPSXPagos, 
        ((SELECT ConciliacionEPSXGlosas1) + (SELECT ConciliacionEPSXGlosas2) ) AS ConciliacionEPSXGlosas, 
        
        
        (SELECT Contrato FROM ts_eps.contratos c WHERE c.ContratoEquivalente=t2.NumeroContrato LIMIT 1) AS ContratoPadre,

        (SELECT IF(TipoNegociacionContrato='CAPITA',
                                 (SELECT COUNT(NumeroFactura) FROM carteraeps ce WHERE ce.NumeroContrato= t2.NumeroContrato AND ce.MesServicio= t2.MesServicio AND ce.CarteraEPSTipoNegociacion='CAPITA' AND ce.CodigoSucursal=t2.CodigoSucursal) ,
                                  1)) AS DivisorMesServicio,    
        (SELECT IF(TipoNegociacionContrato='CAPITA',
                                 (SELECT SUM(NumeroAfiliadosPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio),
                                  0)) AS NumeroAfiliadosLMA,
        (SELECT IF(TipoNegociacionContrato='CAPITA',
                                 (SELECT SUM(DiasLiquidadosSubsidioPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio)/(SELECT DivisorMesServicio) ,
                                  0)) AS NumeroDiasLMA,
        (SELECT IF(TipoNegociacionContrato='CAPITA',
                                 (SELECT (ValorPercapitaXDia) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                  0)) AS ValorPercapita, 
        (SELECT IF(TipoNegociacionContrato='CAPITA',
                                 (SELECT (PorcentajePoblacional) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                  0)) AS PorcentajePoblacional, 
                               
        (SELECT IFNULL((SELECT ROUND((SELECT NumeroDiasLMA) * (SELECT ValorPercapita) * ((SELECT PorcentajePoblacional)/100),2 )),0)) AS ValorAPagarLMA,
        
        (SELECT FechaFactura FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as FechaFactura,
        t2.MesServicio,
            t2.NumeroRadicado,
        
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,
		t2.NumeroContrato,
		t2.ValorOriginal as ValorDocumento,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as ImpuestosCalculados,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXImpuestos)) AS Impuestos,
		t2.ValorMenosImpuestos,
		(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')  AND (notas_db_cr_2.TipoOperacion!='2103') ),0)) AS TotalPagosNotas,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='Capitalizacion') ),0)) AS Capitalizacion,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,

        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) + (SELECT ConciliacionEPSXPagos)  ) ) AS TotalPagos,
        
        (SELECT IF( (SELECT TipoNegociacionContrato)='CAPITA', ((SELECT ValorAPagarLMA)-(t2.ValorOriginal)),0)) AS DescuentoReconocimientoBDUA,
        
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='pgp') ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS FacturasDevueltasAnticipos,

        
        (SELECT IFNULL((SELECT COUNT((NumeroFactura)) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS NumeroFacturasDevueltasAnticipos,
	  
	(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXCopagos)   ) AS CopagosEnNotas,
        
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='copagos') ),0)) as CopagosEnAnticipos,
        (SELECT IF( (SELECT CopagosEnNotas)>0,(SELECT CopagosEnNotas), (SELECT CopagosEnAnticipos) )  ) AS TotalCopagos,

        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='otrosdescuentos') ),0)) AS OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='ajustescartera') ),0)) AS AjustesCartera,
        
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS DevolucionesPresentadas,
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroRadicado) FROM historial_carteracargada_eps WHERE historial_carteracargada_eps.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND historial_carteracargada_eps.TipoOperacion=t1.TipoOperacion AND Aplicacion='FACTURA')  ),0)) AS FacturasPresentadas,
        (SELECT IF(((SELECT DevolucionesPresentadas ) >= ((SELECT FacturasPresentadas)) OR (SELECT NumeroFacturasDevueltasAnticipos ) >= ((SELECT FacturasPresentadas) ) ),'NO','SI')) AS FacturaActiva,

        (SELECT IF(FacturaActiva='SI',0, (SELECT Impuestos)   )) AS ImpuestosPorRecuperar,
         
        
        (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS TotalGlosaInicial,
        (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0) + (SELECT ConciliacionEPSXGlosas) ,0)) AS TotalGlosaFavor,
        (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS TotalGlosaContra,
        (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorPendienteResolver) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS GlosaXConciliar,

        (SELECT IF(FacturaActiva='SI',(0+(SELECT ConciliacionEPSXDevolucion)),(t2.ValorOriginal + (SELECT ConciliacionEPSXDevolucion) )) ) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        
	(t2.ValorOriginal - (SELECT Impuestos) - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) + (SELECT DescuentoReconocimientoBDUA) + (SELECT ConciliacionesAFavorIPS) ) AS ValorSegunEPS,
        (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,

        (SELECT IFNULL((SELECT 'SI' FROM radicadospendientes t4 WHERE EstadoAuditoria LIKE '%AUDITORIA%' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorRadicados,
        (SELECT IFNULL( (SELECT 'SI' FROM devoluciones_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorDevoluciones,
        (SELECT IFNULL( (SELECT 'SI' FROM notas_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorNotas,
        (SELECT IFNULL( (SELECT 'SI' FROM copagos_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorCopagos,
        
        
        ((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura ),0)) AS TotalConciliaciones,
        
        ((SELECT ValorSegunEPS)  ) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes,
        (SELECT IF( (SELECT ABS(TotalPagos)) = (SELECT ABS(Diferencia)),1,0)) as DiferenciaXPagos,
        '0' AS DiferenciaXPagosNoDescargados,
        '0' AS DiferenciaXGlosasPendientesXConciliar,
        '0' AS DiferenciaXFacturasDevueltas,
        '0' AS DiferenciaXDiferenciaXImpuestos,
        '0' AS DiferenciaXFacturasNoRelacionadasXIPS,
        '0' AS DiferenciaXAjustesDeCartera,
        '0' AS DiferenciaXValorFacturado,
        '0' AS DiferenciaXGlosasPendientesXDescargarIPS,
        '0' AS DiferenciaXDescuentoReconocimientoLMA,
        '0' AS DiferenciaVariada
        
        FROM carteraeps t2 WHERE EXISTS (SELECT 1 FROM carteracargadaips t1 WHERE t1.NumeroFactura=t2.NumeroFactura);
