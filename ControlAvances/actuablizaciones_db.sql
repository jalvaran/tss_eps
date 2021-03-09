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

INSERT INTO `menu_carpetas` (`ID`, `Ruta`, `Updated`, `Sync`) VALUES
(12,	'../modulos/actas/',	'2019-05-20 09:17:15',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(31,	'Actas de Liquidación',	13,	12,	4,	'',	1,	'',	'ActasLiquidacion.php',	'_SELF',	1,	'af.png',	3,	'2019-05-27 17:55:48',	'0000-00-00 00:00:00'),
(30,	'Actas de Conciliación',	13,	12,	4,	'',	1,	'',	'ActasConciliacion.php',	'_SELF',	1,	'acta.png',	2,	'2019-05-27 17:55:48',	'0000-00-00 00:00:00');

ALTER TABLE `actas_conciliaciones_items` ADD `NumeroDiasLMA` DOUBLE NOT NULL AFTER `NumeroFactura`, ADD `ValorAPagarLMA` DOUBLE NOT NULL AFTER `NumeroDiasLMA`, ADD `CodigoSucursal` DOUBLE NOT NULL AFTER `ValorAPagarLMA`;
ALTER TABLE `actas_liquidaciones_items` ADD `NumeroDiasLMA` DOUBLE NOT NULL AFTER `NumeroFactura`, ADD `ValorAPagarLMA` DOUBLE NOT NULL AFTER `NumeroDiasLMA`, ADD `CodigoSucursal` DOUBLE NOT NULL AFTER `ValorAPagarLMA`;


INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(32,	'Anticipos pendientes por legalizar',	12,	5,	2,	'',	1,	'',	'cargar_anticipos_pendientes.php',	'_SELF',	1,	'pago.jpg',	10,	'2019-07-30 14:56:51',	'0000-00-00 00:00:00');

INSERT INTO `configuracion_control_tablas` (`ID`, `TablaDB`, `Agregar`, `Editar`, `Ver`, `LinkVer`, `Exportar`, `AccionesAdicionales`, `Eliminar`, `Updated`, `Sync`) VALUES
(15,	'contratos',	1,	1,	0,	'',	1,	1,	0,	'2019-10-21 11:26:48',	'2019-01-13 09:04:48'),
(16,	'contrato_percapita',	1,	1,	0,	'',	1,	1,	0,	'2019-10-21 11:26:48',	'2019-01-13 09:04:48');


INSERT INTO `menu_carpetas` (`ID`, `Ruta`, `Updated`, `Sync`) VALUES
(13,	'../modulos/contratos/',	'2019-05-20 09:17:15',	'0000-00-00 00:00:00');

INSERT INTO `menu` (`ID`, `Nombre`, `idCarpeta`, `Pagina`, `Target`, `Estado`, `Image`, `CSS_Clase`, `Orden`, `Updated`, `Sync`) VALUES
(7,	'Contratos',	1,	'MnuContratos.php',	'_BLANK',	1,	'contrato.jpg',	'fa fa-share',	5,	'2019-06-27 10:12:45',	'0000-00-00 00:00:00');

INSERT INTO `menu_pestanas` (`ID`, `Nombre`, `idMenu`, `Orden`, `Estado`, `Updated`, `Sync`) VALUES
(53,	'Contratos',	7,	1,	CONV('1', 2, 10) + 0,	'2019-05-23 07:51:15',	'2019-01-13 09:12:43');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(33,	'Historial de Contratos',	53,	13,	7,	'contratos',	1,	'',	'contratos.php',	'_SELF',	1,	'historial.png',	1,	'2019-07-30 14:56:51',	'0000-00-00 00:00:00');

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(34,	'Historial de Contratos Percapita',	53,	13,	7,	'contrato_percapita',	1,	'',	'contratos_percapita.php',	'_SELF',	1,	'historial2.png',	2,	'2019-07-30 14:56:51',	'0000-00-00 00:00:00');


INSERT INTO `configuracion_general` (`ID`, `Descripcion`, `Valor`, `Updated`, `Sync`) VALUES
(25,	'Determina el metodo de envío de correo electrónico, 1 para php nativo (Windows), 2 para phpmailer (LINUX).',	'1',	'2019-12-12 13:13:50',	'0000-00-00 00:00:00'),
(24,	'Se configura el Correo que envía la factura electronica',	'technosolucionesfe@gmail.com',	'2019-12-11 22:09:23',	'0000-00-00 00:00:00'),
(23,	'Configura el cuerpo del mensaje a enviar en una factura electronica',	'Cordial Saludo @RazonSocial,<br><br>\r\n\r\nPor medio del presente nos permitimos enviarle su factura electronica @NumeroFactura.<br><br>\r\n\r\nQue tenga un excelente dia.\r\n ',	'2019-12-11 22:02:02',	'0000-00-00 00:00:00'),
(22,	'Configura el asunto a mostrar cuando se envia una factura electronica.',	'FACTURA ELECTRONICA',	'2019-12-17 06:24:48',	'0000-00-00 00:00:00');

CREATE TABLE `configuracion_correos_smtp` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SMTPSecure` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `Host` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Port` bigint(20) NOT NULL,
  `Username` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `Password` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `configuracion_correos_smtp` (`ID`, `SMTPSecure`, `Host`, `Port`, `Username`, `Password`, `Updated`, `Sync`) VALUES
(1,	'ssl',	'smtp.gmail.com',	465,	'technosolucionesfe@gmail.com',	'pirlo1985',	'2019-12-17 11:35:35',	'0000-00-00 00:00:00');

CREATE TABLE `empresa_cargos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NombreCargo` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `empresa_cargos` (`ID`, `NombreCargo`) VALUES
(1,	'GERENTE'),
(2,	'SUBGERENTE'),
(3,	'TECNICO FINANCIERO'),
(4,	'TECNICO EN LIQUIDACION Y CONCILIACION'),
(5,	'Auxiliar integral Administrativo de servicios'),
(6,	'APOYO AL PROCESO DE LIQUIDACIONES'),
(7,	'DIRECTORA ADMINISTRATIVA'),
(8,	'Asistente Administrativo y Financiero'),
(9,	'Técnico en Sistemas y asistente financiero'),
(10,	'Auxiliar de servicios Generales'),
(11,	'Auxiliar de servicios de seguridad'),
(12,	'PROFESIONAL EN AUDITORIA DE CUENTAS MEDICAS Y RECOBROS'),
(13,	'Asesor Jurídico'),
(14,	'Apoyo jurídico al proceso de auditoría.'),
(15,	'Ingeniero en sistemas de información'),
(16,	'Auxiliar de sistemas y soporte'),
(17,	'Auxiliar integral Administrativo de servicios'),
(18,	'Administrativo de Calidad'),
(19,	'Desarrollador del sistema de informacion para el proceso de liquidacion');


CREATE TABLE `empresa_nombres_procesos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NombreProceso` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `empresa_nombres_procesos` (`ID`, `NombreProceso`) VALUES
(1,	'DIRECCIONAMIENTO ESTRATÉGICO'),
(2,	'GESTIÓN DE LIQUIDACIÓN DE CONTRATOS'),
(3,	'GESTIÓN ADMINISTRATIVA Y FINANCIERA'),
(4,	'GESTIÓN JURÍDICA'),
(5,	'GESTIÓN DE TICS'),
(6,	'GESTIÓN DE TALENTO HUMANO-SST'),
(7,	'GESTION DE CALIDAD'),
(8,	'PROYECTO INFORMATICO');

ALTER TABLE `actas_liquidaciones` ADD `Asmet` INT(6) NOT NULL AFTER `TipoActaLiquidacion`;
ALTER TABLE `actas_liquidaciones` ADD INDEX(`Asmet`);
ALTER TABLE `actas_liquidaciones` ADD `FechaCompromisoPagoIPS` DATE NOT NULL AFTER `DocumentoReferencia`;
ALTER TABLE `actas_liquidaciones` ADD `FormaPagoIPS` INT(6) NOT NULL AFTER `FechaCompromisoPagoIPS`;


INSERT INTO `configuracion_tablas_acciones_adicionales` (`TablaDB`, `JavaScript`, `ClaseIcono`, `Titulo`, `Ruta`, `Target`, `Updated`, `Sync`)
SELECT 'contratos', 'onclick=adjuntos_contrato', 'fa fa-paperclip', 'Adjuntos', '#', '_SELF', '2019-04-06 14:49:13', '2019-04-06 14:49:13'
FROM `configuracion_tablas_acciones_adicionales`
WHERE ((`ID` = '8'));
