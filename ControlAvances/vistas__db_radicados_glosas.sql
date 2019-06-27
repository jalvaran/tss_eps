
DROP VIEW IF EXISTS `vista_agrupacion_radicados`;
CREATE VIEW vista_agrupacion_radicados AS 
SELECT COUNT(NumeroFactura) AS TotalFacturas,NumeroRadicado,Nit_IPS,RazonSocial,NumeroContrato FROM resoluciones_glosas_revision_contrato_glosa GROUP BY NumeroRadicado;

DROP VIEW IF EXISTS `vista_coincidencia_radicados`;
CREATE VIEW vista_coincidencia_radicados AS 
SELECT * FROM vista_agrupacion_radicados WHERE EXISTS (SELECT 1 FROM resoluciones_glosas_radicados_sin_enviar WHERE resoluciones_glosas_radicados_sin_enviar.NumeroRadicado=vista_agrupacion_radicados.NumeroRadicado);


