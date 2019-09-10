DROP VIEW IF EXISTS `vista_consolidacion_contrato_liquidados`;
CREATE VIEW vista_consolidacion_contrato_liquidados AS 
SELECT idContrato  , SUM(ValorFacturado) AS ValorFacturado ,SUM(ImpuestosRetencion) AS ImpuestosRetencion,SUM(Devolucion) AS Devolucion,SUM(GlosaInicial) AS GlosaInicial,SUM(GlosaFavorEPS) AS GlosaFavorEPS,SUM(NotasCopagos) AS NotasCopagos,SUM(RecuperacionImpuestos) AS RecuperacionImpuestos,SUM(OtrosDescuentos) AS OtrosDescuentos,SUM(ValorPagado) AS ValorPagado,SUM(Saldo) AS Saldo
FROM registro_liquidacion_contratos_items GROUP BY  idContrato;