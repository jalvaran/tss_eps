DROP VIEW IF EXISTS `vista_copagos_asmet`;
CREATE VIEW vista_copagos_asmet AS 
SELECT NumeroFactura,SUM(ABS(ValorTotal)) AS ValorTotal FROM notas_db_cr_2 WHERE EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion=t1.TipoOperacion AND Aplicacion='copagos') AND (C13<>'N') GROUP BY NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS
SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,t1.NoRelacionada,
        t2.CodigoSucursal,t2.NumeroOperacion,t2.CarteraEPSTipoNegociacion as TipoNegociacion,
        
        (SELECT IFNULL((t2.NumeroFactura REGEXP ('^[0-9]+$')),'0')  ) AS FacturaNumerica,
        (SELECT IF(FacturaNumerica=1, CONVERT(t2.NumeroFactura, SIGNED INTEGER) , '0' )  ) AS FacturaEnNumero,
        (SELECT IF(FacturaNumerica=1, (SELECT COUNT(DISTINCT NumeroFactura) FROM historial_carteracargada_eps WHERE historial_carteracargada_eps.ValidaFactura=( SELECT FacturaEnNumero) ),0 )  ) AS FacturasConCerosIzquierda,

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

        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT COUNT(NumeroFactura) FROM carteraeps ce WHERE ce.NumeroContrato= t2.NumeroContrato AND ce.MesServicio= t2.MesServicio AND ce.CarteraEPSTipoNegociacion='CAPITA' AND ce.CodigoSucursal=t2.CodigoSucursal) ,
                                  1)) AS DivisorMesServicio,    
        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT SUM(NumeroAfiliadosPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio),
                                  0)) AS NumeroAfiliadosLMA,
        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT SUM(DiasLiquidadosSubsidioPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio)/(SELECT DivisorMesServicio) ,
                                  0)) AS NumeroDiasLMA,
        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT (ValorPercapitaXDia) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                  0)) AS ValorPercapita, 
        (SELECT IF(TipoNegociacion='CAPITA',
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

        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) + (SELECT ConciliacionEPSXPagos) - (SELECT ConciliacionesAFavorIPS) ) ) AS TotalPagos,
        
        (SELECT IF( (SELECT TipoNegociacion)='CAPITA', ((SELECT ValorAPagarLMA)-(t2.ValorOriginal)),0)) AS DescuentoReconocimientoBDUA,
        
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='pgp') ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS FacturasDevueltasAnticipos,

        
        (SELECT IFNULL((SELECT COUNT((NumeroFactura)) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS NumeroFacturasDevueltasAnticipos,
	  
	(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXCopagos) + (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='copagos') ),0)) ) AS TotalCopagos,
        
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
        
	(t2.ValorOriginal - (SELECT Impuestos) - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) + (SELECT DescuentoReconocimientoBDUA) ) AS ValorSegunEPS,
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

        
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura WHERE t2.Estado<2;

DROP VIEW IF EXISTS `vista_cruce_cartera_eps`;
CREATE VIEW vista_cruce_cartera_eps AS
SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,(SELECT NoRelacionada FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as NoRelacionada,
         t2.CodigoSucursal,t2.NumeroOperacion,t2.CarteraEPSTipoNegociacion as TipoNegociacion,
        
        (SELECT IFNULL((t2.NumeroFactura REGEXP ('^[0-9]+$')),'0')  ) AS FacturaNumerica,
        (SELECT IF(FacturaNumerica=1, CONVERT(t2.NumeroFactura, SIGNED INTEGER) , '0' )  ) AS FacturaEnNumero,
        (SELECT IF(FacturaNumerica=1, (SELECT COUNT(DISTINCT NumeroFactura) FROM historial_carteracargada_eps WHERE historial_carteracargada_eps.ValidaFactura=( SELECT FacturaEnNumero) ),0 )  ) AS FacturasConCerosIzquierda,

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

        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT COUNT(NumeroFactura) FROM carteraeps ce WHERE ce.NumeroContrato= t2.NumeroContrato AND ce.MesServicio= t2.MesServicio AND ce.CarteraEPSTipoNegociacion='CAPITA' AND ce.CodigoSucursal=t2.CodigoSucursal) ,
                                  1)) AS DivisorMesServicio,    
        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT SUM(NumeroAfiliadosPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio),
                                  0)) AS NumeroAfiliadosLMA,
        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT SUM(DiasLiquidadosSubsidioPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio)/(SELECT DivisorMesServicio) ,
                                  0)) AS NumeroDiasLMA,
        (SELECT IF(TipoNegociacion='CAPITA',
                                 (SELECT (ValorPercapitaXDia) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                  0)) AS ValorPercapita, 
        (SELECT IF(TipoNegociacion='CAPITA',
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

        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) + (SELECT ConciliacionEPSXPagos) - (SELECT ConciliacionesAFavorIPS) ) ) AS TotalPagos,
        
        (SELECT IF( (SELECT TipoNegociacion)='CAPITA', ((SELECT ValorAPagarLMA)-(t2.ValorOriginal)),0)) AS DescuentoReconocimientoBDUA,
        
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='pgp') ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS FacturasDevueltasAnticipos,

        
        (SELECT IFNULL((SELECT COUNT((NumeroFactura)) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS NumeroFacturasDevueltasAnticipos,
	  
	(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXCopagos) + (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='copagos') ),0)) ) AS TotalCopagos,
        
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
        
	(t2.ValorOriginal - (SELECT Impuestos) - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) + (SELECT DescuentoReconocimientoBDUA) ) AS ValorSegunEPS,
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

