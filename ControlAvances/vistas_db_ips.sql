DROP VIEW IF EXISTS `vista_facturas_sr_ips`;
CREATE VIEW vista_facturas_sr_ips AS 
SELECT * FROM carteracargadaips t1
WHERE NOT EXISTS (SELECT 1 FROM carteraeps t2 WHERE t1.NumeroFactura=t2.NumeroFactura);


DROP VIEW IF EXISTS `vista_facturas_sr_eps`;
CREATE VIEW vista_facturas_sr_eps AS 
SELECT * FROM carteraeps t1
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura);


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t1.NumeroRadicado,t1.FechaRadicado,t2.NumeroContrato,t1.ValorDocumento,
        (SELECT IFNULL((SELECT MAX(ValorMenosImpuestos) FROM historial_carteracargada_eps WHERE historial_carteracargada_eps.NumeroFactura=t1.NumeroFactura),0)) AS ValorMenosImpuestos1,
        (t1.ValorDocumento-(SELECT ValorMenosImpuestos1)) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos_asmet WHERE anticipos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(Valor) FROM notas_dv_cr WHERE notas_dv_cr.NumeroFactura=t1.NumeroFactura AND notas_dv_cr.TipoOperacion='2258' ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT SUM(Valor) FROM notas_dv_cr WHERE notas_dv_cr.NumeroFactura=t1.NumeroFactura AND notas_dv_cr.TipoOperacion='2259' ),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t1.ValorDocumento-(SELECT Impuestos)- (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT TotalCopagos)-(SELECT TotalDevoluciones)) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia
        
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t1.NumeroRadicado,t1.FechaRadicado,t2.NumeroContrato,t1.ValorDocumento,
        t2.ValorMenosImpuestos,
        (t1.ValorDocumento-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos_asmet WHERE anticipos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(Valor) FROM notas_dv_cr WHERE notas_dv_cr.NumeroFactura=t1.NumeroFactura AND notas_dv_cr.TipoOperacion='2258' ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT SUM(Valor) FROM notas_dv_cr WHERE notas_dv_cr.NumeroFactura=t1.NumeroFactura AND notas_dv_cr.TipoOperacion='2259' ),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t1.ValorDocumento-(SELECT Impuestos)- (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT TotalCopagos)-(SELECT TotalDevoluciones)) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades

FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t1.NumeroRadicado,t1.FechaRadicado,t2.NumeroContrato,t1.ValorDocumento,
        t2.ValorMenosImpuestos,
        (t1.ValorDocumento-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos_asmet WHERE anticipos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2258' ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' ),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t1.ValorDocumento-(SELECT Impuestos)- (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT TotalCopagos)-(SELECT TotalDevoluciones)) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades

FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;



DROP VIEW IF EXISTS `vista_resumen_cruce_cartera_asmet`;
CREATE VIEW vista_resumen_cruce_cartera_asmet AS 
SELECT NumeroContrato,
SUM(ValorDocumento) AS TotalFacturas,
SUM(Impuestos) AS Impuestos,
SUM(ValorMenosImpuestos) AS TotalMenosImpuestos,
SUM(OtrosDescuentos) AS TotalOtrosDescuentos,
SUM(TotalPagos) AS TotalPagos,
SUM(TotalAnticipos) AS TotalAnticipos,
SUM(TotalGlosaInicial) AS TotalGlosaInicial,
SUM(TotalGlosaFavor) AS TotalGlosaFavor,
SUM(TotalGlosaContra) AS TotalGlosaContra,
SUM(GlosaXConciliar) AS TotalGlosaXConciliar,
SUM(TotalCopagos) AS TotalCopagos,
SUM(TotalDevoluciones) AS TotalDevoluciones,
SUM(ValorSegunEPS) AS ValorSegunEPS
FROM vista_cruce_cartera_asmet GROUP BY NumeroContrato;



DROP VIEW IF EXISTS `vista_facturas_pagadas_no_relacionadas`;
CREATE VIEW vista_facturas_pagadas_no_relacionadas AS 
SELECT * FROM pagos_asmet t1
WHERE NOT EXISTS (SELECT 1 FROM carteraeps t2 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t1.NumeroRadicado,t1.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM Anticipos2 WHERE Anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2258' ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' ),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades

FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;

