CREATE TABLE `hoja_de_trabajo` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃƒÂºmero de la factura del prestador',
  `Estado` int(11) NOT NULL,
  `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
  `NoRelacionada` int(1) NOT NULL,
  `FechaFactura` date DEFAULT NULL,
  `MesServicio` int(6) NOT NULL,
  `NumeroRadicado` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `Pendientes` varchar(2) CHARACTER SET latin1 NOT NULL,
  `FechaConciliacion` datetime DEFAULT NULL,
  `FechaRadicado` date NOT NULL,
  `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
  `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
  `Impuestos` double(19,2) NOT NULL,
  `ImpuestosSegunASMET` double(19,2) NOT NULL,
  `ValorMenosImpuestos` double(15,2) NOT NULL,
  `TotalPagosNotas` double(19,2) NOT NULL,
  `Capitalizacion` double(19,2) NOT NULL,
  `TotalPagos` double(19,2) NOT NULL,
  `TotalAnticipos` double(19,2) NOT NULL,
  `DescuentoPGP` double(19,2) NOT NULL,
  `FacturasDevueltas` double(19,2) NOT NULL,
  `NumeroFacturasDevueltasAnticipos` bigint(21) NOT NULL,
  `ValorFacturasDevueltascxpvsant` double(19,2) NOT NULL,
  `FacturasDevueltasCXPVSANT` bigint(21) NOT NULL,
  `TotalCopagos` double(19,2) NOT NULL,
  `OtrosDescuentos` double(19,2) NOT NULL,
  `AjustesCartera` double(19,2) NOT NULL,
  `TotalGlosaInicial` double(15,2) NOT NULL,
  `TotalGlosaFavor` double(15,2) NOT NULL,
  `TotalGlosaContra` double(15,2) NOT NULL,
  `GlosaXConciliar` double(19,2) NOT NULL,
  `DevolucionesPresentadas` bigint(21) NOT NULL,
  `FacturasPresentadas` bigint(21) NOT NULL,
  `FacturaActiva` varchar(2) CHARACTER SET latin1 NOT NULL,
  `TotalDevolucionesNotas` double(15,2) NOT NULL,
  `TotalDevoluciones` double(19,2) NOT NULL,
  `CarteraXEdades` double(19,2) NOT NULL,
  `ConciliacionesAFavorEPS` double(19,2) NOT NULL,
  `ConciliacionesAFavorIPS` double(19,2) NOT NULL,
  `ValorSegunEPS` double(19,2) NOT NULL,
  `ValorSegunIPS` double(17,0) NOT NULL,
  `Diferencia` double(19,2) NOT NULL,
  `ValorIPSMenor` varchar(2) CHARACTER SET latin1 NOT NULL,
  `TotalConciliaciones` double(19,2) NOT NULL,
  `TotalAPagar` double(19,2) NOT NULL,
  `ConciliacionesPendientes` varchar(2) CHARACTER SET latin1 NOT NULL,
  `DiferenciaXPagos` int(1) NOT NULL,
  `DiferenciaXPagosNoDescargados` double NOT NULL,
  `DiferenciaXGlosasPendientesXConciliar` double NOT NULL,
  `DiferenciaXFacturasDevueltas` double NOT NULL,
  `DiferenciaXDiferenciaXImpuestos` double NOT NULL,
  `DiferenciaXFacturasNoRelacionadasXIPS` double NOT NULL,
  `DiferenciaXAjustesDeCartera` double NOT NULL,
  `DiferenciaXValorFacturado` double NOT NULL,
  `DiferenciaXGlosasPendientesXDescargarIPS` double NOT NULL,
  `DiferenciaVariada` double NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `NumeroFactura` (`NumeroFactura`),
  KEY `MesServicio` (`MesServicio`),
  KEY `NumeroRadicado` (`NumeroRadicado`),
  KEY `NumeroContrato` (`NumeroContrato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;