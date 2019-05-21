-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `alertas`;
CREATE TABLE `alertas` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `AlertaTipo` varchar(45) NOT NULL,
  `Mensaje` text NOT NULL,
  `Estado` int(11) NOT NULL,
  `TablaOrigen` varchar(100) NOT NULL,
  `idTabla` bigint(20) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `configuraciones_nombres_campos`;
CREATE TABLE `configuraciones_nombres_campos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NombreDB` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `Visualiza` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `configuracion_campos_asociados`;
CREATE TABLE `configuracion_campos_asociados` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TablaOrigen` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `CampoTablaOrigen` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `TablaAsociada` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `CampoAsociado` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `IDCampoAsociado` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `configuracion_campos_asociados` (`ID`, `TablaOrigen`, `CampoTablaOrigen`, `TablaAsociada`, `CampoAsociado`, `IDCampoAsociado`, `Updated`, `Sync`) VALUES
(1,	'empresapro',	'Ciudad',	'cod_municipios_dptos',	'Ciudad',	'Ciudad',	'2019-01-13 14:04:47',	'2019-01-13 09:04:47'),
(2,	'empresapro',	'Regimen',	'empresapro_regimenes',	'Regimen',	'Regimen',	'2019-01-13 14:04:47',	'2019-01-13 09:04:47'),
(3,	'comprobantes_ingreso',	'Clientes_idClientes',	'clientes',	'Num_Identificacion',	'idClientes',	'2019-02-24 19:01:51',	'2019-02-24 14:01:51'),
(4,	'productosventa',	'Departamento',	'prod_departamentos',	'Nombre',	'idDepartamentos',	'2019-02-24 19:01:51',	'2019-02-24 14:01:51'),
(5,	'productosventa',	'Sub1',	'prod_sub1',	'NombreSub1',	'idSub1',	'2019-02-26 22:00:37',	'2019-02-26 17:00:37'),
(6,	'productosventa',	'Sub2',	'prod_sub2',	'NombreSub2',	'idSub2',	'2019-02-26 22:02:42',	'2019-02-26 17:02:42'),
(7,	'productosventa',	'Sub3',	'prod_sub3',	'NombreSub3',	'idSub3',	'2019-02-26 22:02:42',	'2019-02-26 17:02:42'),
(8,	'productosventa',	'Sub4',	'prod_sub4',	'NombreSub4',	'idSub4',	'2019-02-26 22:02:42',	'2019-02-26 17:02:42'),
(9,	'productosventa',	'Sub5',	'prod_sub5',	'NombreSub5',	'idSub5',	'2019-02-26 22:02:42',	'2019-02-26 17:02:42'),
(10,	'productosventa',	'Sub6',	'prod_sub6',	'NombreSub6',	'idSub6',	'2019-02-26 22:02:42',	'2019-02-26 17:02:42'),
(11,	'cotizacionesv5',	'Clientes_idClientes',	'clientes',	'Num_Identificacion',	'idClientes',	'2019-03-02 04:38:29',	'2019-03-01 23:38:29'),
(12,	'clientes',	'Tipo_Documento',	'cod_documentos',	'Descripcion',	'Codigo',	'2019-03-07 16:45:07',	'2019-03-07 11:45:07'),
(13,	'clientes',	'Cod_Dpto',	'cod_departamentos',	'Nombre',	'Cod_dpto',	'2019-03-07 16:45:07',	'2019-03-07 11:45:07'),
(14,	'clientes',	'Cod_Mcipio',	'cod_departamentos',	'Ciudad',	'Cod_mcipio',	'2019-03-07 16:45:07',	'2019-03-07 11:45:07'),
(15,	'clientes',	'Pais_Domicilio',	'cod_paises',	'Pais',	'Codigo',	'2019-03-07 16:45:07',	'2019-03-07 11:45:07');

DROP TABLE IF EXISTS `configuracion_control_tablas`;
CREATE TABLE `configuracion_control_tablas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TablaDB` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `Agregar` int(1) NOT NULL,
  `Editar` int(1) NOT NULL,
  `Ver` int(1) NOT NULL,
  `LinkVer` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Exportar` int(1) NOT NULL,
  `AccionesAdicionales` int(1) NOT NULL,
  `Eliminar` int(1) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `configuracion_control_tablas` (`ID`, `TablaDB`, `Agregar`, `Editar`, `Ver`, `LinkVer`, `Exportar`, `AccionesAdicionales`, `Eliminar`, `Updated`, `Sync`) VALUES