FROM carteraeps t2 WHERE t2.Estado<2;

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_no_relacionadas_ips`;
CREATE VIEW vista_cruce_cartera_eps_no_relacionadas_ips AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura) AND ValorSegunEPS<0;

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_relacionadas_ips`;
CREATE VIEW vista_cruce_cartera_eps_relacionadas_ips AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_no_relacionadas_ips_completa`;
CREATE VIEW vista_cruce_cartera_eps_no_relacionadas_ips_completa AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_cruce_cartera_eps_sin_relacion_segun_ags`;
CREATE VIEW vista_cruce_cartera_eps_sin_relacion_segun_ags AS 
SELECT 
        t2.FechaRadicado as FechaFactura ,
        t2.TipoNegociacion ,
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
        t2.DescuentoReconocimientoBDUA ,

        t2.NumeroDiasLMA ,
        t2.ValorAPagarLMA ,
        t2.CodigoSucursal ,
        t2.NumeroOperacion ,
        t2.ImpuestosPorRecuperar ,

        '0' AS ValorSegunEPS ,
        '0' AS ValorSegunIPS ,
        '0' as Diferencia

FROM vista_cruce_cartera_eps t2 
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t1 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_reporte_ips`;
CREATE VIEW vista_reporte_ips AS
SELECT  t2.FechaFactura ,
        t2.TipoNegociacion ,
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
        t2.DescuentoReconocimientoBDUA ,
        
        t2.NumeroDiasLMA ,
        t2.ValorAPagarLMA ,
        t2.CodigoSucursal ,
        t2.NumeroOperacion ,
        t2.ImpuestosPorRecuperar ,

        t2.AjustesCartera ,
        t2.TotalGlosaInicial ,
        t2.TotalGlosaFavor ,
        t2.TotalGlosaContra ,
		t2.GlosaXConciliar ,  
		t2.TotalDevoluciones ,
        t2.ValorSegunEPS ,
        t2.ValorSegunIPS ,
		t2.Diferencia 
FROM hoja_de_trabajo t2;

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

DROP VIEW IF EXISTS `vista_cruce_diferencias`;
CREATE VIEW vista_cruce_diferencias AS 
SELECT t1.NumeroFactura,t1.Diferencia,MesServicio,
        (SELECT IF( ( ABS((TotalPagos))) = ( ABS(Diferencia)),Diferencia,0)) as DiferenciaXPagos,
        (SELECT IF( (SELECT DiferenciaXPagos)=0, IF( ( ABS(TotalAnticipos)) = ( ABS(Diferencia)),Diferencia,0),0) ) as DiferenciaXAnticipos,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)=0, IF( ( ABS(TotalCopagos)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXCopagos,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)=0, IF( ( ABS(DescuentoPGP)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXDescuentoPGP,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)=0, IF( ( ABS(OtrosDescuentos)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXOtrosDescuentos,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)+(SELECT DiferenciaXOtrosDescuentos)=0, IF( ( ABS(AjustesCartera)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXAjustesCartera,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)+(SELECT DiferenciaXOtrosDescuentos)+(SELECT DiferenciaXAjustesCartera)=0, IF( ( ABS(TotalGlosaFavor)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXGlosaFavorEPS,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)+(SELECT DiferenciaXOtrosDescuentos)+(SELECT DiferenciaXAjustesCartera)+(SELECT DiferenciaXGlosaFavorEPS)=0, IF( ( ABS(TotalGlosaContra)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXGlosaContraEPS,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)+(SELECT DiferenciaXOtrosDescuentos)+(SELECT DiferenciaXAjustesCartera)+(SELECT DiferenciaXGlosaFavorEPS)+(SELECT DiferenciaXGlosaContraEPS)=0, IF( ( ABS(GlosaXConciliar)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXGlosaXConciliar,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)+(SELECT DiferenciaXOtrosDescuentos)+(SELECT DiferenciaXAjustesCartera)+(SELECT DiferenciaXGlosaFavorEPS)+(SELECT DiferenciaXGlosaContraEPS)+(SELECT DiferenciaXGlosaXConciliar)=0, IF( ( ABS(TotalDevoluciones)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXDevoluciones,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)+(SELECT DiferenciaXOtrosDescuentos)+(SELECT DiferenciaXAjustesCartera)+(SELECT DiferenciaXGlosaFavorEPS)+(SELECT DiferenciaXGlosaContraEPS)+(SELECT DiferenciaXGlosaXConciliar)+(SELECT DiferenciaXDevoluciones)=0, IF( ( ABS(Impuestos)) = ( ABS(Diferencia)),Diferencia,0),0)) as DiferenciaXImpuestos,
        (SELECT IF( (SELECT DiferenciaXPagos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXAnticipos)+(SELECT DiferenciaXCopagos)+(SELECT DiferenciaXDescuentoPGP)+(SELECT DiferenciaXOtrosDescuentos)+(SELECT DiferenciaXAjustesCartera)+(SELECT DiferenciaXGlosaFavorEPS)+(SELECT DiferenciaXGlosaContraEPS)+(SELECT DiferenciaXGlosaXConciliar)+(SELECT DiferenciaXDevoluciones)+(SELECT DiferenciaXDevoluciones)=0, IF(  ValorSegunIPS > ValorDocumento,Diferencia,0),0)) as DiferenciaXValorFacturado,        

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
       
       (SELECT IF( (SELECT TotalDiferenciasComunes)=0 AND (SELECT DiferenciaXDevolucionesNoIPS)=0,(Diferencia),0)) as DiferenciaVariada 

FROM hoja_de_trabajo t1
WHERE Diferencia<>0;