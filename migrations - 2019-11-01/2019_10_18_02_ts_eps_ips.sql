ALTER TABLE `conciliaciones_cruces` CHANGE `ConceptoConciliacion` `ConceptoConciliacion` INT NOT NULL;
ALTER TABLE `conciliaciones_cruces` ADD INDEX(`ConceptoConciliacion`);