(1,	'empresapro',	1,	1,	0,	'',	1,	0,	0,	'2019-01-13 14:04:48',	'2019-01-13 09:04:48'),
(2,	'formatos_calidad',	1,	1,	0,	'',	1,	0,	0,	'2019-01-13 14:04:48',	'2019-01-13 09:04:48'),
(3,	'facturas',	0,	0,	1,	'PDF_Documentos.draw.php?idDocumento=2&ID=',	1,	1,	0,	'2019-01-13 14:04:48',	'2019-01-13 09:04:48'),
(4,	'cotizacionesv5',	0,	1,	1,	'PDF_Documentos.draw.php?idDocumento=1&ID=',	1,	1,	0,	'2019-03-02 04:38:30',	'2019-03-01 23:38:30'),
(5,	'cot_itemscotizaciones',	0,	1,	0,	'',	1,	0,	0,	'2019-01-13 14:04:48',	'2019-01-13 09:04:48'),
(6,	'empresapro_resoluciones_facturacion',	1,	1,	0,	'',	1,	0,	0,	'2019-01-13 14:04:48',	'2019-01-13 09:04:48'),
(7,	'comprobantes_ingreso',	0,	0,	1,	'PDF_Documentos.draw.php?idDocumento=4&idIngreso=',	1,	1,	0,	'2019-03-02 04:38:30',	'2019-03-01 23:38:30'),
(8,	'clientes',	1,	1,	0,	'',	1,	0,	0,	'2019-03-02 04:38:30',	'2019-03-01 23:38:30'),
(9,	'prestamos_terceros',	0,	0,	1,	'PDF_Documentos.draw.php?idDocumento=35&ID=',	1,	1,	0,	'2019-04-06 19:19:59',	'2019-04-06 14:19:59'),
(10,	'vista_documentos_contables',	0,	0,	1,	'PDF_Documentos.draw.php?idDocumento=32&idDocumentoContable=',	1,	1,	0,	'2019-04-11 14:01:47',	'2019-04-11 09:01:47'),
(11,	'ordenesdecompra',	0,	0,	1,	'PDF_Documentos.draw.php?idDocumento=5&ID=',	1,	1,	0,	'2019-04-16 19:45:33',	'2019-04-16 14:45:33'),
(12,	'vista_factura_compra_totales',	0,	0,	1,	'PDF_Documentos.draw.php?idDocumento=23&ID=',	1,	1,	0,	'2019-04-16 19:45:33',	'2019-04-16 14:45:33');

DROP TABLE IF EXISTS `configuracion_general`;
CREATE TABLE `configuracion_general` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `Valor` text COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `configuracion_general` (`ID`, `Descripcion`, `Valor`, `Updated`, `Sync`) VALUES
(1,	'RUTA PARA EXPORTAR TABLAS EN CSV',	'../../htdocs/ts5/exports/tabla.csv',	'2019-01-13 14:04:49',	'2019-01-13 09:04:49'),
(2,	'Valor por defecto si se imprime o no al momento de realizar una factura pos',	'1',	'2019-03-18 12:44:40',	'2019-03-18 07:44:40'),
(3,	'Determina si se debe pedir autorizacion para retornar un item en pos',	'1',	'2019-03-18 13:20:26',	'2019-03-18 08:20:26'),
(4,	'Determina si se debe pedir autorizacion para elimininar un item en pos',	'1',	'2019-03-18 13:27:46',	'2019-03-18 08:27:46'),
(5,	'Determina si se debe pedir autorizacion para cambiar el precio de venta de un item en pos',	'1',	'2019-03-18 13:33:01',	'2019-03-18 08:33:01'),
(6,	'Determina el valor maximo que se puede aplicar al descuento general',	'50',	'2019-03-18 13:33:01',	'2019-03-18 08:33:01'),
(7,	'Determina si se pueden realizar descuentos a precio de costo',	'0',	'2019-03-18 20:54:51',	'2019-03-18 15:54:51'),
(8,	'Determina cuantas copias saldrán del separado al crearse',	'2',	'2019-03-19 19:19:59',	'2019-03-19 14:19:59'),
(9,	'Determina cuantas copias saldrán del egreso al crearse desde pos',	'2',	'2019-03-19 21:47:01',	'2019-03-19 16:47:01');

