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

DROP VIEW IF EXISTS `vista_paginas_visitadas`;
CREATE VIEW vista_paginas_visitadas AS 
    SELECT COUNT(t1.ID) AS TotalVisitas, t1.Page, t1.idUser,
    (SELECT Nombre FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser LIMIT 1) AS NombreUsuario,
    (SELECT Apellido FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser LIMIT 1) AS ApellidoUsuario
FROM log_pages_visits t1 GROUP BY Page,idUser ORDER BY TotalVisitas DESC;
