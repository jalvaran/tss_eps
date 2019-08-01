DROP TABLE IF EXISTS `temp_conciliaciones_cruces`;
CREATE TABLE `temp_conciliaciones_cruces` (
  `ID` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
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