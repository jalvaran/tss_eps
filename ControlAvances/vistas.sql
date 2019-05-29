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
SELECT * FROM carteracargadaips t1
WHERE EXISTS (SELECT 1 FROM carteraeps t2 WHERE t1.NumeroFactura=t2.NumeroFactura);