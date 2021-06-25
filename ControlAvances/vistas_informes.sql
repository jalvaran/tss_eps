DROP VIEW IF EXISTS `vista_informe_conciliaciones`;
CREATE VIEW vista_informe_conciliaciones AS 
SELECT ID AS 'ACTA CONCILIACION',NIT_IPS AS NIT,RazonSocialIPS AS 'RAZON SOCIAL',
TipoActa AS 'TIPO ACTA',MesServicioInicial AS 'MES INICIAL DE ACTA',MesServicioFinal AS 'MES FINAL DE ACTA',FechaFirma AS 'FECHA DE FIRMA' ,FechaRegistro AS 'FECHA DE REGISTRO',Updated AS 'FECHA DE ACTUALIZACION',
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios WHERE idUsuarios=idUser) as 'NOMBRE LIQUIDADOR',
(SELECT GROUP_CONCAT(NumeroContrato SEPARATOR ' | ') FROM actas_conciliaciones_contratos where actas_conciliaciones_contratos.idActaConciliacion=actas_conciliaciones.ID ) AS 'CONTRATOS', 
Estado AS 'ESTADO' 
FROM actas_conciliaciones ORDER BY idUser;

DROP VIEW IF EXISTS `vista_informe_liquidaciones`;
CREATE VIEW vista_informe_liquidaciones AS 
SELECT ID AS 'ACTA LIQUIDACION',IdentificadorActaEPS as 'IDENTIFICADOR ASMET',NIT_IPS AS NIT,RazonSocialIPS AS 'RAZON SOCIAL',
(SELECT Nombre FROM actas_liquidaciones_tipo WHERE actas_liquidaciones_tipo.ID=actas_liquidaciones.TipoActaLiquidacion) AS 'TIPO ACTA',MesServicioInicial AS 'MES INICIAL DE ACTA',MesServicioFinal AS 'MES FINAL DE ACTA',FechaFirma AS 'FECHA DE FIRMA' ,FechaRegistro AS 'FECHA DE REGISTRO', 
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios WHERE idUsuarios=idUser) as 'NOMBRE LIQUIDADOR',
Estado AS 'ESTADO',
(SELECT GROUP_CONCAT(idContrato SEPARATOR ' | ') FROM actas_liquidaciones_contratos where actas_liquidaciones_contratos.idActaLiquidacion=actas_liquidaciones.ID ) AS 'CONTRATOS'  
FROM actas_liquidaciones ORDER BY idUser;

DROP VIEW IF EXISTS `vista_informe_liquidaciones_tags`;
CREATE VIEW vista_informe_liquidaciones_tags AS 
SELECT ID ,IdentificadorActaEPS,NIT_IPS ,RazonSocialIPS ,
(SELECT Nombre FROM actas_liquidaciones_tipo WHERE actas_liquidaciones_tipo.ID=actas_liquidaciones.TipoActaLiquidacion) AS 'tipo_acta',
MesServicioInicial ,MesServicioFinal,FechaFirma ,FechaRegistro, 
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios WHERE idUsuarios=idUser) as 'nombre_liquidador',
Estado,
(SELECT GROUP_CONCAT(idContrato SEPARATOR ' | ') FROM actas_liquidaciones_contratos where actas_liquidaciones_contratos.idActaLiquidacion=actas_liquidaciones.ID ) AS 'contratos'  
FROM actas_liquidaciones ORDER BY idUser;

DROP VIEW IF EXISTS `vista_informe_tickets_tags`;
CREATE VIEW vista_informe_tickets_tags AS 
SELECT t1.*,
        ifnull((SELECT (t2.Created) FROM tickets_mensajes t2 WHERE t2.idTicket=t1.ID LIMIT 1,1),now()) as fecha_primer_respuesta,
        ifnull((SELECT max(t2.Created) FROM tickets_mensajes t2 WHERE t2.idTicket=t1.ID ),now()) as fecha_ultima_respuesta,
        (TIMESTAMPDIFF(SECOND,t1.FechaApertura,(select fecha_primer_respuesta) ) ) AS segundos_respuesta,
         ((SELECT segundos_respuesta)/3600) as horas_respuesta,
         (TIMESTAMPDIFF(SECOND,t1.FechaApertura,(select fecha_ultima_respuesta) ) ) AS segundos_ultima_respuesta,
         ((SELECT segundos_ultima_respuesta)/3600) as horas_ultima_respuesta,
         if(t1.Estado=10,(select fecha_ultima_respuesta),null) as fecha_cierre,
         (TIMESTAMPDIFF(SECOND,t1.FechaApertura,(select fecha_cierre) ) ) AS segundos_cierre,
         ((SELECT segundos_cierre)/3600) as horas_cierre,
         (SELECT Nombre FROM usuarios WHERE idUsuarios=t1.idUsuarioSolicitante) AS NombreSolicitante,
            (SELECT Apellido FROM usuarios WHERE idUsuarios=t1.idUsuarioSolicitante) AS ApellidoSolicitante,
            (SELECT Nombre FROM usuarios WHERE idUsuarios=t1.idUsuarioAsignado) AS NombreAsignado,
            (SELECT Apellido FROM usuarios WHERE idUsuarios=t1.idUsuarioAsignado) AS ApellidoAsignado,
            (SELECT Estado FROM tickets_estados t2 WHERE t2.ID=t1.Estado) AS NombreEstado, 
            (SELECT Prioridad FROM tickets_prioridad t2 WHERE t2.ID=t1.Prioridad) AS NombrePrioridad,
            (SELECT Proyecto FROM tickets_proyectos t2 WHERE t2.ID=t1.idProyecto) AS NombreProyecto,
            (SELECT NombreModulo FROM tickets_modulos_proyectos t2 WHERE t2.ID=t1.idModuloProyecto) AS NombreModulo,
            (SELECT TipoTicket FROM tickets_tipo t2 WHERE t2.ID=t1.TipoTicket) AS NombreTipoTicket,
         (SELECT GROUP_CONCAT(CONCAT(t3.Created,' ',t3.Mensaje) SEPARATOR ' | \n ') FROM tickets_mensajes t3 where t3.idTicket=t1.ID ) AS 'mensajes'  
         FROM tickets t1
         
        ;


DROP VIEW IF EXISTS `vista_informe_conciliaciones_tags`;
CREATE VIEW vista_informe_conciliaciones_tags AS 
SELECT ID ,NIT_IPS,RazonSocialIPS ,
TipoActa as tipo_acta ,MesServicioInicial,MesServicioFinal ,FechaFirma ,FechaRegistro ,Updated ,
(SELECT CONCAT(Nombre,' ',Apellido) FROM usuarios WHERE idUsuarios=idUser) as 'nombre_liquidador',
(SELECT GROUP_CONCAT(NumeroContrato SEPARATOR ' | ') FROM actas_conciliaciones_contratos where actas_conciliaciones_contratos.idActaConciliacion=actas_conciliaciones.ID ) AS 'contratos', 
Estado  
FROM actas_conciliaciones ORDER BY idUser;