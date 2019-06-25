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
OR notas_db_cr_2.TipoOperacion='2225' OR notas_db_cr_2.TipoOperacion='2214' GROUP BY NumeroFactura;

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


