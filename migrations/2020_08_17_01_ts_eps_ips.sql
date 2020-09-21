DROP VIEW IF EXISTS `vista_tickets`;
CREATE VIEW vista_tickets AS 
SELECT *,
    (SELECT Nombre FROM usuarios WHERE idUsuarios=t1.idUsuarioSolicitante) AS NombreSolicitante,
    (SELECT Apellido FROM usuarios WHERE idUsuarios=t1.idUsuarioSolicitante) AS ApellidoSolicitante,
    (SELECT Nombre FROM usuarios WHERE idUsuarios=t1.idUsuarioAsignado) AS NombreAsignado,
    (SELECT Apellido FROM usuarios WHERE idUsuarios=t1.idUsuarioAsignado) AS ApellidoAsignado,
    (SELECT Estado FROM tickets_estados t2 WHERE t2.ID=t1.Estado) AS NombreEstado, 
    (SELECT Prioridad FROM tickets_prioridad t2 WHERE t2.ID=t1.Prioridad) AS NombrePrioridad,
    (SELECT Proyecto FROM tickets_proyectos t2 WHERE t2.ID=t1.idProyecto) AS NombreProyecto,
    (SELECT NombreModulo FROM tickets_modulos_proyectos t2 WHERE t2.ID=t1.idModuloProyecto) AS NombreModulo,
    (SELECT TipoTicket FROM tickets_tipo t2 WHERE t2.ID=t1.TipoTicket) AS NombreTipoTicket
FROM tickets t1 ;

DROP VIEW IF EXISTS `vista_paginas_visitadas`;
CREATE VIEW vista_paginas_visitadas AS 
    SELECT COUNT(t1.ID) AS TotalVisitas, t1.Page, t1.idUser,
    (SELECT Nombre FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser LIMIT 1) AS NombreUsuario,
    (SELECT Apellido FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser LIMIT 1) AS ApellidoUsuario
FROM log_pages_visits t1 GROUP BY Page,idUser ORDER BY TotalVisitas DESC;


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
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
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
OR notas_db_cr_2.TipoOperacion='2225' OR notas_db_cr_2.TipoOperacion='2260' GROUP BY NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS 
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t1.NumeroRadicado,t1.FechaRadicado,t2.NumeroContrato,t2.ValorOriginal as ValorDocumento,
        t2.ValorMenosImpuestos,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
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

SELECT NumeroFactura, SUM(ValorDebito) as Debitos, SUM(ValorCredito) as Creditos FROM retenciones WHERE Cuentacontable LIKE '2365%' GROUP BY NumeroFactura;

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

DROP VIEW IF EXISTS `vista_copagos_asmet`;
CREATE VIEW vista_copagos_asmet AS 
SELECT NumeroFactura,SUM(ABS(ValorTotal)) AS ValorTotal FROM notas_db_cr_2 WHERE EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion=t1.TipoOperacion AND Aplicacion='copagos') AND (C13<>'N') GROUP BY NumeroFactura;

DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE VIEW vista_cruce_cartera_asmet AS
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
		(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')  AND (notas_db_cr_2.TipoOperacion!='2103' or notas_db_cr_2.TipoOperacion!='2117') ),0)) AS TotalPagosNotas,
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
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura  LIMIT 1),0)) AS CarteraXEdades,
        
	(t2.ValorOriginal - (SELECT Impuestos) - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) + (SELECT DescuentoReconocimientoBDUA) + (SELECT ConciliacionesAFavorIPS) ) AS ValorSegunEPS,
        (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,

        (SELECT IFNULL((SELECT 'SI' FROM radicadospendientes t4 WHERE EstadoAuditoria LIKE '%AUDITORIA%' AND EstadoAuditoria NOT LIKE '%PENDIENTE%'  AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorRadicados,
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
        
        FROM carteraeps t2 WHERE t2.Estado<2 AND EXISTS (SELECT 1 FROM carteracargadaips t1 WHERE t1.NumeroFactura=t2.NumeroFactura);

DROP VIEW IF EXISTS `vista_cruce_cartera_eps`;
CREATE VIEW vista_cruce_cartera_eps AS
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
		(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')  AND (notas_db_cr_2.TipoOperacion!='2103' or notas_db_cr_2.TipoOperacion!='2117') ),0)) AS TotalPagosNotas,
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
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura  LIMIT 1),0)) AS CarteraXEdades,
        
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

DROP VIEW IF EXISTS `vista_pagos_actas_liquidaciones`;
CREATE VIEW `vista_pagos_actas_liquidaciones` AS
SELECT t2.*, (SELECT t1.FechaFirma FROM ts_eps.actas_liquidaciones t1 WHERE t1.ID=t2.idActaLiquidacion LIMIT 1) AS FechaFirmaActa, 
        (SELECT t1.NIT_IPS FROM ts_eps.actas_liquidaciones t1 WHERE t1.ID=t2.idActaLiquidacion LIMIT 1) AS NIT_IPS, 
        (SELECT IF(t2.TotalDevoluciones=0,IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')),0) ,0)) AS TotalPagosPosteriores,
        (SELECT IFNULL((SELECT GROUP_CONCAT(ValorPago SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS ValoresPagados,
        (SELECT IFNULL((SELECT GROUP_CONCAT(FechaTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS FechasTransacciones,
        (SELECT IFNULL((SELECT GROUP_CONCAT(NumeroTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS Transacciones,
        (t2.ValorSegunEPS -(SELECT TotalPagosPosteriores) ) AS SaldoFinal
        
        FROM `actas_liquidaciones_items` t2 ;


DROP VIEW IF EXISTS `vista_pagos_actas_liquidaciones_manuales`;
CREATE VIEW `vista_pagos_actas_liquidaciones_manuales` AS
SELECT t2.*, (SELECT t1.FechaCorte FROM ts_eps.registro_liquidacion_contratos t1 WHERE t1.ID=t2.idContrato LIMIT 1) AS FechaFirmaActa,
        
        (SELECT IF(t2.Devolucion=0,IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')),0),0)) AS TotalPagosPosteriores,
        (SELECT IFNULL((SELECT GROUP_CONCAT(ValorPago SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS ValoresPagados,
        (SELECT IFNULL((SELECT GROUP_CONCAT(FechaTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS FechasTransacciones,
        (SELECT IFNULL((SELECT GROUP_CONCAT(NumeroTransaccion SEPARATOR ' | ') FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.FechaTransaccion>(SELECT FechaFirmaActa) AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos') ),0)) AS Transacciones,
        (t2.Saldo -(SELECT TotalPagosPosteriores) ) AS SaldoFinal
        
        FROM `registro_liquidacion_contratos_items` t2;