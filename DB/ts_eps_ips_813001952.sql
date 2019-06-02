-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `actualizacioncarteracargadaips`;
CREATE TABLE `actualizacioncarteracargadaips` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NitEPS` bigint(20) NOT NULL COMMENT 'Número de identificación del entidad promomotora de salud ',
  `NitIPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud ',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura ',
  `FechaFactura` date NOT NULL,
  `NumeroCuentaGlobal` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la cuenta de cobro ',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado ',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura ',
  `TipoNegociacion` enum('EVENTO','CAPITA') COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Número del contrato ',
  `DiasPactados` int(3) DEFAULT NULL COMMENT 'Dias que se pactaron para el pago de la factura con eps',
  `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de regimen',
  `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor neto a pagar por la entidad contratante ',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de la glosa inicial que tiene la IPS ',
  `ValorGlosaAceptada` double(15,2) NOT NULL COMMENT 'Valor de la glosa Aceptada por IPS ',
  `ValorGlosaConciliada` double(15,2) NOT NULL COMMENT 'Valor de la glosa conciliada por IPS ',
  `ValorDescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor del Descuento en Adress ',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de los anticipos a IPS',
  `ValorRetencion` double(15,2) NOT NULL COMMENT 'Valor de las rentencionede de la factura',
  `ValorTotalpagar` double(15,2) NOT NULL COMMENT 'Valor total a pagar',
  `FechaHasta` date NOT NULL COMMENT 'Fecha hasta donde esta la relacion de la cartera ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro ',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro ',
  `FlagUpdate` int(11) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Nit_EPS` (`NitEPS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de cargue de cartera temporal de cargues cartera ips';


DROP TABLE IF EXISTS `anticipos_asmet`;
CREATE TABLE `anticipos_asmet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `DescripcionNC` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion de la Nota',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura proveedor ',
  `ValorFactura` double(15,2) NOT NULL COMMENT 'Valor de la factura ',
  `ValorReteiva` double(15,2) NOT NULL COMMENT 'Valor reteiva',
  `ValorRetefuente` double(15,2) NOT NULL COMMENT 'Valor retefuente',
  `ValorMenosImpuestos` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `ValorSaldo` double(15,2) NOT NULL COMMENT 'Valor del saldo',
  `ValorAnticipado` double(15,2) NOT NULL COMMENT 'Valor del anticipo ',
  `NumeroAnticipo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número del anticipo',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `keyFile` (`keyFile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Glosas';


DROP TABLE IF EXISTS `carteracargadaips`;
CREATE TABLE `carteracargadaips` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NitEPS` bigint(20) NOT NULL COMMENT 'Número de identificación del entidad promomotora de salud ',
  `NitIPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud ',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura ',
  `FechaFactura` date NOT NULL,
  `NumeroCuentaGlobal` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la cuenta de cobro ',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado ',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura ',
  `TipoNegociacion` enum('EVENTO','CAPITA') COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Número del contrato ',
  `DiasPactados` int(3) DEFAULT NULL COMMENT 'Dias que se pactaron para el pago de la factura con eps',
  `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de regimen',
  `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor neto a pagar por la entidad contratante ',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de la glosa inicial que tiene la IPS ',
  `ValorGlosaAceptada` double(15,2) NOT NULL COMMENT 'Valor de la glosa Aceptada por IPS ',
  `ValorGlosaConciliada` double(15,2) NOT NULL COMMENT 'Valor de la glosa conciliada por IPS ',
  `ValorDescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor del Descuento en Adress ',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de los anticipos a IPS',
  `ValorRetencion` double(15,2) NOT NULL COMMENT 'Valor de las rentencionede de la factura',
  `ValorTotalpagar` double(15,2) NOT NULL COMMENT 'Valor total a pagar',
  `FechaHasta` date NOT NULL COMMENT 'Fecha hasta donde esta la relacion de la cartera ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro ',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro ',
  `FlagUpdate` int(11) NOT NULL,
  `ConciliadoXIPS` int(1) NOT NULL,
  `ConciliadoXEPS` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Nit_EPS` (`NitEPS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de cargue de cartera temporal de cargues cartera ips';


DROP TABLE IF EXISTS `carteraeps`;
CREATE TABLE `carteraeps` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NitEPS` bigint(20) NOT NULL,
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `Sucursal` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'sucursal',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Razón social o apellidos y nombre del prestador',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Prefijo` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Prefijo',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `ValorOriginal` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
  `ValorMenosImpuestos` double(15,2) NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `ConciliadoXIPS` int(1) NOT NULL,
  `ConciliadoXEPS` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Sync` (`Sync`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla tibco de asmet mutual';


DROP TABLE IF EXISTS `controlcargueseps`;
CREATE TABLE `controlcargueseps` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NombreCargue` varchar(65) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del cargue ',
  `ValorCargue` double(15,2) NOT NULL COMMENT 'Valor total del cargue ',
  `Nit_EPS` bigint(20) NOT NULL COMMENT 'Número de identificación del entidad promomotora de salud ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `RutaArchivo` text COLLATE utf8_spanish_ci NOT NULL,
  `ExtensionArchivo` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro ',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro ',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NombreCargue` (`NombreCargue`),
  KEY `Nit_EPS` (`Nit_EPS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de control de cargues';


DROP TABLE IF EXISTS `controlcarguesips`;
CREATE TABLE `controlcarguesips` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NombreCargue` varchar(65) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del cargue ',
  `ValorCargue` double(15,2) NOT NULL COMMENT 'Valor total del cargue ',
  `Nit_EPS` bigint(20) NOT NULL COMMENT 'Número de identificación del entidad promomotora de salud ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `RutaArchivo` text COLLATE utf8_spanish_ci NOT NULL,
  `ExtensionArchivo` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro ',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro ',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NombreCargue` (`NombreCargue`),
  KEY `Nit_EPS` (`Nit_EPS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de control de cargues';


DROP TABLE IF EXISTS `glosaseps_asmet`;
CREATE TABLE `glosaseps_asmet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Razón social o apellidos y nombre del prestador',
  `Sede` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura proveedor ',
  `ValorFactura` double(15,2) NOT NULL COMMENT 'Valor de la factura ',
  `ValorTotalGlosa` double(15,2) NOT NULL COMMENT 'Valor total de la glosa en factura ',
  `ValorGlosaFavor` double(15,2) NOT NULL COMMENT 'Valor de la glosa a favor de la eps ',
  `ValorGlosaContra` double(15,2) NOT NULL COMMENT 'Valor de la glosa a en contra de la eps ',
  `ValorPendienteResolver` double(15,2) NOT NULL COMMENT 'Valor pendiente por resolver de la glosa',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Glosas';


DROP TABLE IF EXISTS `historial_carteracargada_eps`;
CREATE TABLE `historial_carteracargada_eps` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOperacion` int(7) DEFAULT NULL COMMENT 'Numero de Operacion',
  `FechaFactura` date NOT NULL COMMENT 'Fecha de factura',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `Sucursal` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Razón social o apellidos y nombre del prestador',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Prefijo` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Prefijo',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado que genera la EPS',
  `MesServicio` int(5) DEFAULT NULL COMMENT 'Año y mes que se presta el servicio',
  `ValorOriginal` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
  `ValorMenosImpuestos` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `ValorPagado` double(15,2) NOT NULL COMMENT 'Valor que se le ha pagado a factura',
  `ValorCruce` double(15,2) NOT NULL COMMENT 'Valor a cruzar',
  `ValorCruceAnticipo` double(15,2) NOT NULL COMMENT 'Valor de cruces con anticipos',
  `ValorCruceAuditoria` double(15,2) NOT NULL COMMENT 'Valor de cruces en Auditoria',
  `SaldoFactura` double(15,2) NOT NULL COMMENT 'Valor pendiente de pago',
  `ValorAutorizado` double(15,2) NOT NULL COMMENT 'Valor que se Autoriza',
  `AnticiposRelacionados` varchar(500) COLLATE utf8_spanish_ci NOT NULL COMMENT 'relacion de los anticipos con los cuales la factura se afecta',
  `ValorGlosaTotalMutual` double NOT NULL,
  `CrucesMutual` double NOT NULL,
  `SaldoMutual` double NOT NULL,
  `TotalValorGlosadoD2702` double(15,2) NOT NULL COMMENT 'Valor total de las glosas que den lugar a la factura despues del decreto2702/2014',
  `ValorPagosGlosadoD2702` double(15,2) NOT NULL COMMENT 'Valor de cruces de la glosas despues del decreto2702/2014',
  `ValorCruceGlosadoD2702` double(15,2) NOT NULL COMMENT 'Valor de cruces de la glosas despues del decreto2702/2014',
  `SaldoGlosaD2702` double(15,2) NOT NULL COMMENT 'Saldo de lo glosado despues del decreto2702/2014',
  `ValorAutorizadoGlosado` double(15,2) NOT NULL COMMENT 'Valor que se Autoriza en glosa',
  `Original29` double(15,2) NOT NULL COMMENT 'Campo no identificado',
  `TipoOperacionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operacion en cruces de facturacion',
  `NumeroTransaccionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion',
  `FechaTransaccionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion',
  `ValorCruceTransaccionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores que cruzaron con la facura',
  `TipoOperacionPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operacion en pagos de facturacion',
  `NumeroTransaccionPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion en pagos de facturacion',
  `FechaTransaccionPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion en pagos de facturacion',
  `ValorPagadoPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores que pagaron en la facura',
  `NumeroPlanoPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numero del los planos en pagos de facturacion',
  `FechaPlanoPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de planos en pagos de facturacion',
  `TipoOperacionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operaciones en glosas antes del decreto 2702/2014',
  `FechaTransaccionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion en glosas antes del decreto 2702/2014',
  `NumeroTransaccionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion en glosas antes del decreto 2702/2014',
  `ValorCruceTransaccionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores cruzados en glosas antes del decreto 2702/2014',
  `TipoOperacionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operaciones en glosas despues del decreto 2702/2014',
  `FechaTransaccionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion en glosas despues del decreto 2702/2014',
  `NumeroTransaccionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion en glosas despues del decreto 2702/2014',
  `ValorCruceTransaccionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores cruzados en glosas despues del decreto 2702/2014',
  `NumeroPlanoGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numero del los planos en glosas despues del decreto 2702/2014',
  `DescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor de descuento en base de datos unico de afiliados',
  `Previsado` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'campo no identificado',
  `EnGiro` int(5) DEFAULT NULL COMMENT 'Año y mes del giro',
  `ValorGiro` double(15,2) NOT NULL COMMENT 'Valor del Giro',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `Nit_EPS` bigint(20) NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `FlagUpdate` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `Nit_EPS` (`Nit_EPS`),
  KEY `FlagUpdate` (`FlagUpdate`),
  KEY `NumeroOperacion` (`NumeroOperacion`),
  KEY `FechaFactura` (`FechaFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla tibco de asmet Sas';


DROP TABLE IF EXISTS `pagos_asmet`;
CREATE TABLE `pagos_asmet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `Nit_EPS` bigint(20) NOT NULL,
  `Proceso` int(10) NOT NULL COMMENT 'Número del proceso',
  `DescripcionProceso` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del Proceso',
  `Estado` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado de Pago',
  `Cuenta` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'cuenta',
  `Banco` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Banco en el cual se paga',
  `FechaPagoFactura` date NOT NULL COMMENT 'Fecha de pago de la factura',
  `NumeroPago` int(10) NOT NULL COMMENT 'Número del comprobante del pago ',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura proveedor ',
  `ValorBrutoPagar` double(15,2) NOT NULL COMMENT 'Valor bruto a pagar',
  `ValorDescuento` double(15,2) NOT NULL COMMENT 'Valor descuento',
  `ValorIva` double(15,2) NOT NULL COMMENT 'Valor iva',
  `ValorRetefuente` double(15,2) NOT NULL COMMENT 'Valor retefuente',
  `ValorReteiva` double(15,2) NOT NULL COMMENT 'Valor reteiva',
  `ValorReteica` double(15,2) NOT NULL COMMENT 'Valor reteica',
  `ValorOtrasRetenciones` double(15,2) NOT NULL COMMENT 'Valor otras retenciones',
  `ValorCruces` double(15,2) NOT NULL COMMENT 'Valor de cruces posible glosas ',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de anticipos ',
  `ValorTotal` double(15,2) NOT NULL COMMENT 'Valor total ',
  `ValorTranferido` double(15,2) NOT NULL COMMENT 'Valor transferido a banco ',
  `Regional` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se Genera el pago',
  `llaveCompuesta` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'el Prceso, la fecha, tipoOperacion,nuemero del pago,la factura,el valor',
  `idUser` int(11) NOT NULL,
  `Soporte` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Estado` (`Estado`),
  KEY `NumeroPago` (`NumeroPago`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `llaveCompuesta` (`llaveCompuesta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Pagos realizados por ASMET';


DROP TABLE IF EXISTS `pagos_asmet_temporal`;
CREATE TABLE `pagos_asmet_temporal` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `Nit_EPS` bigint(20) NOT NULL,
  `Proceso` int(10) NOT NULL COMMENT 'Número del proceso',
  `DescripcionProceso` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del Proceso',
  `Estado` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado de Pago',
  `Cuenta` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'cuenta',
  `Banco` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Banco en el cual se paga',
  `FechaPagoFactura` date NOT NULL COMMENT 'Fecha de pago de la factura',
  `NumeroPago` int(10) NOT NULL COMMENT 'Número del comprobante del pago ',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura proveedor ',
  `ValorBrutoPagar` double(15,2) NOT NULL COMMENT 'Valor bruto a pagar',
  `ValorDescuento` double(15,2) NOT NULL COMMENT 'Valor descuento',
  `ValorIva` double(15,2) NOT NULL COMMENT 'Valor iva',
  `ValorRetefuente` double(15,2) NOT NULL COMMENT 'Valor retefuente',
  `ValorReteiva` double(15,2) NOT NULL COMMENT 'Valor reteiva',
  `ValorReteica` double(15,2) NOT NULL COMMENT 'Valor reteica',
  `ValorOtrasRetenciones` double(15,2) NOT NULL COMMENT 'Valor otras retenciones',
  `ValorCruces` double(15,2) NOT NULL COMMENT 'Valor de cruces posible glosas ',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de anticipos ',
  `ValorTotal` double(15,2) NOT NULL COMMENT 'Valor total ',
  `ValorTranferido` double(15,2) NOT NULL COMMENT 'Valor transferido a banco ',
  `Regional` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se Genera el pago',
  `llaveCompuesta` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'el Prceso, la fecha, tipoOperacion,nuemero del pago,la factura,el valor',
  `idUser` int(11) NOT NULL,
  `Soporte` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `FlagUpdate` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Estado` (`Estado`),
  KEY `NumeroPago` (`NumeroPago`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `llaveCompuesta` (`llaveCompuesta`),
  KEY `FlagUpdate` (`FlagUpdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Pagos realizados por ASMET';


DROP TABLE IF EXISTS `registro_actualizacion_facturas`;
CREATE TABLE `registro_actualizacion_facturas` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FacturaAnterior` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `FacturaNueva` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `registro_conciliaciones_ips_eps`;
CREATE TABLE `registro_conciliaciones_ips_eps` (
  `ID` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `TipoConciliacion` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `temporalcarguecarteraeps`;
CREATE TABLE `temporalcarguecarteraeps` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOperacion` int(7) DEFAULT NULL COMMENT 'Numero de Operacion',
  `FechaFactura` date NOT NULL COMMENT 'Fecha de factura',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `Sucursal` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Razón social o apellidos y nombre del prestador',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Prefijo` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Prefijo',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado que genera la EPS',
  `MesServicio` int(5) DEFAULT NULL COMMENT 'Año y mes que se presta el servicio',
  `ValorOriginal` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
  `ValorMenosImpuestos` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `ValorPagado` double(15,2) NOT NULL COMMENT 'Valor que se le ha pagado a factura',
  `ValorCruce` double(15,2) NOT NULL COMMENT 'Valor a cruzar',
  `ValorCruceAnticipo` double(15,2) NOT NULL COMMENT 'Valor de cruces con anticipos',
  `ValorCruceAuditoria` double(15,2) NOT NULL COMMENT 'Valor de cruces en Auditoria',
  `SaldoFactura` double(15,2) NOT NULL COMMENT 'Valor pendiente de pago',
  `ValorAutorizado` double(15,2) NOT NULL COMMENT 'Valor que se Autoriza',
  `AnticiposRelacionados` varchar(500) COLLATE utf8_spanish_ci NOT NULL COMMENT 'relacion de los anticipos con los cuales la factura se afecta',
  `ValorGlosaTotalMutual` double NOT NULL,
  `CrucesMutual` double NOT NULL,
  `SaldoMutual` double NOT NULL,
  `TotalValorGlosadoD2702` double(15,2) NOT NULL COMMENT 'Valor total de las glosas que den lugar a la factura despues del decreto2702/2014',
  `ValorPagosGlosadoD2702` double(15,2) NOT NULL COMMENT 'Valor de cruces de la glosas despues del decreto2702/2014',
  `ValorCruceGlosadoD2702` double(15,2) NOT NULL COMMENT 'Valor de cruces de la glosas despues del decreto2702/2014',
  `SaldoGlosaD2702` double(15,2) NOT NULL COMMENT 'Saldo de lo glosado despues del decreto2702/2014',
  `ValorAutorizadoGlosado` double(15,2) NOT NULL COMMENT 'Valor que se Autoriza en glosa',
  `Original29` double(15,2) NOT NULL COMMENT 'Campo no identificado',
  `TipoOperacionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operacion en cruces de facturacion',
  `NumeroTransaccionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion',
  `FechaTransaccionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion',
  `ValorCruceTransaccionCF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores que cruzaron con la facura',
  `TipoOperacionPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operacion en pagos de facturacion',
  `NumeroTransaccionPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion en pagos de facturacion',
  `FechaTransaccionPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion en pagos de facturacion',
  `ValorPagadoPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores que pagaron en la facura',
  `NumeroPlanoPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numero del los planos en pagos de facturacion',
  `FechaPlanoPF` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de planos en pagos de facturacion',
  `TipoOperacionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operaciones en glosas antes del decreto 2702/2014',
  `FechaTransaccionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion en glosas antes del decreto 2702/2014',
  `NumeroTransaccionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion en glosas antes del decreto 2702/2014',
  `ValorCruceTransaccionGA2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores cruzados en glosas antes del decreto 2702/2014',
  `TipoOperacionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'tipos de operaciones en glosas despues del decreto 2702/2014',
  `FechaTransaccionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'fechas de transaccion en glosas despues del decreto 2702/2014',
  `NumeroTransaccionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numeros de documentos de transaccion en glosas despues del decreto 2702/2014',
  `ValorCruceTransaccionGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'valores cruzados en glosas despues del decreto 2702/2014',
  `NumeroPlanoGD2702` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numero del los planos en glosas despues del decreto 2702/2014',
  `DescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor de descuento en base de datos unico de afiliados',
  `Previsado` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'campo no identificado',
  `EnGiro` int(5) DEFAULT NULL COMMENT 'Año y mes del giro',
  `ValorGiro` double(15,2) NOT NULL COMMENT 'Valor del Giro',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `Nit_EPS` bigint(20) NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `FlagUpdate` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `FlagUpdate` (`FlagUpdate`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `FechaFactura` (`FechaFactura`),
  KEY `NumeroOperacion` (`NumeroOperacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Temporal tibco de asmet Sas';


DROP TABLE IF EXISTS `temporalcarguecarteraips`;
CREATE TABLE `temporalcarguecarteraips` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NitEPS` bigint(20) NOT NULL COMMENT 'Número de identificación del entidad promomotora de salud',
  `NitIPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura',
  `FechaFactura` date NOT NULL,
  `NumeroCuentaGlobal` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la cuenta de cobro"',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura',
  `TipoNegociacion` enum('EVENTO','CAPITA') COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Número del contrato',
  `DiasPactados` int(3) DEFAULT NULL COMMENT 'Dias que se pactaron para el pago de la factura con eps',
  `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de regimen',
  `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor neto a pagar por la entidad contratante',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de la glosa inicial que tiene la IPS',
  `ValorGlosaAceptada` double(15,2) NOT NULL COMMENT 'Valor de la glosa Aceptada por IPS',
  `ValorGlosaConciliada` double(15,2) NOT NULL COMMENT 'Valor de la glosa conciliada por IPS',
  `ValorDescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor del Descuento en Adress',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de los anticipos a IPS',
  `ValorRetencion` double(15,2) NOT NULL COMMENT 'Valor de las rentencionede de la factura',
  `ValorTotalpagar` double(15,2) NOT NULL COMMENT 'Valor total a pagar',
  `FechaHasta` date NOT NULL COMMENT 'Fecha hasta donde esta la relacion de la cartera',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro"',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `FlagUpdate` int(11) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo temporal de cargues cartera ips';


DROP TABLE IF EXISTS `temporal_actualizacion_facturas`;
CREATE TABLE `temporal_actualizacion_facturas` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `FacturaAnterior` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `FacturaNueva` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `temporal_anticipos_asmet`;
CREATE TABLE `temporal_anticipos_asmet` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `DescripcionNC` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion de la Nota',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura proveedor ',
  `ValorFactura` double(15,2) NOT NULL COMMENT 'Valor de la factura ',
  `ValorReteiva` double(15,2) NOT NULL COMMENT 'Valor reteiva',
  `ValorRetefuente` double(15,2) NOT NULL COMMENT 'Valor retefuente',
  `ValorMenosImpuestos` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `ValorSaldo` double(15,2) NOT NULL COMMENT 'Valor del saldo',
  `ValorAnticipado` double(15,2) NOT NULL COMMENT 'Valor del anticipo ',
  `NumeroAnticipo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número del anticipo',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Glosas';


DROP TABLE IF EXISTS `temporal_glosaseps_asmet`;
CREATE TABLE `temporal_glosaseps_asmet` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'Número de identificación del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Razón social o apellidos y nombre del prestador',
  `Sede` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la radicado',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Número de la factura proveedor ',
  `ValorFactura` double(15,2) NOT NULL COMMENT 'Valor de la factura ',
  `ValorTotalGlosa` double(15,2) NOT NULL COMMENT 'Valor total de la glosa en factura ',
  `ValorGlosaFavor` double(15,2) NOT NULL COMMENT 'Valor de la glosa a favor de la eps ',
  `ValorGlosaContra` double(15,2) NOT NULL COMMENT 'Valor de la glosa a en contra de la eps ',
  `ValorPendienteResolver` double(15,2) NOT NULL COMMENT 'Valor pendiente por resolver de la glosa',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `FlagUpdate` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Glosas';


DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;
CREATE TABLE `vista_cruce_cartera_asmet` (`ID` bigint(20), `NumeroFactura` varchar(20), `FechaFactura` date, `NumeroRadicado` varchar(20), `FechaRadicado` date, `NumeroContrato` varchar(70), `ValorDocumento` double(15,2), `ValorMenosImpuestos` double(15,2), `Impuestos` double(19,2), `OtrosDescuentos` varchar(1), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `TotalGlosaInicial` double(19,2), `TotalGlosaFavor` double(19,2), `TotalGlosaContra` double(19,2), `GlosaXConciliar` double(19,2), `ValorSegunEPS` double, `ValorSegunIPS` double(15,2), `Diferencia` double);


DROP VIEW IF EXISTS `vista_facturas_originales`;
CREATE TABLE `vista_facturas_originales` (`NumeroFactura` varchar(20), `ValorOriginal` double(15,2), `ValorMenosImpuestos` double(15,2));


DROP VIEW IF EXISTS `vista_facturas_pagadas_no_relacionadas`;
CREATE TABLE `vista_facturas_pagadas_no_relacionadas` (`ID` bigint(20), `Nit_IPS` bigint(20), `Nit_EPS` bigint(20), `Proceso` int(10), `DescripcionProceso` varchar(100), `Estado` varchar(30), `Cuenta` varchar(30), `Banco` varchar(50), `FechaPagoFactura` date, `NumeroPago` int(10), `TipoOperacion` int(5), `NumeroFactura` varchar(20), `ValorBrutoPagar` double(15,2), `ValorDescuento` double(15,2), `ValorIva` double(15,2), `ValorRetefuente` double(15,2), `ValorReteiva` double(15,2), `ValorReteica` double(15,2), `ValorOtrasRetenciones` double(15,2), `ValorCruces` double(15,2), `ValorAnticipos` double(15,2), `ValorTotal` double(15,2), `ValorTranferido` double(15,2), `Regional` varchar(25), `llaveCompuesta` varchar(45), `idUser` int(11), `Soporte` varchar(100), `FechaRegistro` datetime, `FechaActualizacion` datetime, `Sync` datetime);


DROP VIEW IF EXISTS `vista_facturas_sr_eps`;
CREATE TABLE `vista_facturas_sr_eps` (`ID` bigint(20), `NitEPS` bigint(20), `CodigoSucursal` int(6), `Sucursal` varchar(25), `NumeroFactura` varchar(20), `Descripcion` varchar(300), `RazonSocial` varchar(60), `Nit_IPS` bigint(20), `NumeroContrato` varchar(40), `Prefijo` varchar(4), `DepartamentoRadicacion` varchar(25), `ValorOriginal` double(15,2), `idUser` int(11), `FechaRegistro` datetime, `FechaActualizacion` datetime, `Sync` datetime);


DROP VIEW IF EXISTS `vista_facturas_sr_ips`;
CREATE TABLE `vista_facturas_sr_ips` (`ID` bigint(20), `NitEPS` bigint(20), `NitIPS` bigint(20), `NumeroFactura` varchar(20), `FechaFactura` date, `NumeroCuentaGlobal` varchar(20), `NumeroRadicado` varchar(20), `FechaRadicado` date, `TipoNegociacion` enum('EVENTO','CAPITA'), `NumeroContrato` varchar(70), `DiasPactados` int(3), `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION'), `ValorDocumento` double(15,2), `ValorGlosaInicial` double(15,2), `ValorGlosaAceptada` double(15,2), `ValorGlosaConciliada` double(15,2), `ValorDescuentoBdua` double(15,2), `ValorAnticipos` double(15,2), `ValorRetencion` double(15,2), `ValorTotalpagar` double(15,2), `FechaHasta` date, `Soporte` varchar(200), `idUser` int(11), `FechaRegistro` datetime, `FechaActualizacion` datetime, `FlagUpdate` int(11), `Sync` datetime);


DROP TABLE IF EXISTS `vista_cruce_cartera_asmet`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_cartera_asmet` AS select `t1`.`ID` AS `ID`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`FechaFactura` AS `FechaFactura`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`ValorDocumento` AS `ValorDocumento`,`t2`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,(`t1`.`ValorDocumento` - `t2`.`ValorMenosImpuestos`) AS `Impuestos`,'0' AS `OtrosDescuentos`,(select ifnull((select sum(`pagos_asmet`.`ValorTranferido`) from `pagos_asmet` where (`pagos_asmet`.`NumeroFactura` = `t1`.`NumeroFactura`)),0)) AS `TotalPagos`,(select ifnull((select sum(`anticipos_asmet`.`ValorAnticipado`) from `anticipos_asmet` where (`anticipos_asmet`.`NumeroFactura` = `t1`.`NumeroFactura`)),0)) AS `TotalAnticipos`,(select ifnull((select sum(`glosaseps_asmet`.`ValorTotalGlosa`) from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t1`.`NumeroFactura`)),0)) AS `TotalGlosaInicial`,(select ifnull((select sum(`glosaseps_asmet`.`ValorGlosaFavor`) from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t1`.`NumeroFactura`)),0)) AS `TotalGlosaFavor`,(select ifnull((select sum(`glosaseps_asmet`.`ValorGlosaContra`) from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t1`.`NumeroFactura`)),0)) AS `TotalGlosaContra`,(((select `TotalGlosaInicial`) - (select `TotalGlosaFavor`)) - (select `TotalGlosaContra`)) AS `GlosaXConciliar`,(((((`t1`.`ValorDocumento` - (select `Impuestos`)) - (select `TotalPagos`)) - (select `TotalAnticipos`)) - (select `TotalGlosaFavor`)) - (select `OtrosDescuentos`)) AS `ValorSegunEPS`,`t1`.`ValorTotalpagar` AS `ValorSegunIPS`,((select `ValorSegunEPS`) - (select `ValorSegunIPS`)) AS `Diferencia` from (`carteracargadaips` `t1` join `carteraeps` `t2` on((`t1`.`NumeroFactura` = `t2`.`NumeroFactura`)));

DROP TABLE IF EXISTS `vista_facturas_originales`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_originales` AS select `historial_carteracargada_eps`.`NumeroFactura` AS `NumeroFactura`,`historial_carteracargada_eps`.`ValorOriginal` AS `ValorOriginal`,`historial_carteracargada_eps`.`ValorMenosImpuestos` AS `ValorMenosImpuestos` from `historial_carteracargada_eps` group by `historial_carteracargada_eps`.`NumeroFactura`,`historial_carteracargada_eps`.`FechaFactura` order by `historial_carteracargada_eps`.`FechaFactura`;

DROP TABLE IF EXISTS `vista_facturas_pagadas_no_relacionadas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_pagadas_no_relacionadas` AS select `t1`.`ID` AS `ID`,`t1`.`Nit_IPS` AS `Nit_IPS`,`t1`.`Nit_EPS` AS `Nit_EPS`,`t1`.`Proceso` AS `Proceso`,`t1`.`DescripcionProceso` AS `DescripcionProceso`,`t1`.`Estado` AS `Estado`,`t1`.`Cuenta` AS `Cuenta`,`t1`.`Banco` AS `Banco`,`t1`.`FechaPagoFactura` AS `FechaPagoFactura`,`t1`.`NumeroPago` AS `NumeroPago`,`t1`.`TipoOperacion` AS `TipoOperacion`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`ValorBrutoPagar` AS `ValorBrutoPagar`,`t1`.`ValorDescuento` AS `ValorDescuento`,`t1`.`ValorIva` AS `ValorIva`,`t1`.`ValorRetefuente` AS `ValorRetefuente`,`t1`.`ValorReteiva` AS `ValorReteiva`,`t1`.`ValorReteica` AS `ValorReteica`,`t1`.`ValorOtrasRetenciones` AS `ValorOtrasRetenciones`,`t1`.`ValorCruces` AS `ValorCruces`,`t1`.`ValorAnticipos` AS `ValorAnticipos`,`t1`.`ValorTotal` AS `ValorTotal`,`t1`.`ValorTranferido` AS `ValorTranferido`,`t1`.`Regional` AS `Regional`,`t1`.`llaveCompuesta` AS `llaveCompuesta`,`t1`.`idUser` AS `idUser`,`t1`.`Soporte` AS `Soporte`,`t1`.`FechaRegistro` AS `FechaRegistro`,`t1`.`FechaActualizacion` AS `FechaActualizacion`,`t1`.`Sync` AS `Sync` from `pagos_asmet` `t1` where (not(exists(select 1 from `carteraeps` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_facturas_sr_eps`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_sr_eps` AS select `t1`.`ID` AS `ID`,`t1`.`NitEPS` AS `NitEPS`,`t1`.`CodigoSucursal` AS `CodigoSucursal`,`t1`.`Sucursal` AS `Sucursal`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Descripcion` AS `Descripcion`,`t1`.`RazonSocial` AS `RazonSocial`,`t1`.`Nit_IPS` AS `Nit_IPS`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`Prefijo` AS `Prefijo`,`t1`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`ValorOriginal` AS `ValorOriginal`,`t1`.`idUser` AS `idUser`,`t1`.`FechaRegistro` AS `FechaRegistro`,`t1`.`FechaActualizacion` AS `FechaActualizacion`,`t1`.`Sync` AS `Sync` from `carteraeps` `t1` where (not(exists(select 1 from `carteracargadaips` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_facturas_sr_ips`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_sr_ips` AS select `t1`.`ID` AS `ID`,`t1`.`NitEPS` AS `NitEPS`,`t1`.`NitIPS` AS `NitIPS`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`FechaFactura` AS `FechaFactura`,`t1`.`NumeroCuentaGlobal` AS `NumeroCuentaGlobal`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`TipoNegociacion` AS `TipoNegociacion`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`DiasPactados` AS `DiasPactados`,`t1`.`TipoRegimen` AS `TipoRegimen`,`t1`.`ValorDocumento` AS `ValorDocumento`,`t1`.`ValorGlosaInicial` AS `ValorGlosaInicial`,`t1`.`ValorGlosaAceptada` AS `ValorGlosaAceptada`,`t1`.`ValorGlosaConciliada` AS `ValorGlosaConciliada`,`t1`.`ValorDescuentoBdua` AS `ValorDescuentoBdua`,`t1`.`ValorAnticipos` AS `ValorAnticipos`,`t1`.`ValorRetencion` AS `ValorRetencion`,`t1`.`ValorTotalpagar` AS `ValorTotalpagar`,`t1`.`FechaHasta` AS `FechaHasta`,`t1`.`Soporte` AS `Soporte`,`t1`.`idUser` AS `idUser`,`t1`.`FechaRegistro` AS `FechaRegistro`,`t1`.`FechaActualizacion` AS `FechaActualizacion`,`t1`.`FlagUpdate` AS `FlagUpdate`,`t1`.`Sync` AS `Sync` from `carteracargadaips` `t1` where (not(exists(select 1 from `carteraeps` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

-- 2019-06-02 17:17:45
