--Deshacer el nombramiento de un contrato

UPDATE carteraeps t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='800006850' AND t2.ContratoAnterior='SIN CONTRATO 2014' AND t2.ContratoNuevo='URGENCIAS 2014';

UPDATE actas_conciliaciones_items t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='800006850' AND t2.ContratoAnterior='SIN CONTRATO 2014' AND t2.ContratoNuevo='URGENCIAS 2014';


UPDATE historial_carteracargada_eps t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='800006850' AND t2.ContratoAnterior='SIN CONTRATO 2014' AND t2.ContratoNuevo='URGENCIAS 2014';

UPDATE hoja_de_trabajo t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='800006850' AND t2.ContratoAnterior='SIN CONTRATO 2014' AND t2.ContratoNuevo='URGENCIAS 2014';
