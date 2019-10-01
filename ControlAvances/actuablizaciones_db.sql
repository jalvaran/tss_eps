INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(14,	'Cargar Egresos',	12,	5,	2,	'',	1,	'',	'cargar_egresos.php',	'_SELF',	1,	'egresoitems.png',	4,	'2019-06-07 19:16:15',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(15,	'Cargar Archivo de Anticipos EPS',	12,	5,	2,	'',	1,	'',	'cargar_anticipos.php',	'_SELF',	1,	'Anticipos2.png',	5,	'2019-05-27 17:55:48',	'0000-00-00 00:00:00');

INSERT INTO `menu` (`ID`, `Nombre`, `idCarpeta`, `Pagina`, `Target`, `Estado`, `Image`, `CSS_Clase`, `Orden`, `Updated`, `Sync`) VALUES
(6,	'Envío de Resoluciones de Glosas',	1,	'MnuResolucionesGlosas.php',	'_BLANK',	1,	'radicar.jpg',	'fa fa-share',	5,	'2019-06-01 14:23:19',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(16,	'Cargar Radicados Sin Enviar',	50,	10,	6,	'',	1,	'',	'CargarResolucionesSinEnviar.php',	'_SELF',	1,	'resolucion.png',	1,	'2019-06-19 08:28:56',	'0000-00-00 00:00:00');

INSERT INTO `menu_pestanas` (`ID`, `Nombre`, `idMenu`, `Orden`, `Estado`, `Updated`, `Sync`) VALUES
(51,	'Reportes',	6,	2,	CONV('1', 2, 10) + 0,	'2019-01-13 09:12:43',	'2019-01-12 09:12:43'),
(50,	'Archivos',	6,	1,	CONV('1', 2, 10) + 0,	'2019-01-13 09:12:43',	'2019-01-12 09:12:43');

INSERT INTO `menu_carpetas` (`ID`, `Ruta`, `Updated`, `Sync`) VALUES
(10,	'../modulos/envioresolucionesglosas/',	'2019-05-20 09:17:15',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(17,	'Cargar Contratos Liquidados',	50,	10,	6,	'',	1,	'',	'CargarReporteContratoLiquidacion.php',	'_SELF',	1,	'acta.png',	2,	'2019-06-19 08:28:56',	'0000-00-00 00:00:00'),
(18,	'Administrador de Radicados Glosas',	50,	10,	6,	'',	1,	'',	'adminResolucionesGlosas.php',	'_SELF',	1,	'admin.png',	3,	'2019-06-19 08:28:56',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(19,	'Cargar Archivo de Glosas IDRA',	50,	10,	6,	'',	1,	'',	'CargarGlosasIDRA.php',	'_SELF',	1,	'facturar.png',	2,	'2019-06-19 08:28:56',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(20,	'Crear IPS',	1,	3,	1,	'ips',	0,	'',	'ips.php',	'_BLANK',	1,	'eps.png',	2,	'2019-05-20 10:10:26',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(21,	'Cargar Archivo de Impuestos y Retenciones EPS',	12,	5,	2,	'',	1,	'',	'cargar_impuestos.php',	'_SELF',	1,	'impuestos.png',	9,	'2019-07-09 10:46:56',	'0000-00-00 00:00:00');

CREATE TABLE `control_cargue_contratos_liquidados` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NombreArchivo` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `Extension` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `Ruta` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `Estado` int(11) NOT NULL,
  `idUser` bigint(20) NOT NULL,
  `FechaRegistro` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `registro_liquidacion_contratos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NitIPS` bigint(20) NOT NULL,
  `RazonSocialIPS` varchar(120) COLLATE utf8_spanish_ci NOT NULL,
  `Contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `VigenciaInicial` date NOT NULL,
  `VigenciaFinal` date NOT NULL,
  `ValorContrato` double NOT NULL,
  `ValorPagado` double NOT NULL,
  `Modalidad` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL,
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `NombreArchivo` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `BaseDatos` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `EstadoCarga` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `NitIPS` (`NitIPS`),
  KEY `Contrato` (`Contrato`),
  KEY `Modalidad` (`Modalidad`),
  KEY `BaseDatos` (`BaseDatos`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `menu_carpetas` (`ID`, `Ruta`, `Updated`, `Sync`) VALUES
(11,	'../modulos/contratosliquidados/',	'2019-05-20 09:17:15',	'0000-00-00 00:00:00');

INSERT INTO `menu_pestanas` (`ID`, `Nombre`, `idMenu`, `Orden`, `Estado`, `Updated`, `Sync`) VALUES
(52,	'Conciliaciones y Liquidaciones',	3,	2,	CONV('1', 2, 10) + 0,	'2019-05-23 07:51:15',	'2019-01-13 09:12:43');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(28,	'Cargar Contratos Liquidaciones',	52,	11,	3,	'',	1,	'',	'cargar_contrato_liquidado.php',	'_SELF',	1,	'cargar.png',	2,	'2019-08-13 09:53:47',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(29,	'Historial de Contratos Liquidados',	52,	11,	3,	'',	1,	'',	'historial_contratos_liquidados.php',	'_SELF',	1,	'historial.png',	1,	'2019-08-13 09:53:47',	'0000-00-00 00:00:00');


ALTER TABLE `actas_conciliaciones` ADD `FechaInicial` DATE NOT NULL AFTER `ID`;
ALTER TABLE `actas_conciliaciones` ADD `MesServicioInicial` INT(6) NOT NULL AFTER `Estado`, ADD `MesServicioFinal` INT(6) NOT NULL AFTER `MesServicioInicial`;

ALTER TABLE `actas_conciliaciones_items` ADD `DescuentoBDUA` DOUBLE NOT NULL AFTER `DescuentoPGP`;