DROP VIEW IF EXISTS `vista_copagos_asmet`;
CREATE VIEW vista_copagos_asmet AS 
SELECT NumeroFactura,SUM(ABS(ValorTotal)) AS ValorTotal FROM notas_db_cr_2 WHERE TipoOperacion='2258' 
OR notas_db_cr_2.TipoOperacion='2225' OR notas_db_cr_2.TipoOperacion='2214' OR notas_db_cr_2.TipoOperacion='2260' GROUP BY NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t1.NumeroRadicado,t1.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM Anticipos2 WHERE Anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' ),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades

FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,
        t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado ),'NO')) AS Pendientes,
        t2.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,t2.MesServicio,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089') ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades

FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;


DROP VIEW IF EXISTS `vista_pendientes`;
CREATE VIEW vista_pendientes AS 

SELECT 'Radicados',NumeroRadicado,SUM(Valor) as Total FROM radicadospendientes 
 WHERE EstadoAuditoria LIKE '%AUDITORIA%' AND 
EXISTS (SELECT 1 FROM vista_cruce_cartera_asmet WHERE vista_cruce_cartera_asmet.NumeroRadicado=radicadospendientes.NumeroRadicado LIMIT 1) 
GROUP BY NumeroRadicado
UNION 
SELECT 'Devoluciones',NumeroRadicado,SUM(Valor) as Total FROM devoluciones_pendientes
 WHERE NoEnviados > '0' AND 
EXISTS (SELECT 1 FROM vista_cruce_cartera_asmet WHERE vista_cruce_cartera_asmet.NumeroRadicado=devoluciones_pendientes.NumeroRadicado LIMIT 1) 
 GROUP BY NumeroRadicado 
UNION 
SELECT 'Notas',NumeroRadicado,SUM(Valor) as Total FROM notas_pendientes
 WHERE NoEnviados > '0' AND 
EXISTS (SELECT 1 FROM vista_cruce_cartera_asmet WHERE vista_cruce_cartera_asmet.NumeroRadicado=notas_pendientes.NumeroRadicado LIMIT 1) 
 GROUP BY NumeroRadicado 
UNION 
SELECT 'Copagos',NumeroRadicado,SUM(Valor) as Total FROM copagos_pendientes
 WHERE NoEnviados > '0' AND 
EXISTS (SELECT 1 FROM vista_cruce_cartera_asmet WHERE vista_cruce_cartera_asmet.NumeroRadicado=copagos_pendientes.NumeroRadicado LIMIT 1) 
 GROUP BY NumeroRadicado;


DROP VIEW IF EXISTS `vista_retenciones_facturas`;
CREATE VIEW vista_retenciones_facturas AS 

SELECT NumeroFactura, SUM(ValorDebito) as Debitos, SUM(ValorCredito) as Creditos FROM retenciones GROUP BY NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,
        t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado ),'NO')) AS Pendientes,
        t2.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,t2.MesServicio,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t1.NumeroFactura ),0)) AS ImpuestosSegunASMET,
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089') ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades

FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t2.Estado,
        
        t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado ),'NO')) AS Pendientes,
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,t2.MesServicio,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t1.NumeroFactura ),0)) AS ImpuestosSegunASMET,
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089') ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0)) AS TotalDevoluciones,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura ),0)) AS TotalConciliaciones,
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t2.Estado,
        
        t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado ),'NO')) AS Pendientes,
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,t2.MesServicio,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t1.NumeroFactura ),0)) AS ImpuestosSegunASMET,
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089') ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0)) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2086' LIMIT 1),0)) AS ValorRegistradoFacturas,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' LIMIT 1),0)) AS ValorRegistradoDevoluciones,
        ((SELECT ValorRegistradoFacturas) - (SELECT ABS(ValorRegistradoDevoluciones))) AS DiferenciaFacturasDevoluciones,
        (IF((SELECT DiferenciaFacturasDevoluciones)<>(SELECT ValorMenosImpuestos),'SI','NO')) AS PresentaDiferenciasContables,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos)-(SELECT ABS(TotalDevoluciones)) )) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura ),0)) AS TotalConciliaciones,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,
        ((SELECT ValorSegunEPS) + (SELECT ConciliacionesAFavorIPS) - (SELECT ConciliacionesAFavorEPS)) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t2.Estado,
        
        t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado ),'NO')) AS Pendientes,
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,t2.MesServicio,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t1.NumeroFactura ),0)) AS ImpuestosSegunASMET,
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089') ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
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

