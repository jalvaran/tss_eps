DROP VIEW IF EXISTS `vista_reporte_ips`;
CREATE VIEW vista_reporte_ips AS
SELECT  t2.FechaFactura AS 'Fecha Factura',
		t2.MesServicio AS 'Mes servicio',
        t2.NumeroRadicado AS 'Numero Radicado',
        t2.FechaRadicado AS 'Fecha Radicado',
		t2.NumeroContrato AS 'Numero Contrato',
		t2.NumeroFactura AS 'Numero factura',
		t2.ValorDocumento AS 'Valor de Factura',
        t2.Impuestos AS 'Impuestos Factura',
		t2.TotalPagos AS 'Pagos',
		t2.TotalAnticipos AS 'Anticipos',
        t2.TotalCopagos AS 'Copagos',
        t2.DescuentoPGP AS 'Descuentos PGP',
		t2.OtrosDescuentos AS 'Otros Descuentos',
        t2.AjustesCartera AS 'Ajustes Cartera',
        t2.TotalGlosaFavor AS 'Glosa Aceptada Ips',
        t2.TotalGlosaContra AS 'Glosa levantada EPS',
		t2.GlosaXConciliar AS 'Glosa X Conciliar',  
		t2.TotalDevoluciones AS 'Devoluciones',
        t2.ValorSegunEPS AS 'Saldo Eps',
        t2.ValorSegunIPS AS 'Saldo Ips',
		t2.Diferencia AS 'Valor Diferencia'
FROM vista_cruce_cartera_eps_relacionadas_ips t2 WHERE ValorIPSMenor='NO' AND Diferencia<>'0';