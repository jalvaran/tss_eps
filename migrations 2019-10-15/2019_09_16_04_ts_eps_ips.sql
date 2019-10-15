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
        (SELECT IF(  ValorSegunIPS <> ValorDocumento,Diferencia,0)) as DiferenciaXValorFacturado,        

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

FROM hoja_de_trabajo t1
WHERE Diferencia<>0;