DROP TABLE IF EXISTS `configuracion_tablas_acciones_adicionales`;
CREATE TABLE `configuracion_tablas_acciones_adicionales` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TablaDB` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `JavaScript` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `ClaseIcono` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Titulo` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `Ruta` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `Target` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `configuracion_tablas_acciones_adicionales` (`ID`, `TablaDB`, `JavaScript`, `ClaseIcono`, `Titulo`, `Ruta`, `Target`, `Updated`, `Sync`) VALUES
(1,	'facturas',	'',	'fa fa-fw fa-copy',	'Copia',	'../../general/Consultas/PDF_Documentos.draw.php?TipoFactura=COPIA&idDocumento=2&ID=	',	'_BLANK',	'2019-01-13 14:04:49',	'2019-01-13 09:04:49'),
(2,	'facturas',	'',	'fa fa-fw fa-book',	'Contabilidad',	'../../general/Consultas/PDF_Documentos.draw.php?TipoFactura=CONTABILIDAD&idDocumento=2&ID=	',	'_BLANK',	'2019-01-13 14:04:49',	'2019-01-13 09:04:49'),
(3,	'facturas',	'',	'fa fa-fw fa-close',	'Anular',	'../../VAtencion/AnularFactura.php?idFactura=',	'_BLANK',	'2019-01-13 14:04:49',	'2019-01-13 09:04:49'),
(4,	'comprobantes_ingreso',	'',	'fa fa-fw fa-close',	'Anular',	'../../VAtencion/AnularComprobanteIngreso.php?idComprobante=',	'_BLANK',	'2019-03-02 04:38:30',	'2019-03-01 23:38:30'),
(5,	'prestamos_terceros',	'onclick=AbreModalAbonar',	'fa fa-fw fa-plus',	'Abonar',	'#',	'_SELF',	'2019-04-06 19:49:13',	'2019-04-06 14:49:13'),
(6,	'prestamos_terceros',	'onclick=HistorialAbonos',	'fa fa-fw fa-history',	'Historial',	'#',	'_SELF',	'2019-04-07 13:23:24',	'2019-04-07 08:23:24');

