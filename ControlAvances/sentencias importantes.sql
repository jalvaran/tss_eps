--Deshacer el nombramiento de un contrato

UPDATE carteraeps t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='822006595' AND t2.ContratoNuevo='SIN CONTRATO 2010 - 2011';

UPDATE actas_conciliaciones_items t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='822006595' AND t2.ContratoNuevo='SIN CONTRATO 2010 - 2011';

UPDATE historial_carteracargada_eps t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='822006595' AND t2.ContratoNuevo='SIN CONTRATO 2010 - 2011';


UPDATE hoja_de_trabajo t1 INNER JOIN ts_eps.registra_ediciones_contratos t2 ON t2.NumeroFactura=t1.NumeroFactura 
SET t1.NumeroContrato=t2.ContratoAnterior 
WHERE t2.NIT_IPS='822006595' AND t2.ContratoNuevo='SIN CONTRATO 2010 - 2011';


SELECT count(t1.ID) AS Total,
 (SELECT t2.Nombre FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser) as Nombre, 
 (SELECT t2.Apellido FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser) as Apellido 
FROM log_pages_visits t1 GROUP BY idUser ORDER BY Total DESC;


SHOW VARIABLES LIKE 'max_allowed_packet';
SET GLOBAL max_allowed_packet=9524288000;


DELETE t1 FROM notas_db_cr_2 t1
INNER JOIN notas_db_cr_2 t2 
WHERE t1.ID > t2.ID AND t1.TipoOperacion = t2.TipoOperacion AND t1.NumeroTransaccion=t2.NumeroTransaccion 
AND t1.FechaTransaccion=t2.FechaTransaccion AND t1.NumeroFactura=t2.NumeroFactura AND t1.ValorTotal=t2.ValorTotal 
AND t1.TipoOperacion2=t2.TipoOperacion2 AND t1.NumeroOrdenPago=t2.NumeroOrdenPago AND t1.NumeroAutorizacion=t2.NumeroAutorizacion;


DELETE t1 FROM historial_carteracargada_eps t1
INNER JOIN historial_carteracargada_eps t2 
WHERE t1.ID > t2.ID AND t1.NumeroFactura= t2.NumeroFactura AND t1.NumeroRadicado= t2.NumeroRadicado; 

