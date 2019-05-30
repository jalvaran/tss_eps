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
        t2.ValorMenosImpuestos,
        (SELECT SUM(ValorTranferido) FROM pagos_asmet WHERE pagos_asmet.NumeroFactura=t1.NumeroFactura ) AS TotalPagos,
        (SELECT SUM(ValorAnticipado) FROM anticipos_asmet WHERE anticipos_asmet.NumeroFactura=t1.NumeroFactura ) AS TotalAnticipos,
        (SELECT SUM(ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ) AS TotalGlosaInicial,
        (SELECT SUM(ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ) AS TotalGlosaFavor,
        (SELECT SUM(ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t1.NumeroFactura ) AS TotalGlosaContra
FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura;

DROP VIEW IF EXISTS `vista_facturas_pagadas_no_relacionadas`;
CREATE VIEW vista_facturas_pagadas_no_relacionadas AS 
SELECT * FROM pagos_asmet t1
WHERE NOT EXISTS (SELECT 1 FROM carteraeps t2 WHERE t1.NumeroFactura=t2.NumeroFactura);
