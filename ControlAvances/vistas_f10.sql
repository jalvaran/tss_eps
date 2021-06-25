DROP VIEW IF EXISTS `vista_f10`;
CREATE VIEW vista_f10 AS 
SELECT t1.*,
(SELECT nombre_estado FROM f10_estados t2 WHERE t2.ID=t1.estado) as nombre_estado,
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios t3 WHERE t3.idUsuarios=t1.ResponsableConciliacionCartera) as nombre_responsable_conciliacion,
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios t3 WHERE t3.idUsuarios=t1.ResponsableCargueActa) as nombre_responsable_cargue_acta,
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios t3 WHERE t3.idUsuarios=t1.ResponsableLiquidacion) as nombre_responsable_liquidacion,
(SELECT NombreCargo FROM empresa_cargos t4 WHERE t4.ID=t1.CargoResponsableLiquidacion) as nombre_cargo_responsable_liquidacion 

FROM f10 t1 ORDER BY ID DESC;

DROP VIEW IF EXISTS `vista_f10_control_cambios`;
CREATE VIEW vista_f10_control_cambios AS 
SELECT t1.*,
(SELECT nombre_estado FROM f10_estados t2 WHERE t2.ID=t1.estado) as nombre_estado,
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios t3 WHERE t3.idUsuarios=t1.ResponsableConciliacionCartera) as nombre_responsable_conciliacion,
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios t3 WHERE t3.idUsuarios=t1.ResponsableCargueActa) as nombre_responsable_cargue_acta,
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios t3 WHERE t3.idUsuarios=t1.ResponsableLiquidacion) as nombre_responsable_liquidacion,
(SELECT NombreCargo FROM empresa_cargos t4 WHERE t4.ID=t1.CargoResponsableLiquidacion) as nombre_cargo_responsable_liquidacion 

FROM f10_control_cambios t1 ORDER BY ID DESC;

