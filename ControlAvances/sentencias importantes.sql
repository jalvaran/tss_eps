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
SET GLOBAL max_allowed_packet=524288000;