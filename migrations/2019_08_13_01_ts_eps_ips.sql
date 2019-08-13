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