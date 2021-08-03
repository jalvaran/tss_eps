DROP TABLE IF EXISTS `facturas_para_revision_por_ceros_izquierda`;
CREATE TABLE facturas_para_revision_por_ceros_izquierda AS
SELECT t1.ID,t1.TipoOperacion,t1.NumeroOperacion,t1.FechaFactura,t1.NumeroFactura,t1.MesServicio,t1.ValorOriginal,t1.Descripcion,
(SELECT COUNT(DISTINCT NumeroFactura ) FROM historial_carteracargada_eps t2 WHERE t2.ValidaFactura=t1.ValidaFactura ) AS TotalRepetidas 
FROM historial_carteracargada_eps t1 WHERE t1.ValidaFactura > 0 ORDER BY ValidaFactura;


SELECT count(DISTINCT NumeroFactura,NumeroRadicado ) as total_facturas 
FROM historial_carteracargada_eps WHERE NumeroContrato='CAQ-059-S18'
AND t1.MesServicio>=((select MIN(mes_servicio) FROM auditoria_anexo_aly_evento t2 WHERE t2.contrato='CAQ-059-S18')) 
AND t1.MesServicio<=((select MAX(mes_servicio) FROM auditoria_anexo_aly_evento t2 WHERE t2.contrato='CAQ-059-S18')) 
;

SELECT t1.NumeroFactura,t1.NumeroRadicado
       

 FROM  historial_carteracargada_eps t1 
WHERE t1.NumeroContrato='CAQ-059-S18' 
AND t1.MesServicio>=((select MIN(mes_servicio) FROM auditoria_anexo_aly_evento t2 WHERE t2.contrato='CAQ-059-S18')) 
AND t1.MesServicio<=((select MAX(mes_servicio) FROM auditoria_anexo_aly_evento t2 WHERE t2.contrato='CAQ-059-S18')) 
 AND NOT EXISTS (SELECT 1 FROM auditoria_anexo_aly_evento t2 WHERE t2.contrato='CAQ-059-S18' AND t1.NumeroFactura=t2.Factura AND t1.NumeroRadicado=t2.radicado)

