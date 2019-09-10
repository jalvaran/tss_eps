-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `actualizacioncarteracargadaips` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NitEPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del entidad promomotora de salud ',
  `NitIPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud ',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura ',
  `FechaFactura` date NOT NULL,
  `NumeroCuentaGlobal` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la cuenta de cobro ',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado ',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura ',
  `TipoNegociacion` enum('EVENTO','CAPITA') COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'NÃºmero del contrato ',
  `DiasPactados` int(3) DEFAULT NULL COMMENT 'Dias que se pactaron para el pago de la factura con eps',
  `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de regimen',
  `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor neto a pagar por la entidad contratante ',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de la glosa inicial que tiene la IPS ',
  `ValorGlosaAceptada` double(15,2) NOT NULL COMMENT 'Valor de la glosa Aceptada por IPS ',
  `ValorGlosaConciliada` double(15,2) NOT NULL COMMENT 'Valor de la glosa conciliada por IPS ',
  `ValorDescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor del Descuento en Adress ',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de los anticipos a IPS',
  `ValorRetencion` double(15,2) NOT NULL COMMENT 'Valor de las rentencionede de la factura',
  `Copagos` double(15,2) NOT NULL,
  `Devoluciones` double(15,2) NOT NULL,
  `Pagos` double(15,2) NOT NULL,
  `ValorTotalpagar` double(15,2) NOT NULL COMMENT 'Valor total a pagar',
  `FechaHasta` date NOT NULL COMMENT 'Fecha hasta donde esta la relacion de la cartera ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro ',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro ',
  `FlagUpdate` int(11) NOT NULL,
  `ConciliadoXIPS` int(1) NOT NULL,
  `ConciliadoXEPS` int(11) NOT NULL,
  `NoRelacionada` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Nit_EPS` (`NitEPS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de cargue de cartera temporal de cargues cartera ips';


