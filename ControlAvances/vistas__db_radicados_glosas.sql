
DROP VIEW IF EXISTS `vista_agrupacion_radicados`;
CREATE VIEW vista_agrupacion_radicados AS 
SELECT COUNT(NumeroFactura) AS TotalFacturas,NumeroRadicado,Nit_IPS,RazonSocial,NumeroContrato 
FROM resoluciones_glosas_revision_contrato_glosa GROUP BY NumeroRadicado;

DROP VIEW IF EXISTS `vista_coincidencia_radicados`;
CREATE VIEW vista_coincidencia_radicados AS 
SELECT * FROM vista_agrupacion_radicados 
WHERE EXISTS (SELECT 1 FROM resoluciones_glosas_radicados_sin_enviar WHERE resoluciones_glosas_radicados_sin_enviar.NumeroRadicado=vista_agrupacion_radicados.NumeroRadicado);


DROP VIEW IF EXISTS `vista_agrupacion_factura_contratos_liquidados`;
CREATE VIEW vista_agrupacion_factura_contratos_liquidados AS 
SELECT NumeroFactura,NumeroRadicado,Nit_IPS,RazonSocial,NumeroContrato,SUM(ValorGlosa) AS GlosaEnContratos,
SUM(ValorGlosaAFavorAsmet) as GlosaFavorAsmetEnContratos
FROM resoluciones_glosas_revision_contrato_glosa GROUP BY NumeroFactura,NumeroRadicado ORDER BY NumeroRadicado;

DROP VIEW IF EXISTS `vista_agrupacion_factura_hidra`;
CREATE VIEW vista_agrupacion_factura_hidra AS 
SELECT NumeroFactura,NumeroRadicado,(ValorGlosa) AS ValorGlosa,
SUM(ValorGlosaAfavorAsmet) as ValorGlosaAfavorAsmet

FROM resoluciones_glosas_idra GROUP BY NumeroFactura,NumeroRadicado ORDER BY NumeroRadicado;


DROP VIEW IF EXISTS `vista_comparacion_glosas_contratos_hidra_completa`;
CREATE VIEW vista_comparacion_glosas_contratos_hidra_completa AS 
SELECT t1.*,
(SELECT IFNULL((SELECT t1.GlosaEnContratos - t1.GlosaFavorAsmetEnContratos),0)) as GlosaContraAsmetContratos,
(SELECT IFNULL((SELECT ValorGlosa FROM vista_agrupacion_factura_hidra t2 WHERE t2.NumeroFactura=t1.NumeroFactura AND t2.NumeroRadicado=t1.NumeroRadicado),0)) as GlosaHIDRA,
(SELECT IFNULL((SELECT ValorGlosaAfavorAsmet FROM vista_agrupacion_factura_hidra t2 WHERE t2.NumeroFactura=t1.NumeroFactura AND t2.NumeroRadicado=t1.NumeroRadicado),0)) as GlosaFavorAsmetHIDRA,
(SELECT IFNULL((SELECT ValorGlosa-ValorGlosaAfavorAsmet FROM vista_agrupacion_factura_hidra t2 WHERE t2.NumeroFactura=t1.NumeroFactura AND t2.NumeroRadicado=t1.NumeroRadicado),0)) as GlosaContraAsmetHIDRA,
((SELECT t1.GlosaEnContratos)-(SELECT GlosaHIDRA)) AS DiferenciaValorGlosa,
((SELECT t1.GlosaFavorAsmetEnContratos)-(SELECT GlosaFavorAsmetHIDRA)) AS DiferenciaGlosaFavor,
((SELECT GlosaContraAsmetContratos)-(SELECT GlosaContraAsmetHIDRA)) AS DiferenciaGlosaContra
FROM vista_agrupacion_factura_contratos_liquidados t1;



DROP VIEW IF EXISTS `vista_comparacion_glosas_contratos_hidra`;
CREATE VIEW vista_comparacion_glosas_contratos_hidra AS 
SELECT *,(SELECT NumeroRadicado FROM resoluciones_glosas_radicados_sin_enviar t2 WHERE t1.NumeroRadicado=t2.NumeroRadicado ) as NumeroRadicadoEnviadoPorAsmet
FROM vista_comparacion_glosas_contratos_hidra_completa t1 WHERE DiferenciaValorGlosa<>0 OR  DiferenciaGlosaFavor<>0 OR DiferenciaGlosaContra<>0;


