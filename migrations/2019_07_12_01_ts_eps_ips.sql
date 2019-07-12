CREATE TABLE `pendientes_de_envio` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TablaOrigen` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `NumeroRadicado` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `Valor` double NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `TablaOrigen` (`TablaOrigen`),
  KEY `NumeroRadicado` (`NumeroRadicado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;