DROP VIEW IF EXISTS `vista_facturas_sr_eps_3`;
CREATE VIEW vista_facturas_sr_eps_3 AS 
SELECT *
    
 FROM vista_facturas_sr_eps_2 
WHERE Saldo<0 AND TotalDevoluciones<0;


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t2.Estado,
        
        t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado ),'NO')) AS Pendientes,
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


DROP VIEW IF EXISTS `vista_copagos_asmet`;
CREATE VIEW vista_copagos_asmet AS 
SELECT NumeroFactura,SUM(ABS(ValorTotal)) AS ValorTotal FROM notas_db_cr_2 WHERE TipoOperacion='2258' 
OR notas_db_cr_2.TipoOperacion='2225' OR notas_db_cr_2.TipoOperacion='2214' OR notas_db_cr_2.TipoOperacion='2260' GROUP BY NumeroFactura;



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
        (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3070' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089' OR notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='2260') ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaContra,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalCopagos,
        (SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039') AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0)) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2086'  LIMIT 1),0)) AS ValorRegistradoFacturas,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' LIMIT 1),0)) AS ValorRegistradoDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t1.NumeroFactura AND notas_db_cr_2.TipoOperacion='2275' ),0)) AS DescuentoPGP,
        ((SELECT ValorRegistradoFacturas) - (SELECT ABS(ValorRegistradoDevoluciones))) AS DiferenciaFacturasDevoluciones,
        (IF((SELECT DiferenciaFacturasDevoluciones)<>(SELECT ValorMenosImpuestos),'SI','NO')) AS PresentaDiferenciasContables,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones)-(SELECT ABS(DescuentoPGP)))) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
        (SELECT IFNULL((SELECT (ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t1.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura ),0)) AS TotalConciliaciones,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t1.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,
        ((SELECT ValorSegunEPS) + (SELECT ConciliacionesAFavorIPS) - (SELECT ConciliacionesAFavorEPS)) AS TotalAPagar, 
        (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;


CREATE TABLE `registro_liquidacion_contratos` (
    `ID` bigint(20) NOT NULL AUTO_INCREMENT,
    `NitIPS` bigint(20) NOT NULL,
    `RazonSocialIPS` varchar(120) COLLATE utf8_spanish_ci NOT NULL,
    `Contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
    `VigenciaInicial` date NOT NULL,
    `VigenciaFinal` date NOT NULL,
    `ValorContrato` double NOT NULL,
    `Modalidad` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
    `idUser` int(11) NOT NULL,
    `FechaRegistro` datetime NOT NULL,
    PRIMARY KEY (`ID`),
    KEY (`NitIPS`),
    KEY (`Contrato`),
    KEY (`Modalidad`)
    
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE `registro_liquidacion_contratos_items` (
    `ID` bigint(20) NOT NULL AUTO_INCREMENT,
    `idContrato` bigint(20) NOT NULL,
    `DepartamentoRadicacion` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
    `Radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
    `MesServicio` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
    `NumeroFactura` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
    `ValorFacturado` double NOT NULL,
    `ImpuestosRetencion` double NOT NULL,
    `Devolucion` double NOT NULL,
    `GlosaInicial` double NOT NULL,
    `GlosaFavorEPS` double NOT NULL,
    `NotasCopagos` double NOT NULL,
    `RecuperacionImpuestos` double NOT NULL,
    `OtrosDescuentos` double NOT NULL,
    `ValorPagado` double NOT NULL,
    `Saldo` double NOT NULL,
    `idUser` int(11) NOT NULL,
    `FechaRegistro` datetime NOT NULL,
    PRIMARY KEY (`ID`),
    KEY (`idContrato`),
    KEY (`Radicado`),
    KEY (`MesServicio`),
    KEY (`NumeroFactura`),
    KEY (`idUser`)
    
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `menu_pestanas` (`ID`, `Nombre`, `idMenu`, `Orden`, `Estado`, `Updated`, `Sync`) VALUES
(52,	'Conciliaciones y Liquidaciones',	3,	2,	CONV('1', 2, 10) + 0,	'2019-05-23 07:51:15',	'2019-01-13 09:12:43');


