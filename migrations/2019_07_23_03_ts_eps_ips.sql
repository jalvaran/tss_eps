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