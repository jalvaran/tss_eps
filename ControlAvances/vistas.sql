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
SELECT t1.ID,t1.NumeroFactura,t1.FechaFactura,t1.NumeroRadicado,t1.FechaRadicado,t1.NumeroContrato,t1.ValorDocumento,
        t2.ValorMenosImpuestos,(t1.ValorDocumento-t2.ValorMenosImpuestos) as Impuestos,
        '0' as OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalPagos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos_asmet WHERE anticipos_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ),0)) AS TotalGlosaContra,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (t1.ValorDocumento-(SELECT Impuestos)- (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT OtrosDescuentos)) AS ValorSegunEPS,
        t1.ValorTotalpagar as ValorSegunIPS,((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia
        
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;

DROP VIEW IF EXISTS `vista_facturas_pagadas_no_relacionadas`;
CREATE VIEW vista_facturas_pagadas_no_relacionadas AS 
SELECT * FROM pagos_asmet t1
WHERE NOT EXISTS (SELECT 1 FROM carteraeps t2 WHERE t1.NumeroFactura=t2.NumeroFactura);

(SELECT IFNULL((SELECT SUM(ValorGlosado) FROM salud_glosas_iniciales WHERE salud_glosas_iniciales.num_factura=t1.num_factura AND salud_glosas_iniciales.EstadoGlosa<=7),0)) as ValorGlosaInicial,