CREATE TABLE `anticipos2` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NumeroInterno` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del documento',
  `NumeroAnticipo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del anticipo',
  `FechaAnticipo` date NOT NULL COMMENT 'Fecha del anticipo',
  `DescripcionEgreso` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del anticipo',
  `Observacion` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'observacion del anticipo',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOperacion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la operacion',
  `Fecha` date NOT NULL COMMENT 'Fecha',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `MesServicio` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'variable segun tipo de operacion',
  `DescripcionComplement` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion de complementaria del anticipo',
  `ValorAnticipado` double(15,2) NOT NULL COMMENT 'Valor del anticipo ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `NumeroInterno` (`NumeroInterno`),
  KEY `NumeroAnticipo` (`NumeroAnticipo`),
  KEY `NumeroOperacion` (`NumeroOperacion`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `Fecha` (`Fecha`),
  KEY `MesServicio` (`MesServicio`),
  KEY `ValorAnticipado` (`ValorAnticipado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Anticipos2';


CREATE TABLE `anticipos_asmet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `DescripcionNC` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion de la Nota',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura proveedor ',
  `ValorFactura` double(15,2) NOT NULL COMMENT 'Valor de la factura ',
  `ValorReteiva` double(15,2) NOT NULL COMMENT 'Valor reteiva',
  `ValorRetefuente` double(15,2) NOT NULL COMMENT 'Valor retefuente',
  `ValorMenosImpuestos` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `ValorSaldo` double(15,2) NOT NULL COMMENT 'Valor del saldo',
  `ValorAnticipado` double(15,2) NOT NULL COMMENT 'Valor del anticipo ',
  `NumeroAnticipo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del anticipo',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `keyFile` (`keyFile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Glosas';


CREATE TABLE `carteracargadaips` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NitEPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del entidad promomotora de salud ',
  `NitIPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud ',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura ',
  `FechaFactura` date NOT NULL,
  `NumeroCuentaGlobal` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la cuenta de cobro ',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado ',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura ',
  `TipoNegociacion` enum('EVENTO','CAPITA') COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'NÃºmero del contrato ',
  `DiasPactados` int(3) DEFAULT NULL COMMENT 'Dias que se pactaron para el pago de la factura con eps',
  `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de regimen',
  `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor neto a pagar por la entidad contratante ',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de la glosa inicial que tiene la IPS ',
  `ValorGlosaAceptada` double(15,2) NOT NULL COMMENT 'Valor de la glosa Aceptada por IPS ',
  `ValorGlosaConciliada` double(15,2) NOT NULL COMMENT 'Valor de la glosa conciliada por IPS ',
  `ValorDescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor del Descuento en Adress ',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de los anticipos a IPS',
  `ValorRetencion` double(15,2) NOT NULL COMMENT 'Valor de las rentencionede de la factura',
  `Copagos` double(15,2) NOT NULL,
  `Devoluciones` double(15,2) NOT NULL,
  `Pagos` double(15,2) NOT NULL,
  `ValorTotalpagar` double(15,2) NOT NULL COMMENT 'Valor total a pagar',
  `FechaHasta` date NOT NULL COMMENT 'Fecha hasta donde esta la relacion de la cartera ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro ',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro ',
  `FlagUpdate` int(11) NOT NULL,
  `ConciliadoXIPS` int(1) NOT NULL,
  `ConciliadoXEPS` int(1) NOT NULL,
  `NoRelacionada` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Nit_EPS` (`NitEPS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de cargue de cartera temporal de cargues cartera ips';


CREATE TABLE `carteraeps` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NitEPS` bigint(20) NOT NULL,
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `Sucursal` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'sucursal',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Prefijo` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Prefijo',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `ValorOriginal` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
  `ValorMenosImpuestos` double(15,2) NOT NULL,
  `idUser` int(11) NOT NULL,
  `MesServicio` int(6) NOT NULL,
  `FechaRadicado` date NOT NULL,
  `NumeroRadicado` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `ConciliadoXIPS` int(1) NOT NULL,
  `ConciliadoXEPS` int(1) NOT NULL,
  `Estado` int(11) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NumeroFactura` (`NumeroFactura`),
  KEY `Sync` (`Sync`),
  KEY `Estado` (`Estado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla tibco de asmet mutual';


CREATE TABLE `carteraxedades` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TipoOperacion` int(6) NOT NULL,
  `NumeroDocumento` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la transaccion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado',
  `FechaDocumento` date NOT NULL COMMENT 'Fecha del documento',
  `FechaVencimiento` date NOT NULL COMMENT 'Fecha de vencimiento',
  `ValorCorriente` double(15,2) NOT NULL COMMENT 'Valor de la la operacion',
  `Valor30dias` double(15,2) NOT NULL COMMENT 'Valor a 30 dias',
  `Valor60dias` double(15,2) NOT NULL COMMENT 'Valor a 60 dias',
  `Valor90dias` double(15,2) NOT NULL COMMENT 'Valor a 90 dias',
  `Valor180dias` double(15,2) NOT NULL COMMENT 'Valor a 180 dias',
  `Valor360dias` double(15,2) NOT NULL COMMENT 'Valor a 360 dias',
  `Valor500dias` double(15,2) NOT NULL COMMENT 'Valor a mayor a 500 dias',
  `ValorTotalcartera` double(15,2) NOT NULL COMMENT 'Valor total de la cartera',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo cartera x edadaes';


CREATE TABLE `comprobantesegresoasmet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroComprobante` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del comprobante',
  `FechaComprobante` date NOT NULL COMMENT 'Fecha del comprobante',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `EstadoCheque` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'estado del cheque',
  `Observacion` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'observacion del comprobante',
  `DescripcionEgreso` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del comprobante',
  `Estado` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'estado',
  `CuentaBancaria` bigint(20) NOT NULL COMMENT 'NÃºmero de cuenta a la cual re remite el pago',
  `Banco` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'banco de tranferencia',
  `NumeroInterno` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero interno',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `TipoOperacion2` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion 2',
  `MesServicio` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'variable segun tipo de operacion',
  `Valor1` double(15,2) NOT NULL COMMENT 'Valor 1',
  `Valor2` double(15,2) NOT NULL COMMENT 'Valor 2',
  `Valor3` double(15,2) NOT NULL COMMENT 'Valor 3',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `FlagUpdate` (`FlagUpdate`),
  KEY `idUser` (`idUser`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `NumeroComprobante` (`NumeroComprobante`),
  KEY `FechaComprobante` (`FechaComprobante`),
  KEY `NumeroInterno` (`NumeroInterno`),
  KEY `MesServicio` (`MesServicio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo comprobantes de egreso';


CREATE TABLE `conciliaciones_anulaciones` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Fecha` datetime NOT NULL,
  `idConciliacion` bigint(20) NOT NULL,
  `TipoAnulacion` int(11) NOT NULL,
  `Observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `ValorAnulado` double NOT NULL,
  `idUser` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `conciliaciones_cruces` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `MesServicio` int(5) DEFAULT NULL COMMENT 'AÃ±o y mes que se presta el servicio',
  `FechaFactura` date NOT NULL COMMENT 'Fecha de factura',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Pendientes` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Pendientes',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de radicado',
  `ValorOriginal` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
  `ValorImpuestoCalculado` double(15,2) NOT NULL COMMENT 'Valor de impuesto calculado',
  `ValorImpuestoRetenciones` double(15,2) NOT NULL COMMENT 'Valor de impuesto calculado',
  `ValorMenosImpuesto` double(15,2) NOT NULL COMMENT 'Valor de impuesto calculado',
  `ValorPagos` double(15,2) NOT NULL COMMENT 'Valor de pagos',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de anticipos',
  `ValorCopagos` double(15,2) NOT NULL COMMENT 'Valor de copagos',
  `ValorDevoluciones` double(15,2) NOT NULL COMMENT 'Valor de devoluciones',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de Glosa inicial',
  `ValorGlosaFavor` double(15,2) NOT NULL COMMENT 'Valor de Glosa a favor',
  `ValorGlosaContra` double(15,2) NOT NULL COMMENT 'Valor de Glosa en contra',
  `ValorGlosaconciliar` double(15,2) NOT NULL COMMENT 'Valor de Glosa a conciliar',
  `ValorSaldoEps` double(15,2) NOT NULL COMMENT 'Valor de saldo segun eps',
  `ValorSaldoIps` double(15,2) NOT NULL COMMENT 'Valor de saldo segun ips',
  `ValorDiferencia` double(15,2) NOT NULL COMMENT 'Valor de la diferencia',
  `ConceptoConciliacion` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'concepto por el cual se concilia',
  `ConciliacionAFavorDe` varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  `Observacion` text COLLATE utf8_spanish_ci NOT NULL COMMENT 'observacion por la cual se concilia',
  `Soportes` varchar(80) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Soportes que soportan la conciliacion',
  `ValorConciliacion` double(15,2) NOT NULL COMMENT 'Valor conciliado',
  `ConciliadorIps` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'persona con la que se hace la conciliacion de parte de la ips',
  `FechaConciliacion` date NOT NULL COMMENT 'Fecha en la que se concilia',
  `ViaConciliacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'por que medio se concilia',
  `Estado` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `NumeroContrato` (`NumeroContrato`),
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Estado` (`Estado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Conciliaciones de cruces';


CREATE TABLE `controlcargueseps` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NombreCargue` varchar(65) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del cargue ',
  `ValorCargue` double(15,2) NOT NULL COMMENT 'Valor total del cargue ',
  `Nit_EPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del entidad promomotora de salud ',
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


CREATE TABLE `controlcarguesips` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NombreCargue` varchar(65) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del cargue ',
  `ValorCargue` double(15,2) NOT NULL COMMENT 'Valor total del cargue ',
  `Nit_EPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del entidad promomotora de salud ',
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


CREATE TABLE `copagos_pendientes` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor registrado',
  `NumeroRegistros` int(6) DEFAULT NULL COMMENT 'numero de registros',
  `Aplicados` int(6) DEFAULT NULL COMMENT 'aplicados',
  `Errores` int(6) DEFAULT NULL COMMENT 'errores',
  `NoEnviados` int(6) DEFAULT NULL COMMENT 'no enviados',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de copagos';


CREATE TABLE `devoluciones_pendientes` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor registrado',
  `NumeroRegistros` int(6) DEFAULT NULL COMMENT 'numero de registros',
  `Aplicados` int(6) DEFAULT NULL COMMENT 'aplicados',
  `Errores` int(6) DEFAULT NULL COMMENT 'errores',
  `NoEnviados` int(6) DEFAULT NULL COMMENT 'no enviados',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de devoluciones';


CREATE TABLE `glosaseps_asmet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `Sede` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura proveedor ',
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


CREATE TABLE `historial_carteracargada_eps` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOperacion` int(7) DEFAULT NULL COMMENT 'Numero de Operacion',
  `FechaFactura` date NOT NULL COMMENT 'Fecha de factura',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `Sucursal` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Prefijo` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Prefijo',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `MesServicio` int(5) DEFAULT NULL COMMENT 'AÃ±o y mes que se presta el servicio',
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
  `EnGiro` int(5) DEFAULT NULL COMMENT 'AÃ±o y mes del giro',
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


CREATE TABLE `notas_db_cr_2` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `CodEps` int(4) NOT NULL COMMENT 'Codigo de la Eps',
  `C2` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C3` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C4` datetime NOT NULL COMMENT 'no se conoce el campo',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NombreOperacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la operacion que se realiza',
  `NumeroTransaccion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la transaccion',
  `FechaTransaccion` date NOT NULL COMMENT 'Fecha de la transaccion factura',
  `FechaNumero` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `AnoTransaccion` int(4) DEFAULT NULL COMMENT 'AÃ±o en que se hace la transaccion',
  `MesTransaccion` int(2) DEFAULT NULL COMMENT 'Mes en que se hace la transaccion',
  `DiaTransaccion` int(2) DEFAULT NULL COMMENT 'Dia en que se hace la transaccion',
  `C13` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Tipo` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de documento el origien la sede  y el radicado',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `NombreSucursal` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la Sucursal',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `Proveedor` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `DetalleProveedor` int(4) DEFAULT NULL COMMENT 'Detalla proveedor',
  `NombreDetalleProveedor` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del proveedor',
  `Moneda` int(11) NOT NULL,
  `NombreMoneda` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `CuentaContable` int(20) DEFAULT NULL COMMENT 'NÃºmero de la cuenta contable',
  `nombreCuentaContable` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la cuenta contable',
  `Banco` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `CuentaBancaria` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaAprobacion` date NOT NULL COMMENT 'Fecha de aprobacion',
  `ValorTotal` double(15,2) NOT NULL COMMENT 'Valor de la operacion',
  `ValorPago` double(15,2) NOT NULL COMMENT 'Valor Pagos',
  `ValorCruce` double(15,2) NOT NULL COMMENT 'Valor cruce',
  `ValorSaldo` double(15,2) NOT NULL COMMENT 'Valor Saldo',
  `TipoOperacion2` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOrdenPago` int(10) DEFAULT NULL COMMENT 'Numero orden de pago',
  `FechaNumero2` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `FechaOrdenPago` datetime NOT NULL COMMENT 'no se conoce el campo',
  `NumeroCheque` int(10) DEFAULT NULL COMMENT 'Numero orden de pago',
  `EstadoCheque` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado del Cheque',
  `NumeroAutorizacion` int(7) DEFAULT NULL COMMENT 'Numero de autorizacon',
  `CasoWorkflow` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C42` int(10) DEFAULT NULL COMMENT 'no se conoce el campo',
  `FechaDesconocida` datetime NOT NULL COMMENT 'no se conoce el campo',
  `C44` int(10) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C45` int(2) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C46` int(6) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C47` int(6) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `FlagUpdate` (`FlagUpdate`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `NumeroTransaccion` (`NumeroTransaccion`),
  KEY `NumeroAutorizacion` (`NumeroAutorizacion`),
  KEY `FechaTransaccion` (`FechaTransaccion`),
  KEY `TipoOperacion2` (`TipoOperacion2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Notas debito y credito';


CREATE TABLE `notas_dv_cr` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `CodEps` int(4) NOT NULL COMMENT 'Codigo de la Eps',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NombreOperacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la operacion que se realiza',
  `NumeroTransaccion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la transaccion',
  `FechaTransaccion` date NOT NULL COMMENT 'Fecha de la transaccion factura',
  `AnoTransaccion` int(4) DEFAULT NULL COMMENT 'AÃ±o en que se hace la transaccion',
  `MesTransaccion` int(2) DEFAULT NULL COMMENT 'Mes en que se hace la transaccion',
  `DiaTransaccion` int(2) DEFAULT NULL COMMENT 'Dia en que se hace la transaccion',
  `C9` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `NombreSucursal` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la Sucursal',
  `Estado` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado',
  `C13` int(6) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de documento el origien la sede  y el radicado',
  `Moneda` varchar(6) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de Moneda',
  `C18` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Valortasa` int(2) DEFAULT NULL COMMENT 'Valor de la tasa',
  `FechaTasa` date NOT NULL COMMENT 'Fecha de la tasa',
  `DetalleProveedor` int(4) DEFAULT NULL COMMENT 'Detalla proveedor',
  `NombreDetalleProveedor` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del proveedor',
  `C23` int(3) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Actividad` int(6) DEFAULT NULL COMMENT 'Actividad',
  `C25` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C26` int(12) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C27` int(12) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C28` int(2) DEFAULT NULL COMMENT 'no se conoce el campo',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `C30` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor de la la operacion',
  `TipoFactura` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de factura',
  `C33` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `CasoWorkflow` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Contacto` int(1) DEFAULT NULL COMMENT 'Contacto',
  `C36` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `CondicionComercial` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'condicion comercial',
  `C38` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `TipoDistribucion` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C40` varchar(8) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `FechaValidacionImpuesto` datetime NOT NULL COMMENT 'Fecha del impuesto',
  `Numerovitacora` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C43` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `ClaseDocumento` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C45` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `NombreIdentificacion` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C47` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C48` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C49` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C50` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C51` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C52` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C53` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C54` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C55` date NOT NULL COMMENT 'no se conoce el campo',
  `C56` date NOT NULL COMMENT 'no se conoce el campo',
  `C57` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C58` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C59` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C60` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C61` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C62` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C63` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C64` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C65` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C66` varchar(5) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `NumeroAutorizacion` int(7) DEFAULT NULL COMMENT 'Numero de autorizacon',
  `C68` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C69` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C70` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `EstadoInventario` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C72` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C73` date NOT NULL COMMENT 'no se conoce el campo',
  `C74` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C75` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `AutorizadoPor` int(2) DEFAULT NULL COMMENT 'quien autoriza la operacion',
  `NombreAutorizado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre del que autoriza',
  `C78` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C79` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C80` int(9) DEFAULT NULL COMMENT 'quien autoriza la operacion',
  `ReferenciaContrato` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Referencia del contrato',
  `ClaseFactura` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'clase de factura',
  `C83` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C84` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C85` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C86` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Conceptoflujo` int(2) DEFAULT NULL COMMENT 'concepto de flujo',
  `descricpcionConceptoFlujo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C89` int(4) DEFAULT NULL COMMENT 'no se conoce el campo',
  `CodigoFormapago` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `NombreFormapago` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C92` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C93` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C94` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `NumeroConvenio` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `TerceroBeneficiadoPago` int(15) DEFAULT NULL COMMENT 'no se conoce el campo',
  `NombreTerceroBeneficiado` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre de beneficiario',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `keyFile` (`keyFile`),
  KEY `TipoOperacion` (`TipoOperacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Notas Db-Cr';


CREATE TABLE `notas_pendientes` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor registrado',
  `NumeroRegistros` int(6) DEFAULT NULL COMMENT 'numero de registros',
  `Aplicados` int(6) DEFAULT NULL COMMENT 'aplicados',
  `Errores` int(6) DEFAULT NULL COMMENT 'errores',
  `NoEnviados` int(6) DEFAULT NULL COMMENT 'no enviados',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de nota credito';


CREATE TABLE `pagos_asmet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `Nit_EPS` bigint(20) NOT NULL,
  `Proceso` int(10) NOT NULL COMMENT 'NÃºmero del proceso',
  `DescripcionProceso` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del Proceso',
  `Estado` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado de Pago',
  `Cuenta` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'cuenta',
  `Banco` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Banco en el cual se paga',
  `FechaPagoFactura` date NOT NULL COMMENT 'Fecha de pago de la factura',
  `NumeroPago` int(10) NOT NULL COMMENT 'NÃºmero del comprobante del pago ',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura proveedor ',
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


CREATE TABLE `pagos_asmet_temporal` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `Nit_EPS` bigint(20) NOT NULL,
  `Proceso` int(10) NOT NULL COMMENT 'NÃºmero del proceso',
  `DescripcionProceso` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del Proceso',
  `Estado` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado de Pago',
  `Cuenta` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'cuenta',
  `Banco` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Banco en el cual se paga',
  `FechaPagoFactura` date NOT NULL COMMENT 'Fecha de pago de la factura',
  `NumeroPago` int(10) NOT NULL COMMENT 'NÃºmero del comprobante del pago ',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura proveedor ',
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


CREATE TABLE `pendientes_de_envio` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TablaOrigen` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `Valor` double NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `TablaOrigen` (`TablaOrigen`),
  KEY `NumeroRadicado` (`NumeroRadicado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `radicadospendientes` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `ModalidadContratacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Modalidad de la Contratacion',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `EstadoAuditoria` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado de auditoria',
  `EstadoRadicacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado del radicado',
  `FechaAprobacion` date NOT NULL COMMENT 'Fecha de Aprobacion',
  `UsuarioAprobacion` int(20) DEFAULT NULL COMMENT 'usuario de aprobacion',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`),
  KEY `EstadoRadicacion` (`EstadoRadicacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de radicado';


CREATE TABLE `registro_actualizacion_facturas` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FacturaAnterior` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `FacturaNueva` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


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


CREATE TABLE `registro_liquidacion_contratos_items` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `idContrato` bigint(20) NOT NULL,
  `DepartamentoRadicacion` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `MesServicio` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `ValorFacturado` double NOT NULL,
  `ImpuestosRetencion` double NOT NULL,
  `Devolucion` double NOT NULL,
  `GlosaInicial` double NOT NULL,
  `GlosaFavorEPS` double NOT NULL,
  `NotasCopagos` double NOT NULL,
  `RecuperacionImpuestos` double NOT NULL,
  `OtrosDescuentos` double NOT NULL,
  `ValorPagado` double NOT NULL,
  `Saldo` double NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `idContrato` (`idContrato`),
  KEY `Radicado` (`Radicado`),
  KEY `MesServicio` (`MesServicio`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `idUser` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `retenciones` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `Cuentacontable` bigint(20) NOT NULL COMMENT 'NÃºmero de la cuenta contable',
  `ObservacionCuenta` varchar(70) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Observacion de la Cuenta',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `FechaTransaccion` date NOT NULL COMMENT 'Fecha de transaccion de la factura',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroTransaccion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la Transaccion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `Descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `ValorDebito` double(15,2) NOT NULL COMMENT 'Valor del debito',
  `ValorCredito` double(15,2) NOT NULL COMMENT 'Valor del debito',
  `Saldo` double(15,2) NOT NULL COMMENT 'Saldo',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `idUser` (`idUser`),
  KEY `FlagUpdate` (`keyFile`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `FechaTransaccion` (`FechaTransaccion`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `ValorDebito` (`ValorDebito`),
  KEY `ValorCredito` (`ValorCredito`),
  KEY `NumeroTransaccion` (`NumeroTransaccion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla retenciones generadas';


CREATE TABLE `temporalcarguecarteraeps` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOperacion` int(7) DEFAULT NULL COMMENT 'Numero de Operacion',
  `FechaFactura` date NOT NULL COMMENT 'Fecha de factura',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `Sucursal` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Prefijo` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Prefijo',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `MesServicio` int(5) DEFAULT NULL COMMENT 'AÃ±o y mes que se presta el servicio',
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
  `EnGiro` int(5) DEFAULT NULL COMMENT 'AÃ±o y mes del giro',
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


CREATE TABLE `temporalcarguecarteraips` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NitEPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del entidad promomotora de salud',
  `NitIPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura',
  `FechaFactura` date NOT NULL,
  `NumeroCuentaGlobal` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la cuenta de cobro"',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura',
  `TipoNegociacion` enum('EVENTO','CAPITA') COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'NÃºmero del contrato',
  `DiasPactados` int(3) DEFAULT NULL COMMENT 'Dias que se pactaron para el pago de la factura con eps',
  `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de regimen',
  `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor neto a pagar por la entidad contratante',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de la glosa inicial que tiene la IPS',
  `ValorGlosaAceptada` double(15,2) NOT NULL COMMENT 'Valor de la glosa Aceptada por IPS',
  `ValorGlosaConciliada` double(15,2) NOT NULL COMMENT 'Valor de la glosa conciliada por IPS',
  `ValorDescuentoBdua` double(15,2) NOT NULL COMMENT 'Valor del Descuento en Adress',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de los anticipos a IPS',
  `ValorRetencion` double(15,2) NOT NULL COMMENT 'Valor de las rentencionede de la factura',
  `Copagos` double(15,2) NOT NULL,
  `Devoluciones` double(15,2) NOT NULL,
  `Pagos` double(15,2) NOT NULL,
  `ValorTotalpagar` double(15,2) NOT NULL COMMENT 'Valor total a pagar',
  `FechaHasta` date NOT NULL COMMENT 'Fecha hasta donde esta la relacion de la cartera',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro"',
  `FechaActualizacion` datetime NOT NULL COMMENT 'Fecha que se actualiza el registro',
  `FlagUpdate` int(11) NOT NULL,
  `ConciliadoXIPS` int(1) NOT NULL,
  `ConciliadoXEPS` int(11) NOT NULL,
  `NoRelacionada` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo temporal de cargues cartera ips';


CREATE TABLE `temporal_actualizacion_facturas` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `FacturaAnterior` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `FacturaNueva` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `temporal_anticipos2` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroInterno` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del documento',
  `NumeroAnticipo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del anticipo',
  `FechaAnticipo` date NOT NULL COMMENT 'Fecha del anticipo',
  `DescripcionEgreso` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del anticipo',
  `Observacion` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'observacion del anticipo',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOperacion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la operacion',
  `Fecha` date NOT NULL COMMENT 'Fecha',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `MesServicio` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'variable segun tipo de operacion',
  `DescripcionComplement` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion de complementaria del anticipo',
  `ValorAnticipado` double(15,2) NOT NULL COMMENT 'Valor del anticipo ',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `NumeroInterno` (`NumeroInterno`),
  KEY `NumeroAnticipo` (`NumeroAnticipo`),
  KEY `NumeroOperacion` (`NumeroOperacion`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `Fecha` (`Fecha`),
  KEY `MesServicio` (`MesServicio`),
  KEY `ValorAnticipado` (`ValorAnticipado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Anticipos2';


CREATE TABLE `temporal_anticipos_asmet` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `DescripcionNC` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion de la Nota',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura proveedor ',
  `ValorFactura` double(15,2) NOT NULL COMMENT 'Valor de la factura ',
  `ValorReteiva` double(15,2) NOT NULL COMMENT 'Valor reteiva',
  `ValorRetefuente` double(15,2) NOT NULL COMMENT 'Valor retefuente',
  `ValorMenosImpuestos` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `ValorSaldo` double(15,2) NOT NULL COMMENT 'Valor del saldo',
  `ValorAnticipado` double(15,2) NOT NULL COMMENT 'Valor del anticipo ',
  `NumeroAnticipo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del anticipo',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Glosas';


CREATE TABLE `temporal_carteraxedades` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `TipoOperacion` int(6) NOT NULL,
  `NumeroDocumento` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la transaccion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado',
  `FechaDocumento` date NOT NULL COMMENT 'Fecha del documento',
  `FechaVencimiento` date NOT NULL COMMENT 'Fecha de vencimiento',
  `ValorCorriente` double(15,2) NOT NULL COMMENT 'Valor de la la operacion',
  `Valor30dias` double(15,2) NOT NULL COMMENT 'Valor a 30 dias',
  `Valor60dias` double(15,2) NOT NULL COMMENT 'Valor a 60 dias',
  `Valor90dias` double(15,2) NOT NULL COMMENT 'Valor a 90 dias',
  `Valor180dias` double(15,2) NOT NULL COMMENT 'Valor a 180 dias',
  `Valor360dias` double(15,2) NOT NULL COMMENT 'Valor a 360 dias',
  `Valor500dias` double(15,2) NOT NULL COMMENT 'Valor a mayor a 500 dias',
  `ValorTotalcartera` double(15,2) NOT NULL COMMENT 'Valor total de la cartera',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo cartera x edadaes';


CREATE TABLE `temporal_comprobantesegresoasmet` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroComprobante` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero del comprobante',
  `FechaComprobante` date NOT NULL COMMENT 'Fecha del comprobante',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `EstadoCheque` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'estado del cheque',
  `Observacion` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'observacion del comprobante',
  `DescripcionEgreso` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Descripcion del comprobante',
  `Estado` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'estado',
  `CuentaBancaria` bigint(20) NOT NULL COMMENT 'NÃºmero de cuenta a la cual re remite el pago',
  `Banco` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'banco de tranferencia',
  `NumeroInterno` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero interno',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `TipoOperacion2` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion 2',
  `MesServicio` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'variable segun tipo de operacion',
  `Valor1` double(15,2) NOT NULL COMMENT 'Valor 1',
  `Valor2` double(15,2) NOT NULL COMMENT 'Valor 2',
  `Valor3` double(15,2) NOT NULL COMMENT 'Valor 3',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `FlagUpdate` (`FlagUpdate`),
  KEY `idUser` (`idUser`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `NumeroComprobante` (`NumeroComprobante`),
  KEY `FechaComprobante` (`FechaComprobante`),
  KEY `NumeroInterno` (`NumeroInterno`),
  KEY `MesServicio` (`MesServicio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo comprobantes de egreso';


CREATE TABLE `temporal_glosaseps_asmet` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `Sede` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de Radicacion de la factura',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura proveedor ',
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


CREATE TABLE `temporal_notas_db_cr_2` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `CodEps` int(4) NOT NULL COMMENT 'Codigo de la Eps',
  `C2` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C3` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C4` datetime NOT NULL COMMENT 'no se conoce el campo',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NombreOperacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la operacion que se realiza',
  `NumeroTransaccion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la transaccion',
  `FechaTransaccion` date NOT NULL COMMENT 'Fecha de la transaccion factura',
  `FechaNumero` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `AnoTransaccion` int(4) DEFAULT NULL COMMENT 'AÃ±o en que se hace la transaccion',
  `MesTransaccion` int(2) DEFAULT NULL COMMENT 'Mes en que se hace la transaccion',
  `DiaTransaccion` int(2) DEFAULT NULL COMMENT 'Dia en que se hace la transaccion',
  `C13` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Tipo` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de documento el origien la sede  y el radicado',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `NombreSucursal` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la Sucursal',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `Proveedor` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `DetalleProveedor` int(4) DEFAULT NULL COMMENT 'Detalla proveedor',
  `NombreDetalleProveedor` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del proveedor',
  `Moneda` int(11) NOT NULL,
  `NombreMoneda` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `CuentaContable` int(20) DEFAULT NULL COMMENT 'NÃºmero de la cuenta contable',
  `nombreCuentaContable` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la cuenta contable',
  `Banco` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `CuentaBancaria` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FechaAprobacion` date NOT NULL COMMENT 'Fecha de aprobacion',
  `ValorTotal` double(15,2) NOT NULL COMMENT 'Valor de la operacion',
  `ValorPago` double(15,2) NOT NULL COMMENT 'Valor Pagos',
  `ValorCruce` double(15,2) NOT NULL COMMENT 'Valor cruce',
  `ValorSaldo` double(15,2) NOT NULL COMMENT 'Valor Saldo',
  `TipoOperacion2` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroOrdenPago` int(10) DEFAULT NULL COMMENT 'Numero orden de pago',
  `FechaNumero2` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `FechaOrdenPago` datetime NOT NULL COMMENT 'no se conoce el campo',
  `NumeroCheque` int(10) DEFAULT NULL COMMENT 'Numero orden de pago',
  `EstadoCheque` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado del Cheque',
  `NumeroAutorizacion` int(7) DEFAULT NULL COMMENT 'Numero de autorizacon',
  `CasoWorkflow` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C42` int(10) DEFAULT NULL COMMENT 'no se conoce el campo',
  `FechaDesconocida` datetime NOT NULL COMMENT 'no se conoce el campo',
  `C44` int(10) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C45` int(2) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C46` int(6) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C47` int(6) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `FlagUpdate` (`FlagUpdate`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `NumeroTransaccion` (`NumeroTransaccion`),
  KEY `NumeroAutorizacion` (`NumeroAutorizacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Notas debito y credito';


CREATE TABLE `temporal_notas_dv_cr` (
  `ID` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `CodEps` int(4) NOT NULL COMMENT 'Codigo de la Eps',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NombreOperacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la operacion que se realiza',
  `NumeroTransaccion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la transaccion',
  `FechaTransaccion` date NOT NULL COMMENT 'Fecha de la transaccion factura',
  `AnoTransaccion` int(4) DEFAULT NULL COMMENT 'AÃ±o en que se hace la transaccion',
  `MesTransaccion` int(2) DEFAULT NULL COMMENT 'Mes en que se hace la transaccion',
  `DiaTransaccion` int(2) DEFAULT NULL COMMENT 'Dia en que se hace la transaccion',
  `C9` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
  `NombreSucursal` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la Sucursal',
  `Estado` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado',
  `C13` int(6) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `Descripcion` varchar(300) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de documento el origien la sede  y el radicado',
  `Moneda` varchar(6) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de Moneda',
  `C18` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Valortasa` int(2) DEFAULT NULL COMMENT 'Valor de la tasa',
  `FechaTasa` date NOT NULL COMMENT 'Fecha de la tasa',
  `DetalleProveedor` int(4) DEFAULT NULL COMMENT 'Detalla proveedor',
  `NombreDetalleProveedor` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del proveedor',
  `C23` int(3) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Actividad` int(6) DEFAULT NULL COMMENT 'Actividad',
  `C25` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C26` int(12) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C27` int(12) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C28` int(2) DEFAULT NULL COMMENT 'no se conoce el campo',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `C30` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor de la la operacion',
  `TipoFactura` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo de factura',
  `C33` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `CasoWorkflow` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `Contacto` int(1) DEFAULT NULL COMMENT 'Contacto',
  `C36` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `CondicionComercial` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'condicion comercial',
  `C38` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `TipoDistribucion` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C40` varchar(8) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `FechaValidacionImpuesto` datetime NOT NULL COMMENT 'Fecha del impuesto',
  `Numerovitacora` int(1) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C43` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `ClaseDocumento` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C45` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `NombreIdentificacion` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C47` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C48` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C49` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C50` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C51` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C52` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C53` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C54` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C55` date NOT NULL COMMENT 'no se conoce el campo',
  `C56` date NOT NULL COMMENT 'no se conoce el campo',
  `C57` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `C58` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C59` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C60` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C61` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C62` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C63` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C64` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C65` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C66` varchar(5) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `NumeroAutorizacion` int(7) DEFAULT NULL COMMENT 'Numero de autorizacon',
  `C68` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C69` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C70` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `EstadoInventario` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C72` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C73` date NOT NULL COMMENT 'no se conoce el campo',
  `C74` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C75` varchar(3) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `AutorizadoPor` int(2) DEFAULT NULL COMMENT 'quien autoriza la operacion',
  `NombreAutorizado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre del que autoriza',
  `C78` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C79` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C80` int(9) DEFAULT NULL COMMENT 'quien autoriza la operacion',
  `ReferenciaContrato` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Referencia del contrato',
  `ClaseFactura` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'clase de factura',
  `C83` varchar(4) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C84` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C85` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C86` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `Conceptoflujo` int(2) DEFAULT NULL COMMENT 'concepto de flujo',
  `descricpcionConceptoFlujo` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C89` int(4) DEFAULT NULL COMMENT 'no se conoce el campo',
  `CodigoFormapago` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `NombreFormapago` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C92` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C93` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `C94` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'no se conoce el campo',
  `NumeroConvenio` int(8) DEFAULT NULL COMMENT 'no se conoce el campo',
  `TerceroBeneficiadoPago` int(15) DEFAULT NULL COMMENT 'no se conoce el campo',
  `NombreTerceroBeneficiado` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre de beneficiario',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `keyFile` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `idUser` (`idUser`),
  KEY `FlagUpdate` (`FlagUpdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Archivo de Notas Db-Cr';


CREATE TABLE `temporal_registro_liquidacion_contratos_items` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `idContrato` bigint(20) NOT NULL,
  `DepartamentoRadicacion` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `MesServicio` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroFactura` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `ValorFacturado` double NOT NULL,
  `ImpuestosRetencion` double NOT NULL,
  `Devolucion` double NOT NULL,
  `GlosaInicial` double NOT NULL,
  `GlosaFavorEPS` double NOT NULL,
  `NotasCopagos` double NOT NULL,
  `RecuperacionImpuestos` double NOT NULL,
  `OtrosDescuentos` double NOT NULL,
  `ValorPagado` double NOT NULL,
  `Saldo` double NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL,
  KEY `idContrato` (`idContrato`),
  KEY `Radicado` (`Radicado`),
  KEY `MesServicio` (`MesServicio`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `idUser` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `temporal_retenciones` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `Cuentacontable` bigint(20) NOT NULL COMMENT 'NÃºmero de la cuenta contable',
  `ObservacionCuenta` varchar(70) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Observacion de la Cuenta',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `FechaTransaccion` date NOT NULL COMMENT 'Fecha de transaccion de la factura',
  `TipoOperacion` int(5) DEFAULT NULL COMMENT 'Tipo de Operacion',
  `NumeroTransaccion` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la Transaccion',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
  `Descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'A quien va facturo docuneto id -nombre- id factura asmet',
  `ValorDebito` double(15,2) NOT NULL COMMENT 'Valor del debito',
  `ValorCredito` double(15,2) NOT NULL COMMENT 'Valor del debito',
  `Saldo` double(15,2) NOT NULL COMMENT 'Saldo',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FlagUpdate` int(1) NOT NULL,
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `idUser` (`idUser`),
  KEY `FlagUpdate` (`FlagUpdate`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `FechaTransaccion` (`FechaTransaccion`),
  KEY `TipoOperacion` (`TipoOperacion`),
  KEY `ValorDebito` (`ValorDebito`),
  KEY `ValorCredito` (`ValorCredito`),
  KEY `NumeroTransaccion` (`NumeroTransaccion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla retenciones generadas';


CREATE TABLE `temp_conciliaciones_cruces` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃƒÂºmero de la factura del prestador',
  `MesServicio` int(5) DEFAULT NULL COMMENT 'AÃƒÂ±o y mes que se presta el servicio',
  `FechaFactura` date NOT NULL COMMENT 'Fecha de factura',
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃƒÂºmero de la radicado que genera la EPS',
  `Pendientes` varchar(2) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Pendientes',
  `FechaRadicado` date NOT NULL COMMENT 'Fecha de radicado',
  `ValorOriginal` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
  `ValorImpuestoCalculado` double(15,2) NOT NULL COMMENT 'Valor de impuesto calculado',
  `ValorImpuestoRetenciones` double(15,2) NOT NULL COMMENT 'Valor de impuesto calculado',
  `ValorMenosImpuesto` double(15,2) NOT NULL COMMENT 'Valor de impuesto calculado',
  `ValorPagos` double(15,2) NOT NULL COMMENT 'Valor de pagos',
  `ValorAnticipos` double(15,2) NOT NULL COMMENT 'Valor de anticipos',
  `ValorCopagos` double(15,2) NOT NULL COMMENT 'Valor de copagos',
  `ValorDevoluciones` double(15,2) NOT NULL COMMENT 'Valor de devoluciones',
  `ValorGlosaInicial` double(15,2) NOT NULL COMMENT 'Valor de Glosa inicial',
  `ValorGlosaFavor` double(15,2) NOT NULL COMMENT 'Valor de Glosa a favor',
  `ValorGlosaContra` double(15,2) NOT NULL COMMENT 'Valor de Glosa en contra',
  `ValorGlosaconciliar` double(15,2) NOT NULL COMMENT 'Valor de Glosa a conciliar',
  `ValorSaldoEps` double(15,2) NOT NULL COMMENT 'Valor de saldo segun eps',
  `ValorSaldoIps` double(15,2) NOT NULL COMMENT 'Valor de saldo segun ips',
  `ValorDiferencia` double(15,2) NOT NULL COMMENT 'Valor de la diferencia',
  `ConceptoConciliacion` varchar(50) COLLATE utf8_spanish_ci NOT NULL COMMENT 'concepto por el cual se concilia',
  `ConciliacionAFavorDe` varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  `Observacion` text COLLATE utf8_spanish_ci NOT NULL COMMENT 'observacion por la cual se concilia',
  `Soportes` varchar(80) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Soportes que soportan la conciliacion',
  `ValorConciliacion` double(15,2) NOT NULL COMMENT 'Valor conciliado',
  `ConciliadorIps` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'persona con la que se hace la conciliacion de parte de la ips',
  `FechaConciliacion` date NOT NULL COMMENT 'Fecha en la que se concilia',
  `TotalConciliaciones` double NOT NULL,
  `ViaConciliacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'por que medio se concilia',
  `Estado` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `NumeroContrato` (`NumeroContrato`),
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Estado` (`Estado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Conciliaciones de cruces';


CREATE TABLE `temp_copagos_pendientes` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor registrado',
  `NumeroRegistros` int(6) DEFAULT NULL COMMENT 'numero de registros',
  `Aplicados` int(6) DEFAULT NULL COMMENT 'aplicados',
  `Errores` int(6) DEFAULT NULL COMMENT 'errores',
  `NoEnviados` int(6) DEFAULT NULL COMMENT 'no enviados',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` char(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de copagos';


CREATE TABLE `temp_devoluciones_pendientes` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor registrado',
  `NumeroRegistros` int(6) DEFAULT NULL COMMENT 'numero de registros',
  `Aplicados` int(6) DEFAULT NULL COMMENT 'aplicados',
  `Errores` int(6) DEFAULT NULL COMMENT 'errores',
  `NoEnviados` int(6) DEFAULT NULL COMMENT 'no enviados',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de devoluciones';


CREATE TABLE `temp_notas_pendientes` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor registrado',
  `NumeroRegistros` int(6) DEFAULT NULL COMMENT 'numero de registros',
  `Aplicados` int(6) DEFAULT NULL COMMENT 'aplicados',
  `Errores` int(6) DEFAULT NULL COMMENT 'errores',
  `NoEnviados` int(6) DEFAULT NULL COMMENT 'no enviados',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de nota credito';


CREATE TABLE `temp_radicadospendientes` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la radicado que genera la EPS',
  `Origen` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Origien',
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `FechaRadicacion` date NOT NULL COMMENT 'Fecha de Radicacion',
  `Nit_IPS` bigint(20) NOT NULL COMMENT 'NÃºmero de identificaciÃ³n del prestador de servicios de salud',
  `RazonSocial` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'RazÃ³n social o apellidos y nombre del prestador',
  `TipoContrato` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Tipo del contrato',
  `ModalidadContratacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Modalidad de la Contratacion',
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `Valor` double(15,2) NOT NULL COMMENT 'Valor de la factura menos impuestos de ley',
  `EstadoAuditoria` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado de auditoria',
  `EstadoRadicacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estado del radicado',
  `FechaAprobacion` date NOT NULL COMMENT 'Fecha de Aprobacion',
  `UsuarioAprobacion` int(20) DEFAULT NULL COMMENT 'usuario de aprobacion',
  `Soporte` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Documento que soporta el cargue ',
  `idUser` int(11) NOT NULL,
  `FechaRegistro` datetime NOT NULL COMMENT 'Fecha que se hace el registro',
  `keyFile` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `Nit_IPS` (`Nit_IPS`),
  KEY `TipoContrato` (`TipoContrato`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla Envios de radicado';


CREATE TABLE `vista_consolidacion_contrato_liquidados` (`idContrato` bigint(20), `TotalFacturas` bigint(21), `ValorFacturado` double, `ImpuestosRetencion` double, `Devolucion` double, `GlosaInicial` double, `GlosaFavorEPS` double, `NotasCopagos` double, `RecuperacionImpuestos` double, `OtrosDescuentos` double, `ValorPagadoCON` double, `Saldo` double);


CREATE TABLE `vista_copagos_asmet` (`NumeroFactura` varchar(20), `ValorTotal` double(19,2));


CREATE TABLE `vista_cruce_cartera_asmet` (`ID` bigint(20), `NumeroFactura` varchar(20), `Estado` int(11), `DepartamentoRadicacion` varchar(25), `NoRelacionada` int(1), `FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `Pendientes` varchar(2), `FechaConciliacion` datetime, `FechaRadicado` date, `NumeroContrato` varchar(40), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `ImpuestosSegunASMET` double(19,2), `ValorMenosImpuestos` double(15,2), `TotalPagosNotas` double(19,2), `Capitalizacion` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `DescuentoPGP` double(19,2), `FacturasDevueltas` double(19,2), `NumeroFacturasDevueltasAnticipos` bigint(21), `ValorFacturasDevueltascxpvsant` double(19,2), `FacturasDevueltasCXPVSANT` bigint(21), `TotalCopagos` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaInicial` double(15,2), `TotalGlosaFavor` double(15,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `DevolucionesPresentadas` bigint(21), `FacturasPresentadas` bigint(21), `FacturaActiva` varchar(2), `TotalDevolucionesNotas` double(15,2), `TotalDevoluciones` double(19,2), `CarteraXEdades` double(19,2), `ConciliacionesAFavorEPS` double(19,2), `ConciliacionesAFavorIPS` double(19,2), `ValorSegunEPS` double(19,2), `ValorSegunIPS` double(17,0), `Diferencia` double(19,2), `ValorIPSMenor` varchar(2), `TotalConciliaciones` double(19,2), `TotalAPagar` double(19,2), `ConciliacionesPendientes` varchar(2), `DiferenciaXPagos` int(1));


CREATE TABLE `vista_cruce_cartera_eps` (`ID` bigint(20), `NumeroFactura` varchar(20), `Estado` int(11), `DepartamentoRadicacion` varchar(25), `NoRelacionada` bigint(11), `FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `Pendientes` varchar(2), `FechaConciliacion` datetime, `FechaRadicado` date, `NumeroContrato` varchar(40), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `ImpuestosSegunASMET` double(19,2), `ValorMenosImpuestos` double(15,2), `TotalPagosNotas` double(19,2), `Capitalizacion` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `DescuentoPGP` double(19,2), `FacturasDevueltas` double(19,2), `NumeroFacturasDevueltasAnticipos` bigint(21), `ValorFacturasDevueltascxpvsant` double(19,2), `FacturasDevueltasCXPVSANT` bigint(21), `TotalCopagos` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaInicial` double(15,2), `TotalGlosaFavor` double(15,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `DevolucionesPresentadas` bigint(21), `FacturasPresentadas` bigint(21), `FacturaActiva` varchar(2), `TotalDevolucionesNotas` double(15,2), `TotalDevoluciones` double(19,2), `CarteraXEdades` double(19,2), `ConciliacionesAFavorEPS` double(19,2), `ConciliacionesAFavorIPS` double(19,2), `ValorSegunEPS` double(19,2), `ValorSegunIPS` double(17,0), `Diferencia` double(19,2), `ValorIPSMenor` varchar(2), `TotalConciliaciones` double(19,2), `TotalAPagar` double(19,2), `ConciliacionesPendientes` varchar(2), `DiferenciaXPagos` int(1));


CREATE TABLE `vista_cruce_cartera_eps_no_relacionadas_ips` (`ID` bigint(20), `NumeroFactura` varchar(20), `Estado` int(11), `DepartamentoRadicacion` varchar(25), `NoRelacionada` bigint(11), `FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `Pendientes` varchar(2), `FechaConciliacion` datetime, `FechaRadicado` date, `NumeroContrato` varchar(40), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `ImpuestosSegunASMET` double(19,2), `ValorMenosImpuestos` double(15,2), `TotalPagosNotas` double(19,2), `Capitalizacion` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `DescuentoPGP` double(19,2), `FacturasDevueltas` double(19,2), `NumeroFacturasDevueltasAnticipos` bigint(21), `ValorFacturasDevueltascxpvsant` double(19,2), `FacturasDevueltasCXPVSANT` bigint(21), `TotalCopagos` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaInicial` double(15,2), `TotalGlosaFavor` double(15,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `DevolucionesPresentadas` bigint(21), `FacturasPresentadas` bigint(21), `FacturaActiva` varchar(2), `TotalDevolucionesNotas` double(15,2), `TotalDevoluciones` double(19,2), `CarteraXEdades` double(19,2), `ConciliacionesAFavorEPS` double(19,2), `ConciliacionesAFavorIPS` double(19,2), `ValorSegunEPS` double(19,2), `ValorSegunIPS` double(17,0), `Diferencia` double(19,2), `ValorIPSMenor` varchar(2), `TotalConciliaciones` double(19,2), `TotalAPagar` double(19,2), `ConciliacionesPendientes` varchar(2), `DiferenciaXPagos` int(1));


CREATE TABLE `vista_cruce_cartera_eps_no_relacionadas_ips_completa` (`ID` bigint(20), `NumeroFactura` varchar(20), `Estado` int(11), `DepartamentoRadicacion` varchar(25), `NoRelacionada` bigint(11), `FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `Pendientes` varchar(2), `FechaConciliacion` datetime, `FechaRadicado` date, `NumeroContrato` varchar(40), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `ImpuestosSegunASMET` double(19,2), `ValorMenosImpuestos` double(15,2), `TotalPagosNotas` double(19,2), `Capitalizacion` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `DescuentoPGP` double(19,2), `FacturasDevueltas` double(19,2), `NumeroFacturasDevueltasAnticipos` bigint(21), `ValorFacturasDevueltascxpvsant` double(19,2), `FacturasDevueltasCXPVSANT` bigint(21), `TotalCopagos` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaInicial` double(15,2), `TotalGlosaFavor` double(15,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `DevolucionesPresentadas` bigint(21), `FacturasPresentadas` bigint(21), `FacturaActiva` varchar(2), `TotalDevolucionesNotas` double(15,2), `TotalDevoluciones` double(19,2), `CarteraXEdades` double(19,2), `ConciliacionesAFavorEPS` double(19,2), `ConciliacionesAFavorIPS` double(19,2), `ValorSegunEPS` double(19,2), `ValorSegunIPS` double(17,0), `Diferencia` double(19,2), `ValorIPSMenor` varchar(2), `TotalConciliaciones` double(19,2), `TotalAPagar` double(19,2), `ConciliacionesPendientes` varchar(2), `DiferenciaXPagos` int(1));


CREATE TABLE `vista_cruce_cartera_eps_relacionadas_ips` (`ID` bigint(20), `NumeroFactura` varchar(20), `Estado` int(11), `DepartamentoRadicacion` varchar(25), `NoRelacionada` bigint(11), `FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `Pendientes` varchar(2), `FechaConciliacion` datetime, `FechaRadicado` date, `NumeroContrato` varchar(40), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `ImpuestosSegunASMET` double(19,2), `ValorMenosImpuestos` double(15,2), `TotalPagosNotas` double(19,2), `Capitalizacion` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `DescuentoPGP` double(19,2), `FacturasDevueltas` double(19,2), `NumeroFacturasDevueltasAnticipos` bigint(21), `ValorFacturasDevueltascxpvsant` double(19,2), `FacturasDevueltasCXPVSANT` bigint(21), `TotalCopagos` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaInicial` double(15,2), `TotalGlosaFavor` double(15,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `DevolucionesPresentadas` bigint(21), `FacturasPresentadas` bigint(21), `FacturaActiva` varchar(2), `TotalDevolucionesNotas` double(15,2), `TotalDevoluciones` double(19,2), `CarteraXEdades` double(19,2), `ConciliacionesAFavorEPS` double(19,2), `ConciliacionesAFavorIPS` double(19,2), `ValorSegunEPS` double(19,2), `ValorSegunIPS` double(17,0), `Diferencia` double(19,2), `ValorIPSMenor` varchar(2), `TotalConciliaciones` double(19,2), `TotalAPagar` double(19,2), `ConciliacionesPendientes` varchar(2), `DiferenciaXPagos` int(1));


CREATE TABLE `vista_cruce_cartera_eps_sin_relacion_segun_ags` (`FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `FechaRadicado` date, `NumeroContrato` varchar(40), `NumeroFactura` varchar(20), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `TotalCopagos` double(19,2), `DescuentoPGP` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaFavor` double(19,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `TotalDevoluciones` double(19,2), `ValorSegunEPS` varchar(1), `ValorSegunIPS` varchar(1), `Diferencia` varchar(1));


CREATE TABLE `vista_cruce_totales_actas_conciliaciones` (`NumeroFactura` varchar(20), `Diferencia` double(19,2), `MesServicio` int(6), `DiferenciaXPagos` double(19,2), `DiferenciaXAnticipos` double(19,2), `DiferenciaXCopagos` double(19,2), `DiferenciaXDescuentoPGP` double(19,2), `DiferenciaXOtrosDescuentos` double(19,2), `DiferenciaXAjustesCartera` double(19,2), `DiferenciaXGlosaFavorEPS` double(19,2), `DiferenciaXGlosaContraEPS` double(19,2), `DiferenciaXGlosaXConciliar` double(19,2), `DiferenciaXDevoluciones` double(19,2), `DiferenciaXImpuestos` double(19,2), `DiferenciaXValorFacturado` double(19,2), `TotalDiferenciasComunes` double(19,2), `DiferenciaXDevolucionesNoIPS` double(19,2), `GlosasXConciliar2` double(19,2), `XPagos2` double(19,2), `DiferenciaVariada` double(19,2));


CREATE TABLE `vista_facturasdvueltas_anticiposvscxp` (`Numero_concidencia` varchar(20), `Numerofactura_anticipos` varchar(20), `Numerofactura_cxp` varchar(20), `TipoOperacioncxp` bigint(11), `TipoOperacionanticipos` varchar(20), `ValorDevuelto` double(15,2));


CREATE TABLE `vista_facturas_originales` (`NumeroFactura` varchar(20), `ValorOriginal` double(15,2), `ValorMenosImpuestos` double(15,2));


CREATE TABLE `vista_facturas_pagadas_no_relacionadas` (`ID` bigint(20), `Nit_IPS` bigint(20), `Nit_EPS` bigint(20), `Proceso` int(10), `DescripcionProceso` varchar(100), `Estado` varchar(30), `Cuenta` varchar(30), `Banco` varchar(50), `FechaPagoFactura` date, `NumeroPago` int(10), `TipoOperacion` int(5), `NumeroFactura` varchar(20), `ValorBrutoPagar` double(15,2), `ValorDescuento` double(15,2), `ValorIva` double(15,2), `ValorRetefuente` double(15,2), `ValorReteiva` double(15,2), `ValorReteica` double(15,2), `ValorOtrasRetenciones` double(15,2), `ValorCruces` double(15,2), `ValorAnticipos` double(15,2), `ValorTotal` double(15,2), `ValorTranferido` double(15,2), `Regional` varchar(25), `llaveCompuesta` varchar(45), `idUser` int(11), `Soporte` varchar(100), `FechaRegistro` datetime, `FechaActualizacion` datetime, `Sync` datetime);


CREATE TABLE `vista_facturas_sr_eps` (`ID` bigint(20), `NitEPS` bigint(20), `CodigoSucursal` int(6), `Sucursal` varchar(25), `NumeroFactura` varchar(20), `Descripcion` varchar(300), `RazonSocial` varchar(60), `Nit_IPS` bigint(20), `NumeroContrato` varchar(40), `Prefijo` varchar(4), `DepartamentoRadicacion` varchar(25), `ValorOriginal` double(15,2), `idUser` int(11), `FechaRegistro` datetime, `FechaActualizacion` datetime, `Sync` datetime);


CREATE TABLE `vista_facturas_sr_eps_2` (`ID` bigint(20), `NitEPS` bigint(20), `CodigoSucursal` int(6), `Sucursal` varchar(25), `NumeroFactura` varchar(20), `Descripcion` varchar(300), `RazonSocial` varchar(60), `Nit_IPS` bigint(20), `NumeroContrato` varchar(40), `Prefijo` varchar(4), `DepartamentoRadicacion` varchar(25), `ValorOriginal` double(15,2), `ValorMenosImpuestos` double(15,2), `idUser` int(11), `MesServicio` int(6), `FechaRadicado` date, `NumeroRadicado` varchar(90), `FechaRegistro` datetime, `FechaActualizacion` datetime, `ConciliadoXIPS` int(1), `ConciliadoXEPS` int(1), `Estado` int(11), `Sync` datetime, `TotalDevoluciones` double(19,2), `TotalRetenciones` double(19,2), `TotalPagos` double(19,2), `Saldo` double(19,2), `ValorImpuestosCalculados` double(19,2));


CREATE TABLE `vista_facturas_sr_eps_3` (`ID` bigint(20), `NitEPS` bigint(20), `CodigoSucursal` int(6), `Sucursal` varchar(25), `NumeroFactura` varchar(20), `Descripcion` varchar(300), `RazonSocial` varchar(60), `Nit_IPS` bigint(20), `NumeroContrato` varchar(40), `Prefijo` varchar(4), `DepartamentoRadicacion` varchar(25), `ValorOriginal` double(15,2), `ValorMenosImpuestos` double(15,2), `idUser` int(11), `MesServicio` int(6), `FechaRadicado` date, `NumeroRadicado` varchar(90), `FechaRegistro` datetime, `FechaActualizacion` datetime, `ConciliadoXIPS` int(1), `ConciliadoXEPS` int(1), `Estado` int(11), `Sync` datetime, `TotalDevoluciones` double(19,2), `TotalRetenciones` double(19,2), `TotalPagos` double(19,2), `Saldo` double(19,2), `ValorImpuestosCalculados` double(19,2));


CREATE TABLE `vista_facturas_sr_ips` (`ID` bigint(20), `NitEPS` bigint(20), `NitIPS` bigint(20), `NumeroFactura` varchar(20), `FechaFactura` date, `NumeroCuentaGlobal` varchar(20), `NumeroRadicado` varchar(20), `FechaRadicado` date, `TipoNegociacion` enum('EVENTO','CAPITA'), `NumeroContrato` varchar(70), `DiasPactados` int(3), `TipoRegimen` enum('SUBSIDIADO','CONTRIBUTIVO','REGIMEN ESPECIAL','ENTE TERRITORIAL','ENTE MUNICIPAL','OTRAS ENTIDADES','ENTIDAD EN LIQUIDACION'), `ValorDocumento` double(15,2), `ValorGlosaInicial` double(15,2), `ValorGlosaAceptada` double(15,2), `ValorGlosaConciliada` double(15,2), `ValorDescuentoBdua` double(15,2), `ValorAnticipos` double(15,2), `ValorRetencion` double(15,2), `ValorTotalpagar` double(15,2), `FechaHasta` date, `Soporte` varchar(200), `idUser` int(11), `FechaRegistro` datetime, `FechaActualizacion` datetime, `FlagUpdate` int(11), `Sync` datetime);


CREATE TABLE `vista_hoja_trabajo_cruce` (`ID` bigint(20), `NumeroFactura` varchar(20), `Estado` int(11), `DepartamentoRadicacion` varchar(25), `NoRelacionada` bigint(11), `FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `Pendientes` varchar(2), `FechaConciliacion` datetime, `FechaRadicado` date, `NumeroContrato` varchar(40), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `ImpuestosSegunASMET` double(19,2), `ValorMenosImpuestos` double(15,2), `TotalPagosNotas` double(19,2), `Capitalizacion` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `DescuentoPGP` double(19,2), `FacturasDevueltas` double(19,2), `NumeroFacturasDevueltasAnticipos` bigint(21), `ValorFacturasDevueltascxpvsant` double(19,2), `FacturasDevueltasCXPVSANT` bigint(21), `TotalCopagos` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaInicial` double(15,2), `TotalGlosaFavor` double(15,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `DevolucionesPresentadas` bigint(21), `FacturasPresentadas` bigint(21), `FacturaActiva` varchar(2), `TotalDevolucionesNotas` double(15,2), `TotalDevoluciones` double(19,2), `CarteraXEdades` double(19,2), `ConciliacionesAFavorEPS` double(19,2), `ConciliacionesAFavorIPS` double(19,2), `ValorSegunEPS` double(19,2), `ValorSegunIPS` double(17,0), `Diferencia` double(19,2), `ValorIPSMenor` varchar(2), `TotalConciliaciones` double(19,2), `TotalAPagar` double(19,2), `ConciliacionesPendientes` varchar(2), `DiferenciaXPagos` int(1));


CREATE TABLE `vista_pendientes` (`Radicados` varchar(12), `NumeroRadicado` varchar(20), `Total` double(19,2));


CREATE TABLE `vista_reporte_ips` (`FechaFactura` date, `MesServicio` int(6), `NumeroRadicado` varchar(90), `FechaRadicado` date, `NumeroContrato` varchar(40), `NumeroFactura` varchar(20), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `TotalCopagos` double(19,2), `DescuentoPGP` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaFavor` double(15,2), `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `TotalDevoluciones` double(19,2), `ValorSegunEPS` double(19,2), `ValorSegunIPS` double(17,0), `Diferencia` double(19,2));


CREATE TABLE `vista_reporte_ips_completo` (`FechaFactura` date, `MesServicio` int(11), `NumeroRadicado` varchar(90), `FechaRadicado` date, `NumeroContrato` varchar(40), `NumeroFactura` varchar(20), `ValorDocumento` double(15,2), `Impuestos` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `TotalCopagos` double(19,2), `DescuentoPGP` double(19,2), `OtrosDescuentos` double(19,2), `AjustesCartera` double(19,2), `TotalGlosaFavor` double, `TotalGlosaContra` double(15,2), `GlosaXConciliar` double(19,2), `TotalDevoluciones` double(19,2), `ValorSegunEPS` varchar(19), `ValorSegunIPS` varchar(17), `Diferencia` varchar(19));


CREATE TABLE `vista_resumen_cruce_cartera_asmet` (`NumeroContrato` varchar(40), `TotalFacturas` double(19,2), `Impuestos` double(19,2), `TotalMenosImpuestos` double(19,2), `TotalOtrosDescuentos` double(19,2), `TotalPagos` double(19,2), `TotalAnticipos` double(19,2), `TotalGlosaInicial` double(19,2), `TotalGlosaFavor` double(19,2), `TotalGlosaContra` double(19,2), `TotalGlosaXConciliar` double(19,2), `TotalCopagos` double(19,2), `TotalDevoluciones` double(19,2), `ValorSegunEPS` double(19,2));


CREATE TABLE `vista_retenciones_facturas` (`NumeroFactura` varchar(20), `Debitos` double(19,2), `Creditos` double(19,2));


CREATE TABLE `vista_ultimas_facturas_cartera_eps` (`NumeroFactura` varchar(20), `FechaFactura` date, `NumeroRadicado` varchar(20), `MesServicio` int(5), `ValorOriginal` double(15,2));


DROP TABLE IF EXISTS `vista_consolidacion_contrato_liquidados`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_consolidacion_contrato_liquidados` AS select `registro_liquidacion_contratos_items`.`idContrato` AS `idContrato`,count(`registro_liquidacion_contratos_items`.`NumeroFactura`) AS `TotalFacturas`,sum(`registro_liquidacion_contratos_items`.`ValorFacturado`) AS `ValorFacturado`,sum(`registro_liquidacion_contratos_items`.`ImpuestosRetencion`) AS `ImpuestosRetencion`,sum(`registro_liquidacion_contratos_items`.`Devolucion`) AS `Devolucion`,sum(`registro_liquidacion_contratos_items`.`GlosaInicial`) AS `GlosaInicial`,sum(`registro_liquidacion_contratos_items`.`GlosaFavorEPS`) AS `GlosaFavorEPS`,sum(`registro_liquidacion_contratos_items`.`NotasCopagos`) AS `NotasCopagos`,sum(`registro_liquidacion_contratos_items`.`RecuperacionImpuestos`) AS `RecuperacionImpuestos`,sum(`registro_liquidacion_contratos_items`.`OtrosDescuentos`) AS `OtrosDescuentos`,sum(`registro_liquidacion_contratos_items`.`ValorPagado`) AS `ValorPagadoCON`,sum(`registro_liquidacion_contratos_items`.`Saldo`) AS `Saldo` from `registro_liquidacion_contratos_items` group by `registro_liquidacion_contratos_items`.`idContrato`;

DROP TABLE IF EXISTS `vista_copagos_asmet`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_copagos_asmet` AS select `notas_db_cr_2`.`NumeroFactura` AS `NumeroFactura`,sum(abs(`notas_db_cr_2`.`ValorTotal`)) AS `ValorTotal` from `notas_db_cr_2` where ((`notas_db_cr_2`.`TipoOperacion` = '2258') or (`notas_db_cr_2`.`TipoOperacion` = '2225') or (`notas_db_cr_2`.`TipoOperacion` = '2260') or (`notas_db_cr_2`.`TipoOperacion` = '2254') or (`notas_db_cr_2`.`TipoOperacion` = '2218') or (`notas_db_cr_2`.`TipoOperacion` = '2220') or (`notas_db_cr_2`.`TipoOperacion` = '2402')) group by `notas_db_cr_2`.`NumeroFactura`;

DROP TABLE IF EXISTS `vista_cruce_cartera_asmet`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_cartera_asmet` AS select `t2`.`ID` AS `ID`,`t2`.`NumeroFactura` AS `NumeroFactura`,`t2`.`Estado` AS `Estado`,`t2`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`NoRelacionada` AS `NoRelacionada`,(select `carteracargadaips`.`FechaFactura` from `carteracargadaips` where (`carteracargadaips`.`NumeroFactura` = `t2`.`NumeroFactura`) limit 1) AS `FechaFactura`,`t2`.`MesServicio` AS `MesServicio`,`t2`.`NumeroRadicado` AS `NumeroRadicado`,(select ifnull((select 'SI' from `pendientes_de_envio` where (`pendientes_de_envio`.`NumeroRadicado` = `t2`.`NumeroRadicado`) limit 1),'NO')) AS `Pendientes`,(select `conciliaciones_cruces`.`FechaRegistro` from `conciliaciones_cruces` where ((`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`t2`.`Estado` = 1)) order by `conciliaciones_cruces`.`FechaRegistro` desc limit 1) AS `FechaConciliacion`,`t2`.`FechaRadicado` AS `FechaRadicado`,`t2`.`NumeroContrato` AS `NumeroContrato`,`t2`.`ValorOriginal` AS `ValorDocumento`,(`t2`.`ValorOriginal` - `t2`.`ValorMenosImpuestos`) AS `Impuestos`,(select ifnull((select (`vista_retenciones_facturas`.`Creditos` - `vista_retenciones_facturas`.`Debitos`) from `vista_retenciones_facturas` where (`vista_retenciones_facturas`.`NumeroFactura` = `t2`.`NumeroFactura`)),0)) AS `ImpuestosSegunASMET`,`t2`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,(select ifnull((select sum(`notas_db_cr_2`.`ValorPago`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and ((`notas_db_cr_2`.`TipoOperacion2` = '3090') or (`notas_db_cr_2`.`TipoOperacion2` = '3070') or (`notas_db_cr_2`.`TipoOperacion2` = '3071') or (`notas_db_cr_2`.`TipoOperacion2` = '3072') or (`notas_db_cr_2`.`TipoOperacion2` = '3086') or (`notas_db_cr_2`.`TipoOperacion2` = '3089') or (`notas_db_cr_2`.`TipoOperacion2` = '3090') or (`notas_db_cr_2`.`TipoOperacion2` = '3091') or (`notas_db_cr_2`.`TipoOperacion2` = '2260')) and (`notas_db_cr_2`.`TipoOperacion` <> '2103'))),0)) AS `TotalPagosNotas`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2299'))),0)) AS `Capitalizacion`,((select abs(`TotalPagosNotas`)) + (select abs(`Capitalizacion`))) AS `TotalPagos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2216'))),0)) AS `TotalAnticipos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2275'))),0)) AS `DescuentoPGP`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2039'))),0)) AS `FacturasDevueltas`,(select ifnull((select count(`anticipos2`.`NumeroFactura`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2039'))),0)) AS `NumeroFacturasDevueltasAnticipos`,(select ifnull((select sum(`vista_facturasdvueltas_anticiposvscxp`.`ValorDevuelto`) from `vista_facturasdvueltas_anticiposvscxp` where ((`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` = `t2`.`NumeroFactura`) and (`vista_facturasdvueltas_anticiposvscxp`.`TipoOperacionanticipos` = '2259') and (`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` <> `vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_cxp`))),0)) AS `ValorFacturasDevueltascxpvsant`,(select ifnull((select count(`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos`) from `vista_facturasdvueltas_anticiposvscxp` where ((`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` = `t2`.`NumeroFactura`) and (`vista_facturasdvueltas_anticiposvscxp`.`TipoOperacionanticipos` = '2259') and (`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` <> `vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_cxp`))),0)) AS `FacturasDevueltasCXPVSANT`,(select ifnull((select sum(`vista_copagos_asmet`.`ValorTotal`) from `vista_copagos_asmet` where (`vista_copagos_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`)),0)) AS `TotalCopagos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and ((`anticipos2`.`NumeroInterno` = '2215') or (`anticipos2`.`NumeroInterno` = '2601') or (`anticipos2`.`NumeroInterno` = '2214')))),0)) AS `OtrosDescuentos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2260'))),0)) AS `AjustesCartera`,(select ifnull((select `glosaseps_asmet`.`ValorTotalGlosa` from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`) order by `glosaseps_asmet`.`FechaRegistro` desc limit 1),0)) AS `TotalGlosaInicial`,(select ifnull((select `glosaseps_asmet`.`ValorGlosaFavor` from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`) order by `glosaseps_asmet`.`FechaRegistro` desc limit 1),0)) AS `TotalGlosaFavor`,(select ifnull((select `glosaseps_asmet`.`ValorGlosaContra` from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`) order by `glosaseps_asmet`.`FechaRegistro` desc limit 1),0)) AS `TotalGlosaContra`,(((select `TotalGlosaInicial`) - (select `TotalGlosaFavor`)) - (select `TotalGlosaContra`)) AS `GlosaXConciliar`,(select ifnull((select count(distinct `notas_db_cr_2`.`NumeroTransaccion`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`notas_db_cr_2`.`C13` <> 'N') and ((`notas_db_cr_2`.`TipoOperacion` = '2259') or (`notas_db_cr_2`.`TipoOperacion` = '2269') or (`notas_db_cr_2`.`TipoOperacion` = '2039')))),0)) AS `DevolucionesPresentadas`,(select ifnull((select count(distinct `notas_db_cr_2`.`NumeroTransaccion`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`notas_db_cr_2`.`C13` <> 'N') and (`notas_db_cr_2`.`TipoOperacion` like '20%'))),0)) AS `FacturasPresentadas`,(select if(((select `FacturasPresentadas`) > (((select `DevolucionesPresentadas`) + (select `NumeroFacturasDevueltasAnticipos`)) + (select `FacturasDevueltasCXPVSANT`))),'SI','NO')) AS `FacturaActiva`,(select if((`FacturaActiva` = 'SI'),0,(select ifnull((select `notas_db_cr_2`.`ValorTotal` from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and ((`notas_db_cr_2`.`TipoOperacion` = '2259') or (`notas_db_cr_2`.`TipoOperacion` = '2269') or (`notas_db_cr_2`.`TipoOperacion` = '2039')) and (`notas_db_cr_2`.`FechaTransaccion` >= `t2`.`FechaRadicado`)) limit 1),0)))) AS `TotalDevolucionesNotas`,(select if((`FacturaActiva` = 'SI'),0,(((select abs(`TotalDevolucionesNotas`)) + (select abs(`FacturasDevueltas`))) + (select if((`FacturasDevueltasCXPVSANT` = `DevolucionesPresentadas`),0,(select abs(`ValorFacturasDevueltascxpvsant`))))))) AS `TotalDevoluciones`,(select ifnull((select sum(`carteraxedades`.`ValorTotalcartera`) from `carteraxedades` where (`carteraxedades`.`NumeroFactura` = `t2`.`NumeroFactura`) limit 1),0)) AS `CarteraXEdades`,(select ifnull((select sum(`conciliaciones_cruces`.`ValorConciliacion`) from `conciliaciones_cruces` where ((`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`conciliaciones_cruces`.`ConciliacionAFavorDe` = 1))),0)) AS `ConciliacionesAFavorEPS`,(select ifnull((select sum(`conciliaciones_cruces`.`ValorConciliacion`) from `conciliaciones_cruces` where ((`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`conciliaciones_cruces`.`ConciliacionAFavorDe` = 2))),0)) AS `ConciliacionesAFavorIPS`,((((((((((`t2`.`ValorMenosImpuestos` - (select `TotalPagos`)) - (select `TotalAnticipos`)) - (select `TotalGlosaFavor`)) - (select `GlosaXConciliar`)) - (select `OtrosDescuentos`)) - (select abs(`TotalCopagos`))) - (select abs(`TotalDevoluciones`))) - (select abs(`DescuentoPGP`))) - (select abs(`ConciliacionesAFavorEPS`))) + (select abs(`ConciliacionesAFavorIPS`))) AS `ValorSegunEPS`,(select ifnull((select round(`carteracargadaips`.`ValorTotalpagar`,0) from `carteracargadaips` where (`carteracargadaips`.`NumeroFactura` = `t2`.`NumeroFactura`) limit 1),0)) AS `ValorSegunIPS`,((select `ValorSegunEPS`) - (select `ValorSegunIPS`)) AS `Diferencia`,(select if((select (`Diferencia` > 0)),'SI','NO')) AS `ValorIPSMenor`,(select ifnull((select sum(`conciliaciones_cruces`.`ValorConciliacion`) from `conciliaciones_cruces` where (`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`)),0)) AS `TotalConciliaciones`,(select `ValorSegunEPS`) AS `TotalAPagar`,(select if((select ((round(`TotalConciliaciones`,2) <> (select round(abs(`Diferencia`),2))) and (select (`TotalConciliaciones` > 0)))),'SI','NO')) AS `ConciliacionesPendientes`,(select if(((select abs(`TotalPagos`)) = (select abs(`Diferencia`))),1,0)) AS `DiferenciaXPagos` from (`carteracargadaips` `t1` join `carteraeps` `t2` on((`t1`.`NumeroFactura` = `t2`.`NumeroFactura`)));

DROP TABLE IF EXISTS `vista_cruce_cartera_eps`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_cartera_eps` AS select `t2`.`ID` AS `ID`,`t2`.`NumeroFactura` AS `NumeroFactura`,`t2`.`Estado` AS `Estado`,`t2`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,(select `carteracargadaips`.`NoRelacionada` from `carteracargadaips` where (`carteracargadaips`.`NumeroFactura` = `t2`.`NumeroFactura`) limit 1) AS `NoRelacionada`,(select `carteracargadaips`.`FechaFactura` from `carteracargadaips` where (`carteracargadaips`.`NumeroFactura` = `t2`.`NumeroFactura`) limit 1) AS `FechaFactura`,`t2`.`MesServicio` AS `MesServicio`,`t2`.`NumeroRadicado` AS `NumeroRadicado`,(select ifnull((select 'SI' from `pendientes_de_envio` where (`pendientes_de_envio`.`NumeroRadicado` = `t2`.`NumeroRadicado`) limit 1),'NO')) AS `Pendientes`,(select `conciliaciones_cruces`.`FechaRegistro` from `conciliaciones_cruces` where ((`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`t2`.`Estado` = 1)) order by `conciliaciones_cruces`.`FechaRegistro` desc limit 1) AS `FechaConciliacion`,`t2`.`FechaRadicado` AS `FechaRadicado`,`t2`.`NumeroContrato` AS `NumeroContrato`,`t2`.`ValorOriginal` AS `ValorDocumento`,(`t2`.`ValorOriginal` - `t2`.`ValorMenosImpuestos`) AS `Impuestos`,(select ifnull((select (`vista_retenciones_facturas`.`Creditos` - `vista_retenciones_facturas`.`Debitos`) from `vista_retenciones_facturas` where (`vista_retenciones_facturas`.`NumeroFactura` = `t2`.`NumeroFactura`)),0)) AS `ImpuestosSegunASMET`,`t2`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,(select ifnull((select sum(`notas_db_cr_2`.`ValorPago`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and ((`notas_db_cr_2`.`TipoOperacion2` = '3090') or (`notas_db_cr_2`.`TipoOperacion2` = '3070') or (`notas_db_cr_2`.`TipoOperacion2` = '3071') or (`notas_db_cr_2`.`TipoOperacion2` = '3072') or (`notas_db_cr_2`.`TipoOperacion2` = '3086') or (`notas_db_cr_2`.`TipoOperacion2` = '3089') or (`notas_db_cr_2`.`TipoOperacion2` = '3090') or (`notas_db_cr_2`.`TipoOperacion2` = '3091') or (`notas_db_cr_2`.`TipoOperacion2` = '2260')) and (`notas_db_cr_2`.`TipoOperacion` <> '2103'))),0)) AS `TotalPagosNotas`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2299'))),0)) AS `Capitalizacion`,((select abs(`TotalPagosNotas`)) + (select abs(`Capitalizacion`))) AS `TotalPagos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2216'))),0)) AS `TotalAnticipos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2275'))),0)) AS `DescuentoPGP`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2039'))),0)) AS `FacturasDevueltas`,(select ifnull((select count(`anticipos2`.`NumeroFactura`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2039'))),0)) AS `NumeroFacturasDevueltasAnticipos`,(select ifnull((select sum(`vista_facturasdvueltas_anticiposvscxp`.`ValorDevuelto`) from `vista_facturasdvueltas_anticiposvscxp` where ((`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` = `t2`.`NumeroFactura`) and (`vista_facturasdvueltas_anticiposvscxp`.`TipoOperacionanticipos` = '2259') and (`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` <> `vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_cxp`))),0)) AS `ValorFacturasDevueltascxpvsant`,(select ifnull((select count(`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos`) from `vista_facturasdvueltas_anticiposvscxp` where ((`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` = `t2`.`NumeroFactura`) and (`vista_facturasdvueltas_anticiposvscxp`.`TipoOperacionanticipos` = '2259') and (`vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_anticipos` <> `vista_facturasdvueltas_anticiposvscxp`.`Numerofactura_cxp`))),0)) AS `FacturasDevueltasCXPVSANT`,(select ifnull((select sum(`vista_copagos_asmet`.`ValorTotal`) from `vista_copagos_asmet` where (`vista_copagos_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`)),0)) AS `TotalCopagos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and ((`anticipos2`.`NumeroInterno` = '2215') or (`anticipos2`.`NumeroInterno` = '2601') or (`anticipos2`.`NumeroInterno` = '2214')))),0)) AS `OtrosDescuentos`,(select ifnull((select sum(`anticipos2`.`ValorAnticipado`) from `anticipos2` where ((`anticipos2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`anticipos2`.`NumeroInterno` = '2260'))),0)) AS `AjustesCartera`,(select ifnull((select `glosaseps_asmet`.`ValorTotalGlosa` from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`) order by `glosaseps_asmet`.`FechaRegistro` desc limit 1),0)) AS `TotalGlosaInicial`,(select ifnull((select `glosaseps_asmet`.`ValorGlosaFavor` from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`) order by `glosaseps_asmet`.`FechaRegistro` desc limit 1),0)) AS `TotalGlosaFavor`,(select ifnull((select `glosaseps_asmet`.`ValorGlosaContra` from `glosaseps_asmet` where (`glosaseps_asmet`.`NumeroFactura` = `t2`.`NumeroFactura`) order by `glosaseps_asmet`.`FechaRegistro` desc limit 1),0)) AS `TotalGlosaContra`,(((select `TotalGlosaInicial`) - (select `TotalGlosaFavor`)) - (select `TotalGlosaContra`)) AS `GlosaXConciliar`,(select ifnull((select count(distinct `notas_db_cr_2`.`NumeroTransaccion`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`notas_db_cr_2`.`C13` <> 'N') and ((`notas_db_cr_2`.`TipoOperacion` = '2259') or (`notas_db_cr_2`.`TipoOperacion` = '2269') or (`notas_db_cr_2`.`TipoOperacion` = '2039')))),0)) AS `DevolucionesPresentadas`,(select ifnull((select count(distinct `notas_db_cr_2`.`NumeroTransaccion`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`notas_db_cr_2`.`C13` <> 'N') and (`notas_db_cr_2`.`TipoOperacion` like '20%'))),0)) AS `FacturasPresentadas`,(select if(((select `FacturasPresentadas`) > (((select `DevolucionesPresentadas`) + (select `NumeroFacturasDevueltasAnticipos`)) + (select `FacturasDevueltasCXPVSANT`))),'SI','NO')) AS `FacturaActiva`,(select if((`FacturaActiva` = 'SI'),0,(select ifnull((select `notas_db_cr_2`.`ValorTotal` from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t2`.`NumeroFactura`) and ((`notas_db_cr_2`.`TipoOperacion` = '2259') or (`notas_db_cr_2`.`TipoOperacion` = '2269') or (`notas_db_cr_2`.`TipoOperacion` = '2039')) and (`notas_db_cr_2`.`FechaTransaccion` >= `t2`.`FechaRadicado`)) limit 1),0)))) AS `TotalDevolucionesNotas`,(select if((`FacturaActiva` = 'SI'),0,(((select abs(`TotalDevolucionesNotas`)) + (select abs(`FacturasDevueltas`))) + (select if((`FacturasDevueltasCXPVSANT` = `DevolucionesPresentadas`),0,(select abs(`ValorFacturasDevueltascxpvsant`))))))) AS `TotalDevoluciones`,(select ifnull((select sum(`carteraxedades`.`ValorTotalcartera`) from `carteraxedades` where (`carteraxedades`.`NumeroFactura` = `t2`.`NumeroFactura`) limit 1),0)) AS `CarteraXEdades`,(select ifnull((select sum(`conciliaciones_cruces`.`ValorConciliacion`) from `conciliaciones_cruces` where ((`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`conciliaciones_cruces`.`ConciliacionAFavorDe` = 1))),0)) AS `ConciliacionesAFavorEPS`,(select ifnull((select sum(`conciliaciones_cruces`.`ValorConciliacion`) from `conciliaciones_cruces` where ((`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`) and (`conciliaciones_cruces`.`ConciliacionAFavorDe` = 2))),0)) AS `ConciliacionesAFavorIPS`,((((((((((`t2`.`ValorMenosImpuestos` - (select `TotalPagos`)) - (select `TotalAnticipos`)) - (select `TotalGlosaFavor`)) - (select `GlosaXConciliar`)) - (select `OtrosDescuentos`)) - (select abs(`TotalCopagos`))) - (select abs(`TotalDevoluciones`))) - (select abs(`DescuentoPGP`))) - (select abs(`ConciliacionesAFavorEPS`))) + (select abs(`ConciliacionesAFavorIPS`))) AS `ValorSegunEPS`,(select ifnull((select round(`carteracargadaips`.`ValorTotalpagar`,0) from `carteracargadaips` where (`carteracargadaips`.`NumeroFactura` = `t2`.`NumeroFactura`) limit 1),0)) AS `ValorSegunIPS`,((select `ValorSegunEPS`) - (select `ValorSegunIPS`)) AS `Diferencia`,(select if((select (`Diferencia` > 0)),'SI','NO')) AS `ValorIPSMenor`,(select ifnull((select sum(`conciliaciones_cruces`.`ValorConciliacion`) from `conciliaciones_cruces` where (`conciliaciones_cruces`.`NumeroFactura` = `t2`.`NumeroFactura`)),0)) AS `TotalConciliaciones`,(select `ValorSegunEPS`) AS `TotalAPagar`,(select if((select ((round(`TotalConciliaciones`,2) <> (select round(abs(`Diferencia`),2))) and (select (`TotalConciliaciones` > 0)))),'SI','NO')) AS `ConciliacionesPendientes`,(select if(((select abs(`TotalPagos`)) = (select abs(`Diferencia`))),1,0)) AS `DiferenciaXPagos` from `carteraeps` `t2`;

DROP TABLE IF EXISTS `vista_cruce_cartera_eps_no_relacionadas_ips`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_cartera_eps_no_relacionadas_ips` AS select `t1`.`ID` AS `ID`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Estado` AS `Estado`,`t1`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`NoRelacionada` AS `NoRelacionada`,`t1`.`FechaFactura` AS `FechaFactura`,`t1`.`MesServicio` AS `MesServicio`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`Pendientes` AS `Pendientes`,`t1`.`FechaConciliacion` AS `FechaConciliacion`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`ValorDocumento` AS `ValorDocumento`,`t1`.`Impuestos` AS `Impuestos`,`t1`.`ImpuestosSegunASMET` AS `ImpuestosSegunASMET`,`t1`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,`t1`.`TotalPagosNotas` AS `TotalPagosNotas`,`t1`.`Capitalizacion` AS `Capitalizacion`,`t1`.`TotalPagos` AS `TotalPagos`,`t1`.`TotalAnticipos` AS `TotalAnticipos`,`t1`.`DescuentoPGP` AS `DescuentoPGP`,`t1`.`FacturasDevueltas` AS `FacturasDevueltas`,`t1`.`NumeroFacturasDevueltasAnticipos` AS `NumeroFacturasDevueltasAnticipos`,`t1`.`ValorFacturasDevueltascxpvsant` AS `ValorFacturasDevueltascxpvsant`,`t1`.`FacturasDevueltasCXPVSANT` AS `FacturasDevueltasCXPVSANT`,`t1`.`TotalCopagos` AS `TotalCopagos`,`t1`.`OtrosDescuentos` AS `OtrosDescuentos`,`t1`.`AjustesCartera` AS `AjustesCartera`,`t1`.`TotalGlosaInicial` AS `TotalGlosaInicial`,`t1`.`TotalGlosaFavor` AS `TotalGlosaFavor`,`t1`.`TotalGlosaContra` AS `TotalGlosaContra`,`t1`.`GlosaXConciliar` AS `GlosaXConciliar`,`t1`.`DevolucionesPresentadas` AS `DevolucionesPresentadas`,`t1`.`FacturasPresentadas` AS `FacturasPresentadas`,`t1`.`FacturaActiva` AS `FacturaActiva`,`t1`.`TotalDevolucionesNotas` AS `TotalDevolucionesNotas`,`t1`.`TotalDevoluciones` AS `TotalDevoluciones`,`t1`.`CarteraXEdades` AS `CarteraXEdades`,`t1`.`ConciliacionesAFavorEPS` AS `ConciliacionesAFavorEPS`,`t1`.`ConciliacionesAFavorIPS` AS `ConciliacionesAFavorIPS`,`t1`.`ValorSegunEPS` AS `ValorSegunEPS`,`t1`.`ValorSegunIPS` AS `ValorSegunIPS`,`t1`.`Diferencia` AS `Diferencia`,`t1`.`ValorIPSMenor` AS `ValorIPSMenor`,`t1`.`TotalConciliaciones` AS `TotalConciliaciones`,`t1`.`TotalAPagar` AS `TotalAPagar`,`t1`.`ConciliacionesPendientes` AS `ConciliacionesPendientes`,`t1`.`DiferenciaXPagos` AS `DiferenciaXPagos` from `vista_cruce_cartera_eps` `t1` where ((not(exists(select 1 from `carteracargadaips` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`)))) and ((`t1`.`FacturaActiva` = 'NO') or (`t1`.`DevolucionesPresentadas` = 0)) and (`t1`.`ValorSegunEPS` < 0));

