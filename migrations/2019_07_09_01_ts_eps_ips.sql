DROP VIEW IF EXISTS `vista_ultimas_facturas_cartera_eps`;
CREATE VIEW vista_ultimas_facturas_cartera_eps AS
 
SELECT DISTINCT(NumeroFactura),FechaFactura,NumeroRadicado,MesServicio,ValorOriginal 
FROM historial_carteracargada_eps WHERE TipoOperacion LIKE '20%'
ORDER BY FechaFactura DESC;