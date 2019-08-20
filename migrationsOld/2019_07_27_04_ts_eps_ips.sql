DROP VIEW IF EXISTS `vista_facturas_sr_eps_3`;
CREATE VIEW vista_facturas_sr_eps_3 AS 
SELECT *
    
 FROM vista_facturas_sr_eps_2 
WHERE Saldo<0 AND TotalDevoluciones<0;