DROP TABLE IF EXISTS `vista_cruce_cartera_eps_no_relacionadas_ips_completa`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_cartera_eps_no_relacionadas_ips_completa` AS select `t1`.`ID` AS `ID`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Estado` AS `Estado`,`t1`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`NoRelacionada` AS `NoRelacionada`,`t1`.`FechaFactura` AS `FechaFactura`,`t1`.`MesServicio` AS `MesServicio`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`Pendientes` AS `Pendientes`,`t1`.`FechaConciliacion` AS `FechaConciliacion`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`ValorDocumento` AS `ValorDocumento`,`t1`.`Impuestos` AS `Impuestos`,`t1`.`ImpuestosSegunASMET` AS `ImpuestosSegunASMET`,`t1`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,`t1`.`TotalPagosNotas` AS `TotalPagosNotas`,`t1`.`Capitalizacion` AS `Capitalizacion`,`t1`.`TotalPagos` AS `TotalPagos`,`t1`.`TotalAnticipos` AS `TotalAnticipos`,`t1`.`DescuentoPGP` AS `DescuentoPGP`,`t1`.`FacturasDevueltas` AS `FacturasDevueltas`,`t1`.`NumeroFacturasDevueltasAnticipos` AS `NumeroFacturasDevueltasAnticipos`,`t1`.`ValorFacturasDevueltascxpvsant` AS `ValorFacturasDevueltascxpvsant`,`t1`.`FacturasDevueltasCXPVSANT` AS `FacturasDevueltasCXPVSANT`,`t1`.`TotalCopagos` AS `TotalCopagos`,`t1`.`OtrosDescuentos` AS `OtrosDescuentos`,`t1`.`AjustesCartera` AS `AjustesCartera`,`t1`.`TotalGlosaInicial` AS `TotalGlosaInicial`,`t1`.`TotalGlosaFavor` AS `TotalGlosaFavor`,`t1`.`TotalGlosaContra` AS `TotalGlosaContra`,`t1`.`GlosaXConciliar` AS `GlosaXConciliar`,`t1`.`DevolucionesPresentadas` AS `DevolucionesPresentadas`,`t1`.`FacturasPresentadas` AS `FacturasPresentadas`,`t1`.`FacturaActiva` AS `FacturaActiva`,`t1`.`TotalDevolucionesNotas` AS `TotalDevolucionesNotas`,`t1`.`TotalDevoluciones` AS `TotalDevoluciones`,`t1`.`CarteraXEdades` AS `CarteraXEdades`,`t1`.`ConciliacionesAFavorEPS` AS `ConciliacionesAFavorEPS`,`t1`.`ConciliacionesAFavorIPS` AS `ConciliacionesAFavorIPS`,`t1`.`ValorSegunEPS` AS `ValorSegunEPS`,`t1`.`ValorSegunIPS` AS `ValorSegunIPS`,`t1`.`Diferencia` AS `Diferencia`,`t1`.`ValorIPSMenor` AS `ValorIPSMenor`,`t1`.`TotalConciliaciones` AS `TotalConciliaciones`,`t1`.`TotalAPagar` AS `TotalAPagar`,`t1`.`ConciliacionesPendientes` AS `ConciliacionesPendientes`,`t1`.`DiferenciaXPagos` AS `DiferenciaXPagos` from `vista_cruce_cartera_eps` `t1` where (not(exists(select 1 from `carteracargadaips` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_cruce_cartera_eps_relacionadas_ips`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_cartera_eps_relacionadas_ips` AS select `t1`.`ID` AS `ID`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Estado` AS `Estado`,`t1`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`NoRelacionada` AS `NoRelacionada`,`t1`.`FechaFactura` AS `FechaFactura`,`t1`.`MesServicio` AS `MesServicio`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`Pendientes` AS `Pendientes`,`t1`.`FechaConciliacion` AS `FechaConciliacion`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`ValorDocumento` AS `ValorDocumento`,`t1`.`Impuestos` AS `Impuestos`,`t1`.`ImpuestosSegunASMET` AS `ImpuestosSegunASMET`,`t1`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,`t1`.`TotalPagosNotas` AS `TotalPagosNotas`,`t1`.`Capitalizacion` AS `Capitalizacion`,`t1`.`TotalPagos` AS `TotalPagos`,`t1`.`TotalAnticipos` AS `TotalAnticipos`,`t1`.`DescuentoPGP` AS `DescuentoPGP`,`t1`.`FacturasDevueltas` AS `FacturasDevueltas`,`t1`.`NumeroFacturasDevueltasAnticipos` AS `NumeroFacturasDevueltasAnticipos`,`t1`.`ValorFacturasDevueltascxpvsant` AS `ValorFacturasDevueltascxpvsant`,`t1`.`FacturasDevueltasCXPVSANT` AS `FacturasDevueltasCXPVSANT`,`t1`.`TotalCopagos` AS `TotalCopagos`,`t1`.`OtrosDescuentos` AS `OtrosDescuentos`,`t1`.`AjustesCartera` AS `AjustesCartera`,`t1`.`TotalGlosaInicial` AS `TotalGlosaInicial`,`t1`.`TotalGlosaFavor` AS `TotalGlosaFavor`,`t1`.`TotalGlosaContra` AS `TotalGlosaContra`,`t1`.`GlosaXConciliar` AS `GlosaXConciliar`,`t1`.`DevolucionesPresentadas` AS `DevolucionesPresentadas`,`t1`.`FacturasPresentadas` AS `FacturasPresentadas`,`t1`.`FacturaActiva` AS `FacturaActiva`,`t1`.`TotalDevolucionesNotas` AS `TotalDevolucionesNotas`,`t1`.`TotalDevoluciones` AS `TotalDevoluciones`,`t1`.`CarteraXEdades` AS `CarteraXEdades`,`t1`.`ConciliacionesAFavorEPS` AS `ConciliacionesAFavorEPS`,`t1`.`ConciliacionesAFavorIPS` AS `ConciliacionesAFavorIPS`,`t1`.`ValorSegunEPS` AS `ValorSegunEPS`,`t1`.`ValorSegunIPS` AS `ValorSegunIPS`,`t1`.`Diferencia` AS `Diferencia`,`t1`.`ValorIPSMenor` AS `ValorIPSMenor`,`t1`.`TotalConciliaciones` AS `TotalConciliaciones`,`t1`.`TotalAPagar` AS `TotalAPagar`,`t1`.`ConciliacionesPendientes` AS `ConciliacionesPendientes`,`t1`.`DiferenciaXPagos` AS `DiferenciaXPagos` from `vista_cruce_cartera_eps` `t1` where exists(select 1 from `carteracargadaips` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`));

DROP TABLE IF EXISTS `vista_cruce_cartera_eps_sin_relacion_segun_ags`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_cartera_eps_sin_relacion_segun_ags` AS select `t2`.`FechaFactura` AS `FechaFactura`,`t2`.`MesServicio` AS `MesServicio`,`t2`.`NumeroRadicado` AS `NumeroRadicado`,`t2`.`FechaRadicado` AS `FechaRadicado`,`t2`.`NumeroContrato` AS `NumeroContrato`,`t2`.`NumeroFactura` AS `NumeroFactura`,`t2`.`ValorDocumento` AS `ValorDocumento`,`t2`.`Impuestos` AS `Impuestos`,`t2`.`TotalPagos` AS `TotalPagos`,`t2`.`TotalAnticipos` AS `TotalAnticipos`,`t2`.`TotalCopagos` AS `TotalCopagos`,`t2`.`DescuentoPGP` AS `DescuentoPGP`,`t2`.`OtrosDescuentos` AS `OtrosDescuentos`,`t2`.`AjustesCartera` AS `AjustesCartera`,`t2`.`Diferencia` AS `TotalGlosaFavor`,`t2`.`TotalGlosaContra` AS `TotalGlosaContra`,`t2`.`GlosaXConciliar` AS `GlosaXConciliar`,`t2`.`TotalDevoluciones` AS `TotalDevoluciones`,'0' AS `ValorSegunEPS`,'0' AS `ValorSegunIPS`,'0' AS `Diferencia` from `vista_cruce_cartera_eps` `t2` where (not(exists(select 1 from `carteracargadaips` `t1` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_cruce_totales_actas_conciliaciones`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_cruce_totales_actas_conciliaciones` AS select `t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Diferencia` AS `Diferencia`,`t1`.`MesServicio` AS `MesServicio`,(select if((abs(`t1`.`TotalPagos`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXPagos`,(select if((abs(`t1`.`TotalAnticipos`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXAnticipos`,(select if((abs(`t1`.`TotalCopagos`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXCopagos`,(select if((abs(`t1`.`DescuentoPGP`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXDescuentoPGP`,(select if((abs(`t1`.`OtrosDescuentos`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXOtrosDescuentos`,(select if((abs(`t1`.`AjustesCartera`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXAjustesCartera`,(select if((abs(`t1`.`TotalGlosaFavor`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXGlosaFavorEPS`,(select if((abs(`t1`.`TotalGlosaContra`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXGlosaContraEPS`,(select if((abs(`t1`.`GlosaXConciliar`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXGlosaXConciliar`,(select if((abs(`t1`.`TotalDevoluciones`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXDevoluciones`,(select if((abs(`t1`.`Impuestos`) = abs(`t1`.`Diferencia`)),`t1`.`Diferencia`,0)) AS `DiferenciaXImpuestos`,(select if((`t1`.`ValorSegunIPS` > `t1`.`ValorDocumento`),`t1`.`Diferencia`,0)) AS `DiferenciaXValorFacturado`,((((((((((((select `t1`.`DiferenciaXPagos`) + (select `DiferenciaXAnticipos`)) + (select `DiferenciaXCopagos`)) + (select `DiferenciaXDescuentoPGP`)) + (select `DiferenciaXOtrosDescuentos`)) + (select `DiferenciaXAjustesCartera`)) + (select `DiferenciaXGlosaFavorEPS`)) + (select `DiferenciaXGlosaContraEPS`)) + (select `DiferenciaXGlosaXConciliar`)) + (select `DiferenciaXDevoluciones`)) + (select `DiferenciaXImpuestos`)) + (select `DiferenciaXValorFacturado`)) AS `TotalDiferenciasComunes`,(select if((((select `TotalDiferenciasComunes`) = 0) and (`t1`.`ValorSegunEPS` = 0) and (abs(`t1`.`TotalDevoluciones`) > 0)),`t1`.`Diferencia`,0)) AS `DiferenciaXDevolucionesNoIPS`,(select if((((select `TotalDiferenciasComunes`) = 0) and ((select `DiferenciaXDevolucionesNoIPS`) = 0) and (abs(`t1`.`TotalPagos`) > 0) and (abs(`t1`.`GlosaXConciliar`) > 0)),`t1`.`GlosaXConciliar`,0)) AS `GlosasXConciliar2`,(select if((((select `TotalDiferenciasComunes`) = 0) and ((select `DiferenciaXDevolucionesNoIPS`) = 0) and (abs(`t1`.`TotalPagos`) > 0) and (abs(`t1`.`GlosaXConciliar`) > 0)),`t1`.`TotalPagos`,0)) AS `XPagos2`,(select if((((select `TotalDiferenciasComunes`) = 0) and (abs(`DiferenciaXDevolucionesNoIPS`) = 0) and (abs(`GlosasXConciliar2`) = 0)),`t1`.`Diferencia`,0)) AS `DiferenciaVariada` from `ts_eps_ips_890300513`.`vista_cruce_cartera_asmet` `t1` where ((`t1`.`Diferencia` <> 0) and exists(select 1 from `ts_eps`.`actas_conciliaciones_contratos` `t2` where (`t2`.`NumeroContrato` = `t1`.`NumeroContrato`)));

DROP TABLE IF EXISTS `vista_facturasdvueltas_anticiposvscxp`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturasdvueltas_anticiposvscxp` AS select (select `notas_db_cr_2`.`NumeroTransaccion`) AS `Numero_concidencia`,(select `anticipos2`.`NumeroFactura`) AS `Numerofactura_anticipos`,(select `notas_db_cr_2`.`NumeroFactura`) AS `Numerofactura_cxp`,(select `notas_db_cr_2`.`TipoOperacion`) AS `TipoOperacioncxp`,(select `anticipos2`.`NumeroInterno`) AS `TipoOperacionanticipos`,(select `anticipos2`.`ValorAnticipado`) AS `ValorDevuelto` from (`anticipos2` join `notas_db_cr_2` on(((`notas_db_cr_2`.`NumeroTransaccion` = `anticipos2`.`NumeroOperacion`) and (`notas_db_cr_2`.`TipoOperacion` = `anticipos2`.`NumeroInterno`))));

DROP TABLE IF EXISTS `vista_facturas_originales`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_originales` AS select `historial_carteracargada_eps`.`NumeroFactura` AS `NumeroFactura`,`historial_carteracargada_eps`.`ValorOriginal` AS `ValorOriginal`,`historial_carteracargada_eps`.`ValorMenosImpuestos` AS `ValorMenosImpuestos` from `historial_carteracargada_eps` group by `historial_carteracargada_eps`.`NumeroFactura`,`historial_carteracargada_eps`.`FechaFactura` order by `historial_carteracargada_eps`.`FechaFactura`;

DROP TABLE IF EXISTS `vista_facturas_pagadas_no_relacionadas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_pagadas_no_relacionadas` AS select `t1`.`ID` AS `ID`,`t1`.`Nit_IPS` AS `Nit_IPS`,`t1`.`Nit_EPS` AS `Nit_EPS`,`t1`.`Proceso` AS `Proceso`,`t1`.`DescripcionProceso` AS `DescripcionProceso`,`t1`.`Estado` AS `Estado`,`t1`.`Cuenta` AS `Cuenta`,`t1`.`Banco` AS `Banco`,`t1`.`FechaPagoFactura` AS `FechaPagoFactura`,`t1`.`NumeroPago` AS `NumeroPago`,`t1`.`TipoOperacion` AS `TipoOperacion`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`ValorBrutoPagar` AS `ValorBrutoPagar`,`t1`.`ValorDescuento` AS `ValorDescuento`,`t1`.`ValorIva` AS `ValorIva`,`t1`.`ValorRetefuente` AS `ValorRetefuente`,`t1`.`ValorReteiva` AS `ValorReteiva`,`t1`.`ValorReteica` AS `ValorReteica`,`t1`.`ValorOtrasRetenciones` AS `ValorOtrasRetenciones`,`t1`.`ValorCruces` AS `ValorCruces`,`t1`.`ValorAnticipos` AS `ValorAnticipos`,`t1`.`ValorTotal` AS `ValorTotal`,`t1`.`ValorTranferido` AS `ValorTranferido`,`t1`.`Regional` AS `Regional`,`t1`.`llaveCompuesta` AS `llaveCompuesta`,`t1`.`idUser` AS `idUser`,`t1`.`Soporte` AS `Soporte`,`t1`.`FechaRegistro` AS `FechaRegistro`,`t1`.`FechaActualizacion` AS `FechaActualizacion`,`t1`.`Sync` AS `Sync` from `pagos_asmet` `t1` where (not(exists(select 1 from `carteraeps` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_facturas_sr_eps`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_sr_eps` AS select `t1`.`ID` AS `ID`,`t1`.`NitEPS` AS `NitEPS`,`t1`.`CodigoSucursal` AS `CodigoSucursal`,`t1`.`Sucursal` AS `Sucursal`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Descripcion` AS `Descripcion`,`t1`.`RazonSocial` AS `RazonSocial`,`t1`.`Nit_IPS` AS `Nit_IPS`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`Prefijo` AS `Prefijo`,`t1`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`ValorOriginal` AS `ValorOriginal`,`t1`.`idUser` AS `idUser`,`t1`.`FechaRegistro` AS `FechaRegistro`,`t1`.`FechaActualizacion` AS `FechaActualizacion`,`t1`.`Sync` AS `Sync` from `carteraeps` `t1` where (not(exists(select 1 from `carteracargadaips` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_facturas_sr_eps_2`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_sr_eps_2` AS select `t1`.`ID` AS `ID`,`t1`.`NitEPS` AS `NitEPS`,`t1`.`CodigoSucursal` AS `CodigoSucursal`,`t1`.`Sucursal` AS `Sucursal`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Descripcion` AS `Descripcion`,`t1`.`RazonSocial` AS `RazonSocial`,`t1`.`Nit_IPS` AS `Nit_IPS`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`Prefijo` AS `Prefijo`,`t1`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`ValorOriginal` AS `ValorOriginal`,`t1`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,`t1`.`idUser` AS `idUser`,`t1`.`MesServicio` AS `MesServicio`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`FechaRegistro` AS `FechaRegistro`,`t1`.`FechaActualizacion` AS `FechaActualizacion`,`t1`.`ConciliadoXIPS` AS `ConciliadoXIPS`,`t1`.`ConciliadoXEPS` AS `ConciliadoXEPS`,`t1`.`Estado` AS `Estado`,`t1`.`Sync` AS `Sync`,(select sum(`notas_db_cr_2`.`ValorTotal`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t1`.`NumeroFactura`) and ((`notas_db_cr_2`.`TipoOperacion` = '2259') or (`notas_db_cr_2`.`TipoOperacion` = '2269') or (`notas_db_cr_2`.`TipoOperacion` = '2039')))) AS `TotalDevoluciones`,(select sum((`retenciones`.`ValorCredito` - `retenciones`.`ValorDebito`)) from `retenciones` where (`retenciones`.`NumeroFactura` = `t1`.`NumeroFactura`)) AS `TotalRetenciones`,(select ifnull((select sum(`notas_db_cr_2`.`ValorPago`) from `notas_db_cr_2` where ((`notas_db_cr_2`.`NumeroFactura` = `t1`.`NumeroFactura`) and ((`notas_db_cr_2`.`TipoOperacion2` = '3090') or (`notas_db_cr_2`.`TipoOperacion2` = '3071') or (`notas_db_cr_2`.`TipoOperacion2` = '3072') or (`notas_db_cr_2`.`TipoOperacion2` = '3086') or (`notas_db_cr_2`.`TipoOperacion2` = '3089')))),0)) AS `TotalPagos`,((`t1`.`ValorOriginal` - (select `TotalPagos`)) - (select `TotalRetenciones`)) AS `Saldo`,(`t1`.`ValorOriginal` - `t1`.`ValorMenosImpuestos`) AS `ValorImpuestosCalculados` from `carteraeps` `t1` where (not(exists(select 1 from `carteracargadaips` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_facturas_sr_eps_3`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_sr_eps_3` AS select `vista_facturas_sr_eps_2`.`ID` AS `ID`,`vista_facturas_sr_eps_2`.`NitEPS` AS `NitEPS`,`vista_facturas_sr_eps_2`.`CodigoSucursal` AS `CodigoSucursal`,`vista_facturas_sr_eps_2`.`Sucursal` AS `Sucursal`,`vista_facturas_sr_eps_2`.`NumeroFactura` AS `NumeroFactura`,`vista_facturas_sr_eps_2`.`Descripcion` AS `Descripcion`,`vista_facturas_sr_eps_2`.`RazonSocial` AS `RazonSocial`,`vista_facturas_sr_eps_2`.`Nit_IPS` AS `Nit_IPS`,`vista_facturas_sr_eps_2`.`NumeroContrato` AS `NumeroContrato`,`vista_facturas_sr_eps_2`.`Prefijo` AS `Prefijo`,`vista_facturas_sr_eps_2`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`vista_facturas_sr_eps_2`.`ValorOriginal` AS `ValorOriginal`,`vista_facturas_sr_eps_2`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,`vista_facturas_sr_eps_2`.`idUser` AS `idUser`,`vista_facturas_sr_eps_2`.`MesServicio` AS `MesServicio`,`vista_facturas_sr_eps_2`.`FechaRadicado` AS `FechaRadicado`,`vista_facturas_sr_eps_2`.`NumeroRadicado` AS `NumeroRadicado`,`vista_facturas_sr_eps_2`.`FechaRegistro` AS `FechaRegistro`,`vista_facturas_sr_eps_2`.`FechaActualizacion` AS `FechaActualizacion`,`vista_facturas_sr_eps_2`.`ConciliadoXIPS` AS `ConciliadoXIPS`,`vista_facturas_sr_eps_2`.`ConciliadoXEPS` AS `ConciliadoXEPS`,`vista_facturas_sr_eps_2`.`Estado` AS `Estado`,`vista_facturas_sr_eps_2`.`Sync` AS `Sync`,`vista_facturas_sr_eps_2`.`TotalDevoluciones` AS `TotalDevoluciones`,`vista_facturas_sr_eps_2`.`TotalRetenciones` AS `TotalRetenciones`,`vista_facturas_sr_eps_2`.`TotalPagos` AS `TotalPagos`,`vista_facturas_sr_eps_2`.`Saldo` AS `Saldo`,`vista_facturas_sr_eps_2`.`ValorImpuestosCalculados` AS `ValorImpuestosCalculados` from `vista_facturas_sr_eps_2` where ((`vista_facturas_sr_eps_2`.`Saldo` < 0) and (`vista_facturas_sr_eps_2`.`TotalDevoluciones` < 0));

DROP TABLE IF EXISTS `vista_facturas_sr_ips`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_facturas_sr_ips` AS select `t1`.`ID` AS `ID`,`t1`.`NitEPS` AS `NitEPS`,`t1`.`NitIPS` AS `NitIPS`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`FechaFactura` AS `FechaFactura`,`t1`.`NumeroCuentaGlobal` AS `NumeroCuentaGlobal`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`TipoNegociacion` AS `TipoNegociacion`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`DiasPactados` AS `DiasPactados`,`t1`.`TipoRegimen` AS `TipoRegimen`,`t1`.`ValorDocumento` AS `ValorDocumento`,`t1`.`ValorGlosaInicial` AS `ValorGlosaInicial`,`t1`.`ValorGlosaAceptada` AS `ValorGlosaAceptada`,`t1`.`ValorGlosaConciliada` AS `ValorGlosaConciliada`,`t1`.`ValorDescuentoBdua` AS `ValorDescuentoBdua`,`t1`.`ValorAnticipos` AS `ValorAnticipos`,`t1`.`ValorRetencion` AS `ValorRetencion`,`t1`.`ValorTotalpagar` AS `ValorTotalpagar`,`t1`.`FechaHasta` AS `FechaHasta`,`t1`.`Soporte` AS `Soporte`,`t1`.`idUser` AS `idUser`,`t1`.`FechaRegistro` AS `FechaRegistro`,`t1`.`FechaActualizacion` AS `FechaActualizacion`,`t1`.`FlagUpdate` AS `FlagUpdate`,`t1`.`Sync` AS `Sync` from `carteracargadaips` `t1` where (not(exists(select 1 from `carteraeps` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`))));

DROP TABLE IF EXISTS `vista_hoja_trabajo_cruce`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_hoja_trabajo_cruce` AS select `t1`.`ID` AS `ID`,`t1`.`NumeroFactura` AS `NumeroFactura`,`t1`.`Estado` AS `Estado`,`t1`.`DepartamentoRadicacion` AS `DepartamentoRadicacion`,`t1`.`NoRelacionada` AS `NoRelacionada`,`t1`.`FechaFactura` AS `FechaFactura`,`t1`.`MesServicio` AS `MesServicio`,`t1`.`NumeroRadicado` AS `NumeroRadicado`,`t1`.`Pendientes` AS `Pendientes`,`t1`.`FechaConciliacion` AS `FechaConciliacion`,`t1`.`FechaRadicado` AS `FechaRadicado`,`t1`.`NumeroContrato` AS `NumeroContrato`,`t1`.`ValorDocumento` AS `ValorDocumento`,`t1`.`Impuestos` AS `Impuestos`,`t1`.`ImpuestosSegunASMET` AS `ImpuestosSegunASMET`,`t1`.`ValorMenosImpuestos` AS `ValorMenosImpuestos`,`t1`.`TotalPagosNotas` AS `TotalPagosNotas`,`t1`.`Capitalizacion` AS `Capitalizacion`,`t1`.`TotalPagos` AS `TotalPagos`,`t1`.`TotalAnticipos` AS `TotalAnticipos`,`t1`.`DescuentoPGP` AS `DescuentoPGP`,`t1`.`FacturasDevueltas` AS `FacturasDevueltas`,`t1`.`NumeroFacturasDevueltasAnticipos` AS `NumeroFacturasDevueltasAnticipos`,`t1`.`ValorFacturasDevueltascxpvsant` AS `ValorFacturasDevueltascxpvsant`,`t1`.`FacturasDevueltasCXPVSANT` AS `FacturasDevueltasCXPVSANT`,`t1`.`TotalCopagos` AS `TotalCopagos`,`t1`.`OtrosDescuentos` AS `OtrosDescuentos`,`t1`.`AjustesCartera` AS `AjustesCartera`,`t1`.`TotalGlosaInicial` AS `TotalGlosaInicial`,`t1`.`TotalGlosaFavor` AS `TotalGlosaFavor`,`t1`.`TotalGlosaContra` AS `TotalGlosaContra`,`t1`.`GlosaXConciliar` AS `GlosaXConciliar`,`t1`.`DevolucionesPresentadas` AS `DevolucionesPresentadas`,`t1`.`FacturasPresentadas` AS `FacturasPresentadas`,`t1`.`FacturaActiva` AS `FacturaActiva`,`t1`.`TotalDevolucionesNotas` AS `TotalDevolucionesNotas`,`t1`.`TotalDevoluciones` AS `TotalDevoluciones`,`t1`.`CarteraXEdades` AS `CarteraXEdades`,`t1`.`ConciliacionesAFavorEPS` AS `ConciliacionesAFavorEPS`,`t1`.`ConciliacionesAFavorIPS` AS `ConciliacionesAFavorIPS`,`t1`.`ValorSegunEPS` AS `ValorSegunEPS`,`t1`.`ValorSegunIPS` AS `ValorSegunIPS`,`t1`.`Diferencia` AS `Diferencia`,`t1`.`ValorIPSMenor` AS `ValorIPSMenor`,`t1`.`TotalConciliaciones` AS `TotalConciliaciones`,`t1`.`TotalAPagar` AS `TotalAPagar`,`t1`.`ConciliacionesPendientes` AS `ConciliacionesPendientes`,`t1`.`DiferenciaXPagos` AS `DiferenciaXPagos` from `vista_cruce_cartera_eps` `t1` where (exists(select 1 from `carteracargadaips` `t2` where (`t1`.`NumeroFactura` = `t2`.`NumeroFactura`)) or ((not(exists(select 1 from `carteracargadaips` `t3` where (`t1`.`NumeroFactura` = `t3`.`NumeroFactura`)))) and (`t1`.`ValorSegunEPS` < 0)));

DROP TABLE IF EXISTS `vista_pendientes`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_pendientes` AS select 'Radicados' AS `Radicados`,`radicadospendientes`.`NumeroRadicado` AS `NumeroRadicado`,sum(`radicadospendientes`.`Valor`) AS `Total` from `radicadospendientes` where ((`radicadospendientes`.`EstadoAuditoria` like '%AUDITORIA%') and exists(select 1 from `vista_cruce_cartera_asmet` where (`vista_cruce_cartera_asmet`.`NumeroRadicado` = `radicadospendientes`.`NumeroRadicado`))) group by `radicadospendientes`.`NumeroRadicado` union select 'Devoluciones' AS `Devoluciones`,`devoluciones_pendientes`.`NumeroRadicado` AS `NumeroRadicado`,sum(`devoluciones_pendientes`.`Valor`) AS `Total` from `devoluciones_pendientes` where ((`devoluciones_pendientes`.`NoEnviados` > '0') and exists(select 1 from `vista_cruce_cartera_asmet` where (`vista_cruce_cartera_asmet`.`NumeroRadicado` = `devoluciones_pendientes`.`NumeroRadicado`))) group by `devoluciones_pendientes`.`NumeroRadicado` union select 'Notas' AS `Notas`,`notas_pendientes`.`NumeroRadicado` AS `NumeroRadicado`,sum(`notas_pendientes`.`Valor`) AS `Total` from `notas_pendientes` where ((`notas_pendientes`.`NoEnviados` > '0') and exists(select 1 from `vista_cruce_cartera_asmet` where (`vista_cruce_cartera_asmet`.`NumeroRadicado` = `notas_pendientes`.`NumeroRadicado`))) group by `notas_pendientes`.`NumeroRadicado` union select 'Copagos' AS `Copagos`,`copagos_pendientes`.`NumeroRadicado` AS `NumeroRadicado`,sum(`copagos_pendientes`.`Valor`) AS `Total` from `copagos_pendientes` where ((`copagos_pendientes`.`NoEnviados` > '0') and exists(select 1 from `vista_cruce_cartera_asmet` where (`vista_cruce_cartera_asmet`.`NumeroRadicado` = `copagos_pendientes`.`NumeroRadicado`))) group by `copagos_pendientes`.`NumeroRadicado`;

DROP TABLE IF EXISTS `vista_reporte_ips`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_reporte_ips` AS select `t2`.`FechaFactura` AS `FechaFactura`,`t2`.`MesServicio` AS `MesServicio`,`t2`.`NumeroRadicado` AS `NumeroRadicado`,`t2`.`FechaRadicado` AS `FechaRadicado`,`t2`.`NumeroContrato` AS `NumeroContrato`,`t2`.`NumeroFactura` AS `NumeroFactura`,`t2`.`ValorDocumento` AS `ValorDocumento`,`t2`.`Impuestos` AS `Impuestos`,`t2`.`TotalPagos` AS `TotalPagos`,`t2`.`TotalAnticipos` AS `TotalAnticipos`,`t2`.`TotalCopagos` AS `TotalCopagos`,`t2`.`DescuentoPGP` AS `DescuentoPGP`,`t2`.`OtrosDescuentos` AS `OtrosDescuentos`,`t2`.`AjustesCartera` AS `AjustesCartera`,`t2`.`TotalGlosaFavor` AS `TotalGlosaFavor`,`t2`.`TotalGlosaContra` AS `TotalGlosaContra`,`t2`.`GlosaXConciliar` AS `GlosaXConciliar`,`t2`.`TotalDevoluciones` AS `TotalDevoluciones`,`t2`.`ValorSegunEPS` AS `ValorSegunEPS`,`t2`.`ValorSegunIPS` AS `ValorSegunIPS`,`t2`.`Diferencia` AS `Diferencia` from `vista_cruce_cartera_eps_relacionadas_ips` `t2`;

DROP TABLE IF EXISTS `vista_reporte_ips_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_reporte_ips_completo` AS select `vista_reporte_ips`.`FechaFactura` AS `FechaFactura`,`vista_reporte_ips`.`MesServicio` AS `MesServicio`,`vista_reporte_ips`.`NumeroRadicado` AS `NumeroRadicado`,`vista_reporte_ips`.`FechaRadicado` AS `FechaRadicado`,`vista_reporte_ips`.`NumeroContrato` AS `NumeroContrato`,`vista_reporte_ips`.`NumeroFactura` AS `NumeroFactura`,`vista_reporte_ips`.`ValorDocumento` AS `ValorDocumento`,`vista_reporte_ips`.`Impuestos` AS `Impuestos`,`vista_reporte_ips`.`TotalPagos` AS `TotalPagos`,`vista_reporte_ips`.`TotalAnticipos` AS `TotalAnticipos`,`vista_reporte_ips`.`TotalCopagos` AS `TotalCopagos`,`vista_reporte_ips`.`DescuentoPGP` AS `DescuentoPGP`,`vista_reporte_ips`.`OtrosDescuentos` AS `OtrosDescuentos`,`vista_reporte_ips`.`AjustesCartera` AS `AjustesCartera`,`vista_reporte_ips`.`TotalGlosaFavor` AS `TotalGlosaFavor`,`vista_reporte_ips`.`TotalGlosaContra` AS `TotalGlosaContra`,`vista_reporte_ips`.`GlosaXConciliar` AS `GlosaXConciliar`,`vista_reporte_ips`.`TotalDevoluciones` AS `TotalDevoluciones`,`vista_reporte_ips`.`ValorSegunEPS` AS `ValorSegunEPS`,`vista_reporte_ips`.`ValorSegunIPS` AS `ValorSegunIPS`,`vista_reporte_ips`.`Diferencia` AS `Diferencia` from `vista_reporte_ips` union all select `vista_cruce_cartera_eps_sin_relacion_segun_ags`.`FechaFactura` AS `FechaFactura`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`MesServicio` AS `MesServicio`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`NumeroRadicado` AS `NumeroRadicado`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`FechaRadicado` AS `FechaRadicado`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`NumeroContrato` AS `NumeroContrato`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`NumeroFactura` AS `NumeroFactura`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`ValorDocumento` AS `ValorDocumento`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`Impuestos` AS `Impuestos`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`TotalPagos` AS `TotalPagos`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`TotalAnticipos` AS `TotalAnticipos`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`TotalCopagos` AS `TotalCopagos`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`DescuentoPGP` AS `DescuentoPGP`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`OtrosDescuentos` AS `OtrosDescuentos`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`AjustesCartera` AS `AjustesCartera`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`TotalGlosaFavor` AS `TotalGlosaFavor`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`TotalGlosaContra` AS `TotalGlosaContra`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`GlosaXConciliar` AS `GlosaXConciliar`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`TotalDevoluciones` AS `TotalDevoluciones`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`ValorSegunEPS` AS `ValorSegunEPS`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`ValorSegunIPS` AS `ValorSegunIPS`,`vista_cruce_cartera_eps_sin_relacion_segun_ags`.`Diferencia` AS `Diferencia` from `vista_cruce_cartera_eps_sin_relacion_segun_ags`;

DROP TABLE IF EXISTS `vista_resumen_cruce_cartera_asmet`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_resumen_cruce_cartera_asmet` AS select `vista_cruce_cartera_asmet`.`NumeroContrato` AS `NumeroContrato`,sum(`vista_cruce_cartera_asmet`.`ValorDocumento`) AS `TotalFacturas`,sum(`vista_cruce_cartera_asmet`.`Impuestos`) AS `Impuestos`,sum(`vista_cruce_cartera_asmet`.`ValorMenosImpuestos`) AS `TotalMenosImpuestos`,sum(`vista_cruce_cartera_asmet`.`OtrosDescuentos`) AS `TotalOtrosDescuentos`,sum(`vista_cruce_cartera_asmet`.`TotalPagos`) AS `TotalPagos`,sum(`vista_cruce_cartera_asmet`.`TotalAnticipos`) AS `TotalAnticipos`,sum(`vista_cruce_cartera_asmet`.`TotalGlosaInicial`) AS `TotalGlosaInicial`,sum(`vista_cruce_cartera_asmet`.`TotalGlosaFavor`) AS `TotalGlosaFavor`,sum(`vista_cruce_cartera_asmet`.`TotalGlosaContra`) AS `TotalGlosaContra`,sum(`vista_cruce_cartera_asmet`.`GlosaXConciliar`) AS `TotalGlosaXConciliar`,sum(`vista_cruce_cartera_asmet`.`TotalCopagos`) AS `TotalCopagos`,sum(`vista_cruce_cartera_asmet`.`TotalDevoluciones`) AS `TotalDevoluciones`,sum(`vista_cruce_cartera_asmet`.`ValorSegunEPS`) AS `ValorSegunEPS` from `vista_cruce_cartera_asmet` group by `vista_cruce_cartera_asmet`.`NumeroContrato`;

DROP TABLE IF EXISTS `vista_retenciones_facturas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_retenciones_facturas` AS select `retenciones`.`NumeroFactura` AS `NumeroFactura`,sum(`retenciones`.`ValorDebito`) AS `Debitos`,sum(`retenciones`.`ValorCredito`) AS `Creditos` from `retenciones` group by `retenciones`.`NumeroFactura`;

DROP TABLE IF EXISTS `vista_ultimas_facturas_cartera_eps`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_ultimas_facturas_cartera_eps` AS select distinct `historial_carteracargada_eps`.`NumeroFactura` AS `NumeroFactura`,`historial_carteracargada_eps`.`FechaFactura` AS `FechaFactura`,`historial_carteracargada_eps`.`NumeroRadicado` AS `NumeroRadicado`,`historial_carteracargada_eps`.`MesServicio` AS `MesServicio`,`historial_carteracargada_eps`.`ValorOriginal` AS `ValorOriginal` from `historial_carteracargada_eps` where (`historial_carteracargada_eps`.`TipoOperacion` like '20%') order by `historial_carteracargada_eps`.`FechaFactura` desc;

-- 2019-09-10 12:30:27
