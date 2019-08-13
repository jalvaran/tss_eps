DROP VIEW IF EXISTS `vista_cruce_cartera_eps_no_relacionadas_ips_completa`;
CREATE VIEW vista_cruce_cartera_eps_no_relacionadas_ips_completa AS 
SELECT *
FROM vista_cruce_cartera_eps t1
WHERE NOT EXISTS (SELECT 1 FROM carteracargadaips t2 WHERE t1.NumeroFactura=t2.NumeroFactura);