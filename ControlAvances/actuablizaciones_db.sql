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