DROP TABLE IF EXISTS `empresapro`;
CREATE TABLE `empresapro` (
  `idEmpresaPro` int(11) NOT NULL AUTO_INCREMENT,
  `RazonSocial` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `NIT` bigint(20) DEFAULT NULL,
  `DigitoVerificacion` int(1) NOT NULL,
  `Direccion` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Barrio` varchar(70) COLLATE utf8_spanish_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Celular` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Ciudad` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ResolucionDian` text COLLATE utf8_spanish_ci NOT NULL,
  `Regimen` enum('SIMPLIFICADO','COMUN') COLLATE utf8_spanish_ci DEFAULT 'SIMPLIFICADO',
  `TipoPersona` enum('1','2','3') COLLATE utf8_spanish_ci NOT NULL COMMENT '1 Persona jurica, 2 persona natural,3 grandes contribuyentes',
  `TipoDocumento` int(11) NOT NULL,
  `MatriculoMercantil` bigint(20) NOT NULL,
  `ActividadesEconomicas` text COLLATE utf8_spanish_ci NOT NULL,
  `Email` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `WEB` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ObservacionesLegales` text COLLATE utf8_spanish_ci NOT NULL,
  `PuntoEquilibrio` bigint(20) DEFAULT NULL,
  `DatosBancarios` text COLLATE utf8_spanish_ci NOT NULL,
  `RutaImagen` varchar(200) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'LogosEmpresas/logotipo1.png',
  `FacturaSinInventario` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `CXPAutomaticas` varchar(2) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'SI',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idEmpresaPro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `empresapro` (`idEmpresaPro`, `RazonSocial`, `NIT`, `DigitoVerificacion`, `Direccion`, `Barrio`, `Telefono`, `Celular`, `Ciudad`, `ResolucionDian`, `Regimen`, `TipoPersona`, `TipoDocumento`, `MatriculoMercantil`, `ActividadesEconomicas`, `Email`, `WEB`, `ObservacionesLegales`, `PuntoEquilibrio`, `DatosBancarios`, `RutaImagen`, `FacturaSinInventario`, `CXPAutomaticas`, `Updated`, `Sync`) VALUES
(1,	'Ftech Colombia SAS',	901143311,	1,	'AvPoblado Cra 43 A 19 17',	'MEDELLIN',	'3177740609',	'3177740609',	'MEDELLIN',	'IVA REGIMEN COMUN ACTIVIDAD ECONOMICA CIIU 8020',	'COMUN',	'3',	31,	1234567,	'O-42;O-42',	'info@technosoluciones.com',	'www.technosoluciones.com',	'Esta Factura de Venta se asimila en todos sus efectos a una letra de cambio (Art. 621 y siguientes del Codigo de Comercio). En caso de mora se causaran los intereses legales Vigentes.',	5000000,	'_',	'LogosEmpresas/logotipo1.png',	'SI',	'SI',	'2019-01-13 14:04:55',	'2019-01-13 09:04:55');

DROP TABLE IF EXISTS `formatos_calidad`;
CREATE TABLE `formatos_calidad` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `Version` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Codigo` text COLLATE utf8_spanish_ci NOT NULL,
  `Fecha` date NOT NULL,
  `CuerpoFormato` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `NotasPiePagina` text COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `idCarpeta` int(11) NOT NULL,
  `Pagina` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `Target` varchar(10) COLLATE utf8_spanish_ci NOT NULL DEFAULT '_SELF',
  `Estado` int(1) NOT NULL DEFAULT '1',
  `Image` text COLLATE utf8_spanish_ci NOT NULL,
  `CSS_Clase` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `Orden` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `menu_carpetas`;
CREATE TABLE `menu_carpetas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Ruta` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `menu_pestanas`;
CREATE TABLE `menu_pestanas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `idMenu` int(11) NOT NULL,
  `Orden` int(11) NOT NULL,
  `Estado` bit(1) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `menu_submenus`;
CREATE TABLE `menu_submenus` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `idPestana` int(11) NOT NULL,
  `idCarpeta` int(11) NOT NULL,
  `idMenu` int(11) NOT NULL,
  `TablaAsociada` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `TipoLink` int(1) NOT NULL,
  `JavaScript` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Pagina` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `Target` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `Estado` int(1) NOT NULL,
  `Image` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `Orden` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `idUsuarios` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Apellido` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Identificacion` varchar(40) COLLATE utf8_spanish_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Login` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Password` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TipoUser` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Email` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Role` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Habilitado` varchar(2) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'SI',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idUsuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `usuarios` (`idUsuarios`, `Nombre`, `Apellido`, `Identificacion`, `Telefono`, `Login`, `Password`, `TipoUser`, `Email`, `Role`, `Habilitado`, `Updated`, `Sync`) VALUES
(1,	'TECHNO ',	'SOLUCIONES',	'900833180',	'3177740609',	'admin',	'techno',	'administrador',	'info@technosoluciones.com',	'SUPERVISOR',	'SI',	'2019-04-27 15:38:18',	'2019-04-27 10:38:18'),
(2,	'ADMINISTRADOR',	'SOFTCONTECH',	'1',	'1',	'administrador',	'91f5167c34c400758115c2a6826ec2e3',	'operador',	'no@no.com',	'SUPERVISOR',	'SI',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(3,	'JULIAN ANDRES',	'ALVARAN',	'94481747',	'3177740609',	'jalvaran',	'pirlo1985',	'administrador',	'jalvaran@gmail.com',	'SUPERVISOR',	'SI',	'2019-04-08 20:35:50',	'2019-04-08 15:35:50'),
(4,	'WILSON',	'ALBERTO MOSQUERA',	'1',	'318 5658225',	'wamc',	'f5dc2d19e23c69e58e398ea72ae06fd4',	'comercial',	'no',	'ADMINISTRADOR',	'SI',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14');

DROP TABLE IF EXISTS `usuarios_tipo`;
CREATE TABLE `usuarios_tipo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Tipo` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `usuarios_tipo` (`ID`, `Tipo`, `Updated`, `Sync`) VALUES
(1,	'administrador',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(2,	'operador',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(3,	'comercial',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(4,	'cajero',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(5,	'bodega',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14');

-- 2019-05-20 12:22:11
