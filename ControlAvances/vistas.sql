DROP VIEW IF EXISTS `vista_tickets`;
CREATE VIEW vista_tickets AS 
SELECT *,
    (SELECT Nombre FROM usuarios WHERE idUsuarios=t1.idUsuarioSolicitante) AS NombreSolicitante,
    (SELECT Apellido FROM usuarios WHERE idUsuarios=t1.idUsuarioSolicitante) AS ApellidoSolicitante,
    (SELECT Nombre FROM usuarios WHERE idUsuarios=t1.idUsuarioAsignado) AS NombreAsignado,
    (SELECT Apellido FROM usuarios WHERE idUsuarios=t1.idUsuarioAsignado) AS ApellidoAsignado,
    (SELECT Estado FROM tickets_estados t2 WHERE t2.ID=t1.Estado) AS NombreEstado, 
    (SELECT Prioridad FROM tickets_prioridad t2 WHERE t2.ID=t1.Prioridad) AS NombrePrioridad,
    (SELECT Proyecto FROM tickets_proyectos t2 WHERE t2.ID=t1.idProyecto) AS NombreProyecto,
    (SELECT NombreModulo FROM tickets_modulos_proyectos t2 WHERE t2.ID=t1.idModuloProyecto) AS NombreModulo,
    (SELECT TipoTicket FROM tickets_tipo t2 WHERE t2.ID=t1.TipoTicket) AS NombreTipoTicket
FROM tickets t1 ;
