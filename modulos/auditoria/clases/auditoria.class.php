<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

if(file_exists("../../../general/clases/mail.class.php")){
    include_once("../../../general/clases/mail.class.php");
}

/* 
 * Clase donde se realizaran procesos para construir recetas
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class Auditoria extends conexion{
    
    function crear_hoja_trabajo_auditoria($ips_id,$hoja_trabajo_id,$tipo_negociacion,$Fecha,$Descripcion,$user_id) {
        $datos["ips_id"]=$ips_id;
        $datos["hoja_trabajo_id"]=$hoja_trabajo_id;
        $datos["tipo_negociacion"]=$tipo_negociacion;
        $datos["Fecha"]=$Fecha;
        $datos["Descripcion"]=$Descripcion;
        $datos["estado"]=1;
        $datos["user_id"]=$user_id;
        $sql=$this->getSQLInsert("auditoria_hojas_trabajo", $datos);
        $this->Query($sql);
    }
    
    function agregar_contrato_hoja_de_trabajo($hoja_trabajo_id,$contrato) {
        $sql="SELECT ID FROM auditoria_hojas_trabajo_contrato WHERE hoja_trabajo_id='$hoja_trabajo_id' AND contrato='$contrato'  ";
        
        $datos_validacion=$this->FetchAssoc($this->Query($sql));
        if($datos_validacion["ID"]>0){
            return(2);
        }
        $datos["hoja_trabajo_id"]=$hoja_trabajo_id;        
        $datos["contrato"]=$contrato;
        
        $sql=$this->getSQLInsert("auditoria_hojas_trabajo_contrato", $datos);
        $this->Query($sql);
        return(1);
    }
    
    public function registra_anexo($anexo_id,$ips_id,$Ruta,$Tamano,$NombreArchivo,$Extension,$idUser) {
        $Datos["ID"]=$anexo_id;
        $Datos["ips_id"]=$ips_id;
        $Datos["Ruta"]=$Ruta;
        $Datos["NombreArchivo"]=$NombreArchivo;
        $Datos["Extension"]=$Extension;
        $Datos["Created"]=date("Y-m-d H:i:s");
        $Datos["idUser"]=$idUser;        
        $Datos["Tamano"]=$Tamano;
        $sql= $this->getSQLInsert("auditoria_registra_anexos", $Datos);
        $this->Query($sql);
        
    }
    
    public function create_table_auditoria_hoja_de_trabajo_evento($db) {
        
        $table_name="auditoria_hoja_de_trabajo_evento";       
                        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name` (
            
            `hoja_trabajo_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
            
            `valor_facturado_aly` double NOT NULL,
            `valor_facturado_ts` double NOT NULL,
            `valor_facturado_diferencia` double NOT NULL,
            
            `retencion_impuestos_aly` double NOT NULL,
            `retencion_impuestos_ts` double NOT NULL,
            `retencion_impuestos_diferencia` double NOT NULL,
            
            `devoluciones_aly` double NOT NULL,
            `devoluciones_ts` double NOT NULL,
            `devoluciones_diferencia` double NOT NULL,
            
            `glosa_inicial_aly` double NOT NULL,
            `glosa_inicial_ts` double NOT NULL,
            `glosa_inicial_diferencia` double NOT NULL,
            
            `glosa_favor_aly` double NOT NULL,
            `glosa_favor_ts` double NOT NULL,
            `glosa_favor_diferencia` double NOT NULL,
            
            `glosa_conciliar_aly` double NOT NULL,
            `glosa_conciliar_ts` double NOT NULL,
            `glosa_conciliar_diferencia` double NOT NULL,
            
            `notas_copagos_aly` double NOT NULL,
            `notas_copagos_ts` double NOT NULL,
            `notas_copagos_diferencia` double NOT NULL,
            
            `recuperacion_impuestos_aly` double NOT NULL,
            `recuperacion_impuestos_ts` double NOT NULL,
            `recuperacion_impuestos_diferencia` double NOT NULL,
            
            `otros_descuentos_aly` double NOT NULL,
            `otros_descuentos_ts` double NOT NULL,
            `otros_descuentos_diferencia` double NOT NULL,
            
            `valor_pagado_aly` double NOT NULL,
            `valor_pagado_ts` double NOT NULL,
            `valor_pagado_diferencia` double NOT NULL,
            
            `valor_conciliaciones_aly` double NOT NULL,
            `valor_conciliaciones_ts` double NOT NULL,
            `valor_conciliaciones_diferencia` double NOT NULL,

            `saldo_aly` double NOT NULL,
            `saldo_ts` double NOT NULL,
            `saldo_diferencia` double NOT NULL,
            
            `updated_ts` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
                        
            
            KEY `contrato` (`contrato`),
            KEY `hoja_trabajo_id` (`hoja_trabajo_id`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `Sync` (`Sync`),
            KEY `updated_ts` (`updated_ts`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        return(1);
        
    }
    
    public function delete_table_auditoria_hoja_de_trabajo_evento($db) {
        
        $table_name="auditoria_hoja_de_trabajo_evento";
                
        $sql="DROP TABLE IF EXISTS `$table_name`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }


    public function create_tables_auditoria_anexo_evento($db) {
        
        $table_name="auditoria_anexo_aly_evento";
        $table_name_temp="auditoria_anexo_aly_evento_temp";
                        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT,    
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
            
            `valor_facturado` double NOT NULL,                        
            `retencion_impuestos` double NOT NULL,
            `devoluciones` double NOT NULL,                        
            `glosa_inicial` double NOT NULL,
            `glosa_favor` double NOT NULL,
            `notas_copagos` double NOT NULL, 
            `recuperacion_impuestos` double NOT NULL,                        
            `otros_descuentos` double NOT NULL,
            `valor_pagado` double NOT NULL,
            `saldo` double NOT NULL,           
            `copied_work_sheet` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
            
            PRIMARY KEY (`ID`),
            KEY `contrato` (`contrato`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `copied_work_sheet` (`copied_work_sheet`),
            KEY `Sync` (`Sync`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name_temp` (
            `ID` varchar(1) COLLATE utf8_spanish_ci NULL,    
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
            
            `valor_facturado` double NOT NULL,                        
            `retencion_impuestos` double NOT NULL,
            `devoluciones` double NOT NULL,                        
            `glosa_inicial` double NOT NULL,
            `glosa_favor` double NOT NULL,
            `notas_copagos` double NOT NULL, 
            `recuperacion_impuestos` double NOT NULL,                        
            `otros_descuentos` double NOT NULL,
            `valor_pagado` double NOT NULL,
            `saldo` double NOT NULL,           
            `copied_work_sheet` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
            
            
            KEY `contrato` (`contrato`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `copied_work_sheet` (`copied_work_sheet`),
            KEY `Sync` (`Sync`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        return(1);
        
    }
    
    public function delete_tables_auditoria_anexo_evento($db) {
        
        $table_name="auditoria_anexo_aly_evento";
        $table_name_temp="auditoria_anexo_aly_evento_temp";
        
        $sql="DROP TABLE IF EXISTS `$table_name`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="DROP TABLE IF EXISTS `$table_name_temp`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function copiar_anexo_evento_temporal($keyArchivo,$db,$idIPS,$idUser) {
        
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $sql="SELECT * FROM auditoria_registra_anexos WHERE ID='$keyArchivo'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$DatosUpload["Ruta"];
        
        if($DatosUpload["Extension"]=="xlsx"){
            $objReader = IOFactory::createReader('Xlsx');
        }else if($DatosUpload["Extension"]=="xls"){
            $objReader = IOFactory::createReader('Xls');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
        $table_temp="auditoria_anexo_aly_evento_temp"; 
        $this->VaciarTabla("$db.$table_temp");
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        $ColumnasTabla= $this->ShowColums($db.".$table_temp");
        
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            $nit_anexo=$objPHPExcel->getActiveSheet()->getCell('B4')->getCalculatedValue();
            if($nit_anexo<>$idIPS){
                exit('E1;<h3>El Anexo Enviado no corresponde al NIT: '.$idIPS.'</strong>, se envió el del NIT: '.$nit_anexo.'</h3>');
            }
            if($columnas<>'N'){
                exit('E1;<h3>No se recibió el archivo del <strong>Anexo De liquidacion evento, debería terminar en la columna N y termina en la Columna: '.$columnas.'</strong></h3>');
            }
            $sql= "INSERT INTO $db.`$table_temp` ( ";
            foreach ($ColumnasTabla["Field"] as $key => $value) {
                $sql.="`$value`,";
            }
            $sql=substr($sql, 0, -1);
            $sql.=") VALUES ";
            $r=0;
            
            $inicio=12;     
            
            for ($i=10;$i<=15;$i++){
                $FilaC=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                if(str_replace(" ", "", $FilaC)=='MESDESERVICIOS'){
                    $inicio=$i+1;
                    break;
                }
                
            }
            for ($i=$inicio;$i<=$filas;$i++){
                $FilaC=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();                
                              
                if(!is_numeric($FilaC) ){

                    continue; 

                }
                $c=0;  
                $r++;//Contador de filas a insertar
                $sql.="(";
                foreach ($ColumnasTabla["Field"] as $key => $value) {
                    if($value=='ID' or $value=="contrato"){
                        $sql.="'',";
                        continue;
                    }
                    
                    $c=$c+1;
                    $Dato=$objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    
                    $cell = $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i);
                    if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                        $Dato=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getValue());
                        $Dato=get_object_vars($Dato);
                        $Dato = $Dato["date"];

                    }
                        
                    
                    
                    if($value=="Sync"){
                        $Dato='0';
                    }
                    
                    
                    if($value=="FlagUpdate"){
                        $Dato="0";
                    }
                    $Dato= str_replace("'", "", $Dato);
                    $Dato= str_replace(";", ",", $Dato);
                    $sql.="'$Dato',";
                    
                }
                $sql=substr($sql, 0, -1);
                $sql.="),";
                if($r==1000){
                
                    $sql=substr($sql, 0, -1);
                    
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`$table_temp` ( ";
                    foreach ($ColumnasTabla["Field"] as $key => $value) {
                        $sql.="`$value`,";
                    }
                    $sql=substr($sql, 0, -1);
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        }
        
        
        $sql=substr($sql, 0, -1);
        //print($sql);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($Dato);
        unset($objFecha);
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
    }
    
    
    public function create_table_auditoria_hoja_de_trabajo_pgp($db) {
        
        $table_name="auditoria_hoja_de_trabajo_pgp";       
                        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name` (
            
            `hoja_trabajo_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
            
            `valor_facturado_aly` double NOT NULL,
            `valor_facturado_ts` double NOT NULL,
            `valor_facturado_diferencia` double NOT NULL,
            
            `retencion_impuestos_aly` double NOT NULL,
            `retencion_impuestos_ts` double NOT NULL,
            `retencion_impuestos_diferencia` double NOT NULL,
            
            `glosa_inicial_aly` double NOT NULL,
            `glosa_inicial_ts` double NOT NULL,
            `glosa_inicial_diferencia` double NOT NULL,
            
            `glosa_favor_aly` double NOT NULL,
            `glosa_favor_ts` double NOT NULL,
            `glosa_favor_diferencia` double NOT NULL,
            
            `glosa_conciliar_aly` double NOT NULL,
            `glosa_conciliar_ts` double NOT NULL,
            `glosa_conciliar_diferencia` double NOT NULL,
            
            `notas_copagos_aly` double NOT NULL,
            `notas_copagos_ts` double NOT NULL,
            `notas_copagos_diferencia` double NOT NULL,
                        
            `descuentos_pgp_aly` double NOT NULL,
            `descuentos_pgp_ts` double NOT NULL,
            `descuentos_pgp_diferencia` double NOT NULL,
            
            `valor_pagado_aly` double NOT NULL,
            `valor_pagado_ts` double NOT NULL,
            `valor_pagado_diferencia` double NOT NULL,
            
            `valor_conciliaciones_aly` double NOT NULL,
            `valor_conciliaciones_ts` double NOT NULL,
            `valor_conciliaciones_diferencia` double NOT NULL,

            `saldo_aly` double NOT NULL,
            `saldo_ts` double NOT NULL,
            `saldo_diferencia` double NOT NULL,
            
            `updated_ts` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
                        
            
            KEY `contrato` (`contrato`),
            KEY `hoja_trabajo_id` (`hoja_trabajo_id`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `Sync` (`Sync`),
            KEY `updated_ts` (`updated_ts`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        return(1);
        
    }
    
    public function create_tables_auditoria_anexo_pgp($db) {
        
        $table_name="auditoria_anexo_aly_pgp";
        $table_name_temp="auditoria_anexo_aly_pgp_temp";
                        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT,    
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
            
            `valor_facturado` double NOT NULL,                        
            `retencion_impuestos` double NOT NULL,                                 
            `glosa_inicial` double NOT NULL,
            `glosa_favor` double NOT NULL,
            `notas_copagos` double NOT NULL,                                 
            `descuento_pgp` double NOT NULL,
            `valor_pagado` double NOT NULL,
            `saldo` double NOT NULL,           
            `copied_work_sheet` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
            
            PRIMARY KEY (`ID`),
            KEY `contrato` (`contrato`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `copied_work_sheet` (`copied_work_sheet`),
            KEY `Sync` (`Sync`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name_temp` (
            `ID` varchar(1) COLLATE utf8_spanish_ci NULL,    
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
            
            `valor_facturado` double NOT NULL,                        
            `retencion_impuestos` double NOT NULL,                                 
            `glosa_inicial` double NOT NULL,
            `glosa_favor` double NOT NULL,
            `notas_copagos` double NOT NULL,                                 
            `descuento_pgp` double NOT NULL,
            `valor_pagado` double NOT NULL,
            `saldo` double NOT NULL,           
            `copied_work_sheet` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
            
            
            KEY `contrato` (`contrato`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `copied_work_sheet` (`copied_work_sheet`),
            KEY `Sync` (`Sync`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        return(1);
        
    }
    
    public function copiar_anexo_pgp_temporal($keyArchivo,$db,$idIPS,$idUser) {
        
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $sql="SELECT * FROM auditoria_registra_anexos WHERE ID='$keyArchivo'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$DatosUpload["Ruta"];
        
        if($DatosUpload["Extension"]=="xlsx"){
            $objReader = IOFactory::createReader('Xlsx');
        }else if($DatosUpload["Extension"]=="xls"){
            $objReader = IOFactory::createReader('Xls');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
        $table_temp="auditoria_anexo_aly_pgp_temp"; 
        $this->VaciarTabla("$db.$table_temp");
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        $ColumnasTabla= $this->ShowColums($db.".$table_temp");
        
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            $nit_anexo=$objPHPExcel->getActiveSheet()->getCell('B4')->getCalculatedValue();
            if($nit_anexo<>$idIPS){
                exit('E1;<h3>El Anexo Enviado no corresponde al NIT: '.$idIPS.'</strong>, se envió el del NIT: '.$nit_anexo.'</h3>');
            }
            if($columnas<>'L'){
                exit('E1;<h3>No se recibió el archivo del <strong>Anexo De liquidacion PGP, debería terminar en la columna L y termina en la Columna: '.$columnas.'</strong></h3>');
            }
            $sql= "INSERT INTO $db.`$table_temp` ( ";
            foreach ($ColumnasTabla["Field"] as $key => $value) {
                $sql.="`$value`,";
            }
            $sql=substr($sql, 0, -1);
            $sql.=") VALUES ";
            $r=0;
            
            $inicio=12;     
            
            for ($i=10;$i<=15;$i++){
                $FilaC=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                if(str_replace(" ", "", $FilaC)=='MESDESERVICIOS'){
                    $inicio=$i+1;
                    break;
                }
                
            }
            for ($i=$inicio;$i<=$filas;$i++){
                $FilaC=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();                
                              
                if(!is_numeric($FilaC) ){

                    continue; 

                }
                $c=0;  
                $r++;//Contador de filas a insertar
                $sql.="(";
                foreach ($ColumnasTabla["Field"] as $key => $value) {
                    if($value=='ID' or $value=="contrato"){
                        $sql.="'',";
                        continue;
                    }
                    
                    $c=$c+1;
                    $Dato=$objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    
                    $cell = $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i);
                    if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                        $Dato=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getValue());
                        $Dato=get_object_vars($Dato);
                        $Dato = $Dato["date"];

                    }
                        
                    
                    
                    if($value=="Sync"){
                        $Dato='0';
                    }
                    
                    
                    if($value=="FlagUpdate"){
                        $Dato="0";
                    }
                    $Dato= str_replace("'", "", $Dato);
                    $Dato= str_replace(";", ",", $Dato);
                    $sql.="'$Dato',";
                    
                }
                $sql=substr($sql, 0, -1);
                $sql.="),";
                if($r==1000){
                
                    $sql=substr($sql, 0, -1);
                    
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`$table_temp` ( ";
                    foreach ($ColumnasTabla["Field"] as $key => $value) {
                        $sql.="`$value`,";
                    }
                    $sql=substr($sql, 0, -1);
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        }
        
        
        $sql=substr($sql, 0, -1);
        //print($sql);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($Dato);
       
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
    }
    
    public function insertar_registros_hoja_trabajo_evento($db,$hoja_trabajo_id,$condition_limit,$fecha_actualizacion) {
        $sql="INSERT INTO $db.auditoria_hoja_de_trabajo_evento 
                (hoja_trabajo_id,departamento_radicacion,contrato,radicado,mes_servicio,factura,valor_facturado_aly,retencion_impuestos_aly,devoluciones_aly,glosa_inicial_aly,glosa_favor_aly,glosa_conciliar_aly,notas_copagos_aly,recuperacion_impuestos_aly,otros_descuentos_aly,valor_pagado_aly,saldo_aly)

                SELECT '$hoja_trabajo_id',t1.departamento_radicacion,t1.contrato,t1.radicado,t1.mes_servicio,t1.factura,t1.valor_facturado,t1.retencion_impuestos,t1.devoluciones,t1.glosa_inicial,t1.glosa_favor,'0',t1.notas_copagos,t1.recuperacion_impuestos,t1.otros_descuentos,t1.valor_pagado,t1.saldo
                FROM $db.auditoria_anexo_aly_evento t1

                WHERE EXISTS (SELECT 1 FROM auditoria_hojas_trabajo_contrato t2 WHERE t2.contrato like t1.contrato and t2.hoja_trabajo_id='$hoja_trabajo_id')
                AND t1.copied_work_sheet='0000-00-00 00:00:00' or t1.copied_work_sheet IS NULL $condition_limit


                ";
        $this->Query($sql);
        $sql="UPDATE $db.auditoria_anexo_aly_evento t1 SET t1.copied_work_sheet='$fecha_actualizacion' WHERE EXISTS (SELECT 1 FROM auditoria_hojas_trabajo_contrato t2 WHERE t2.contrato like t1.contrato and t2.hoja_trabajo_id='$hoja_trabajo_id')

                AND t1.copied_work_sheet='0000-00-00 00:00:00' or t1.copied_work_sheet IS NULL $condition_limit 

                ";
        $this->Query($sql);
    }
    
    public function insertar_registros_hoja_trabajo_pgp($db,$hoja_trabajo_id,$condition_limit,$fecha_actualizacion) {
        $sql="INSERT INTO $db.auditoria_hoja_de_trabajo_pgp 
                (hoja_trabajo_id,departamento_radicacion,contrato,radicado,mes_servicio,factura,valor_facturado_aly,retencion_impuestos_aly,glosa_inicial_aly,glosa_favor_aly,glosa_conciliar_aly,notas_copagos_aly,descuentos_pgp_aly,valor_pagado_aly,saldo_aly)

                SELECT '$hoja_trabajo_id',t1.departamento_radicacion,t1.contrato,t1.radicado,t1.mes_servicio,t1.factura,t1.valor_facturado,t1.retencion_impuestos,t1.glosa_inicial,t1.glosa_favor,'0',t1.notas_copagos,t1.descuento_pgp,t1.valor_pagado,t1.saldo
                FROM $db.auditoria_anexo_aly_pgp t1

                WHERE EXISTS (SELECT 1 FROM auditoria_hojas_trabajo_contrato t2 WHERE t2.contrato like t1.contrato and t2.hoja_trabajo_id='$hoja_trabajo_id')
                AND t1.copied_work_sheet='0000-00-00 00:00:00' or t1.copied_work_sheet IS NULL $condition_limit


                ";
        $this->Query($sql);
        $sql="UPDATE $db.auditoria_anexo_aly_pgp t1 SET t1.copied_work_sheet='$fecha_actualizacion' WHERE EXISTS (SELECT 1 FROM auditoria_hojas_trabajo_contrato t2 WHERE t2.contrato like t1.contrato and t2.hoja_trabajo_id='$hoja_trabajo_id')

                AND t1.copied_work_sheet='0000-00-00 00:00:00' or t1.copied_work_sheet IS NULL $condition_limit 

                ";
        $this->Query($sql);
    }
    
    public function actualizar_hoja_trabajo_evento($db,$hoja_trabajo_id,$fecha_actualizacion,$condition_limit) {
        $sql="UPDATE $db.auditoria_hoja_de_trabajo_evento t1
                        SET t1.valor_facturado_ts=(SELECT ValorOriginal FROM $db.historial_carteracargada_eps t2 WHERE EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Aplicacion='FACTURA' AND t3.TipoOperacion=t2.TipoOperacion ) AND t2.NumeroFactura=t1.factura AND t2.NumeroRadicado=t1.radicado ORDER BY ID DESC LIMIT 1 ),
                            t1.valor_facturado_diferencia=t1.valor_facturado_aly-t1.valor_facturado_ts,
                            
                            t1.retencion_impuestos_ts=(SELECT (SUM(ValorCredito)-SUM(ValorDebito) ) FROM $db.retenciones t2 WHERE t2.Cuentacontable like '2365%' AND t2.NumeroFactura=t1.factura  ),
                            t1.retencion_impuestos_diferencia=t1.retencion_impuestos_aly-t1.retencion_impuestos_ts,
                            
                            t1.devoluciones_ts=
                            (SELECT IF (  
                            (SELECT COUNT(DISTINCT NumeroTransaccion) FROM $db.notas_db_cr_2 t2 WHERE t2.NumeroFactura=t1.factura AND t2.C13<>'N' AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE Estado=1 AND t2.TipoOperacion=t3.TipoOperacion AND t3.Aplicacion='devoluciones') )
                            >=
                            (SELECT COUNT(DISTINCT NumeroRadicado) FROM $db.historial_carteracargada_eps t2 WHERE t2.NumeroFactura=t1.factura  AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.TipoOperacion=t3.TipoOperacion AND t3.Aplicacion='FACTURA')  )
                            ,t1.valor_facturado_ts,0 ) ),
                            t1.devoluciones_diferencia=t1.devoluciones_aly-t1.devoluciones_ts,
                            
                            t1.glosa_inicial_ts=(SELECT (ValorTotalGlosa) FROM $db.glosaseps_asmet t2  WHERE t2.NumeroFactura=t1.factura ORDER BY t2.FechaRegistro DESC LIMIT 1),
                            t1.glosa_inicial_diferencia=t1.glosa_inicial_aly-t1.glosa_inicial_ts,
                            
                            t1.glosa_favor_ts=(SELECT (ValorGlosaFavor) FROM $db.glosaseps_asmet t2  WHERE t2.NumeroFactura=t1.factura ORDER BY t2.FechaRegistro DESC LIMIT 1),
                            t1.glosa_favor_diferencia=t1.glosa_favor_aly-t1.glosa_favor_ts, 
                            
                            t1.glosa_conciliar_ts=(SELECT (ValorPendienteResolver) FROM $db.glosaseps_asmet t2  WHERE t2.NumeroFactura=t1.factura ORDER BY t2.FechaRegistro DESC LIMIT 1),
                            t1.glosa_conciliar_diferencia=t1.glosa_conciliar_aly-t1.glosa_conciliar_ts,
                            
                            t1.notas_copagos_ts=
                            (SELECT IF(
                            (SELECT SUM(ABS(ValorTotal)) as total_copagos_notas FROM $db.notas_db_cr_2 t2 WHERE EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.TipoOperacion=t2.TipoOperacion AND t3.Aplicacion='copagos') AND  t2.NumeroFactura=t1.factura)  
                            >
                            (SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='copagos')  )
                            ,
                            (SELECT SUM(ABS(ValorTotal)) as total_copagos_notas FROM $db.notas_db_cr_2 t2 WHERE EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.TipoOperacion=t2.TipoOperacion AND t3.Aplicacion='copagos') AND  t2.NumeroFactura=t1.factura)
                            ,
                            (SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='copagos')  )
                            ) ),
                            t1.notas_copagos_diferencia=t1.notas_copagos_aly-t1.notas_copagos_ts,
                            
                            t1.recuperacion_impuestos_ts=(IF(t1.devoluciones_ts>0 AND t1.retencion_impuestos_ts>0,t1.retencion_impuestos_ts,0)),
                            t1.recuperacion_impuestos_diferencia=t1.recuperacion_impuestos_aly-t1.recuperacion_impuestos_ts,
                            
                            t1.otros_descuentos_ts=(SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND (t3.Aplicacion='otrosdescuentos' or t3.Aplicacion='ajustescartera' )) ),
                            t1.otros_descuentos_diferencia=t1.otros_descuentos_aly-t1.otros_descuentos_ts,
                            
                            t1.valor_pagado_ts=
                            (SELECT IFNULL((SELECT SUM(ValorPago) FROM $db.notas_db_cr_2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.TipoOperacion2=t3.TipoOperacion AND t3.Aplicacion='TotalPagos')  AND (t2.TipoOperacion!='2103' and t2.TipoOperacion!='2117' and t2.TipoOperacion!='2351' and t2.TipoOperacion!='2122')),0))
                            +
                            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='Capitalizacion')),0))
                            +
                            (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM $db.conciliaciones_cruces t2 WHERE t2.NumeroFactura=t1.factura AND t2.ConciliacionAFavorDe=1),0))
                            +
                            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='anticipos') ),0))
                            ,
                            t1.valor_pagado_diferencia=t1.valor_pagado_aly-t1.valor_pagado_ts,
                            
                            t1.saldo_ts=(t1.valor_facturado_ts-t1.retencion_impuestos_ts-t1.devoluciones_ts-t1.glosa_favor_ts-t1.glosa_conciliar_ts-t1.notas_copagos_ts-t1.recuperacion_impuestos_ts-t1.otros_descuentos_ts-t1.valor_pagado_ts),
                            t1.saldo_diferencia=t1.saldo_aly-t1.saldo_ts 
                            
                        WHERE hoja_trabajo_id='$hoja_trabajo_id' AND  (t1.updated_ts='0000-00-00 00:00:00' or t1.updated_ts IS NULL) $condition_limit
                        

                        ";
                $this->Query($sql);
                $sql="UPDATE $db.auditoria_hoja_de_trabajo_evento SET updated_ts='$fecha_actualizacion' WHERE hoja_trabajo_id='$hoja_trabajo_id' AND  (updated_ts='0000-00-00 00:00:00' or updated_ts IS NULL) $condition_limit";
                $this->Query($sql);
    }
    
    public function actualizar_hoja_trabajo_pgp($db,$hoja_trabajo_id,$fecha_actualizacion,$condition_limit) {
        $sql="UPDATE $db.auditoria_hoja_de_trabajo_pgp t1
                        SET t1.valor_facturado_ts=(SELECT ValorOriginal FROM $db.historial_carteracargada_eps t2 WHERE EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Aplicacion='FACTURA' AND t3.TipoOperacion=t2.TipoOperacion ) AND t2.NumeroFactura=t1.factura AND t2.NumeroRadicado=t1.radicado ORDER BY ID DESC LIMIT 1 ),
                            t1.valor_facturado_diferencia=t1.valor_facturado_aly-t1.valor_facturado_ts,
                            
                            t1.retencion_impuestos_ts=(SELECT (SUM(ValorCredito)-SUM(ValorDebito) ) FROM $db.retenciones t2 WHERE t2.Cuentacontable like '2365%' AND t2.NumeroFactura=t1.factura  ),
                            t1.retencion_impuestos_diferencia=t1.retencion_impuestos_aly-t1.retencion_impuestos_ts,
                            
                            t1.glosa_inicial_ts=(SELECT (ValorTotalGlosa) FROM $db.glosaseps_asmet t2  WHERE t2.NumeroFactura=t1.factura ORDER BY t2.FechaRegistro DESC LIMIT 1),
                            t1.glosa_inicial_diferencia=t1.glosa_inicial_aly-t1.glosa_inicial_ts,
                            
                            t1.glosa_favor_ts=(SELECT (ValorGlosaFavor) FROM $db.glosaseps_asmet t2  WHERE t2.NumeroFactura=t1.factura ORDER BY t2.FechaRegistro DESC LIMIT 1),
                            t1.glosa_favor_diferencia=t1.glosa_favor_aly-t1.glosa_favor_ts, 
                            
                            t1.glosa_conciliar_ts=(SELECT (ValorPendienteResolver) FROM $db.glosaseps_asmet t2  WHERE t2.NumeroFactura=t1.factura ORDER BY t2.FechaRegistro DESC LIMIT 1),
                            t1.glosa_conciliar_diferencia=t1.glosa_conciliar_aly-t1.glosa_conciliar_ts,
                            
                            t1.notas_copagos_ts=
                            (SELECT IF(
                            (SELECT SUM(ABS(ValorTotal)) as total_copagos_notas FROM $db.notas_db_cr_2 t2 WHERE EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.TipoOperacion=t2.TipoOperacion AND t3.Aplicacion='copagos') AND  t2.NumeroFactura=t1.factura)  
                            >
                            (SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='copagos')  )
                            ,
                            (SELECT SUM(ABS(ValorTotal)) as total_copagos_notas FROM $db.notas_db_cr_2 t2 WHERE EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.TipoOperacion=t2.TipoOperacion AND t3.Aplicacion='copagos') AND  t2.NumeroFactura=t1.factura)
                            ,
                            (SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='copagos')  )
                            ) ),
                            t1.notas_copagos_diferencia=t1.notas_copagos_aly-t1.notas_copagos_ts,
                            
                            t1.descuentos_pgp_ts=(SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND (t3.Aplicacion='pgp' or t3.Aplicacion='otrosdescuentos' or t3.Aplicacion='ajustescartera' )) ),
                            t1.descuentos_pgp_diferencia=t1.descuentos_pgp_aly-t1.descuentos_pgp_ts,
                            
                            t1.valor_pagado_ts=
                            (SELECT IFNULL((SELECT SUM(ValorPago) FROM $db.notas_db_cr_2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.TipoOperacion2=t3.TipoOperacion AND t3.Aplicacion='TotalPagos')  AND (t2.TipoOperacion!='2103' and t2.TipoOperacion!='2117' and t2.TipoOperacion!='2351' and t2.TipoOperacion!='2122')),0))
                            +
                            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='Capitalizacion')),0))
                            +
                            (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM $db.conciliaciones_cruces t2 WHERE t2.NumeroFactura=t1.factura AND t2.ConciliacionAFavorDe=1),0))
                            +
                            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t2 WHERE t2.NumeroFactura=t1.factura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t3.Estado=1 AND t2.NumeroInterno=t3.TipoOperacion AND t3.Aplicacion='anticipos') ),0))
                            ,
                            t1.valor_pagado_diferencia=t1.valor_pagado_aly-t1.valor_pagado_ts,
                            
                            t1.saldo_ts=(t1.valor_facturado_ts-t1.retencion_impuestos_ts-t1.glosa_favor_ts-t1.glosa_conciliar_ts-t1.notas_copagos_ts-t1.descuentos_pgp_ts-t1.valor_pagado_ts),
                            t1.saldo_diferencia=t1.saldo_aly-t1.saldo_ts 
                            
                        WHERE hoja_trabajo_id='$hoja_trabajo_id' AND  (t1.updated_ts='0000-00-00 00:00:00' or t1.updated_ts IS NULL) $condition_limit
                        

                        ";
                $this->Query($sql);
                $sql="UPDATE $db.auditoria_hoja_de_trabajo_pgp SET updated_ts='$fecha_actualizacion' WHERE hoja_trabajo_id='$hoja_trabajo_id' AND  (updated_ts='0000-00-00 00:00:00' or updated_ts IS NULL) $condition_limit";
                $this->Query($sql);
    }
    
    
    /*
     * CAPITA
     */
    
    public function create_tables_auditoria_anexo_capita($db) {
        
        $table_name="auditoria_anexo_aly_capita";
        $table_name_temp="auditoria_anexo_aly_capita_temp";
                        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT,    
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `municipio` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `dias_lma` int(11) NOT NULL,
            `valor_segun_lma` double NOT NULL,
            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,            
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,            
            `valor_facturado` double NOT NULL,                        
            `retencion_impuestos` double NOT NULL,                                 
            `descuento_reconocimiento_bdua` double NOT NULL,
            `descuento_inicial` double NOT NULL,
            `descuento_conciliado_asmet` double NOT NULL,                                 
            
            `valor_pagado` double NOT NULL,
            `saldo` double NOT NULL,           
            `copied_work_sheet` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
            
            PRIMARY KEY (`ID`),
            KEY `contrato` (`contrato`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `municipio` (`municipio`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `copied_work_sheet` (`copied_work_sheet`),
            KEY `Sync` (`Sync`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="CREATE TABLE IF NOT EXISTS `$table_name_temp` (
            `ID` varchar(1) COLLATE utf8_spanish_ci NULL,    
            `contrato` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `departamento_radicacion` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
            `municipio` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
            `mes_servicio` int(6) NOT NULL,
            `dias_lma` int(11) NOT NULL,
            `valor_segun_lma` double NOT NULL,
            
            `radicado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,            
            `factura` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,            
            `valor_facturado` double NOT NULL,                        
            `retencion_impuestos` double NOT NULL,                                 
            `descuento_reconocimiento_bdua` double NOT NULL,
            `descuento_inicial` double NOT NULL,
            `descuento_conciliado_asmet` double NOT NULL,                                 
            
            `valor_pagado` double NOT NULL,
            `saldo` double NOT NULL,           
            `copied_work_sheet` datetime DEFAULT NULL,
            `Sync` datetime DEFAULT NULL,
            
            
            KEY `contrato` (`contrato`),
            KEY `departamento_radicacion` (`departamento_radicacion`),
            KEY `municipio` (`municipio`),
            KEY `radicado` (`radicado`),
            KEY `mes_servicio` (`mes_servicio`),
            KEY `copied_work_sheet` (`copied_work_sheet`),
            KEY `Sync` (`Sync`),
            KEY `factura` (`factura`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        return(1);
        
    }
    
    public function copiar_anexo_capita_temporal($keyArchivo,$db,$idIPS,$idUser) {
        
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $sql="SELECT * FROM auditoria_registra_anexos WHERE ID='$keyArchivo'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$DatosUpload["Ruta"];
        
        if($DatosUpload["Extension"]=="xlsx"){
            $objReader = IOFactory::createReader('Xlsx');
        }else if($DatosUpload["Extension"]=="xls"){
            $objReader = IOFactory::createReader('Xls');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
        $table_temp="auditoria_anexo_aly_capita_temp"; 
        $this->VaciarTabla("$db.$table_temp");
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        $ColumnasTabla= $this->ShowColums($db.".$table_temp");
        
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            $nit_anexo=$objPHPExcel->getActiveSheet()->getCell('B4')->getCalculatedValue();
            if($nit_anexo<>$idIPS){
                exit('E1;<h3>El Anexo Enviado no corresponde al NIT: '.$idIPS.'</strong>, se envió el del NIT: '.$nit_anexo.'</h3>');
            }
            if($columnas<>'N'){
                exit('E1;<h3>No se recibió el archivo del <strong>Anexo De liquidacion evento, debería terminar en la columna N y termina en la Columna: '.$columnas.'</strong></h3>');
            }
            $sql= "INSERT INTO $db.`$table_temp` ( ";
            foreach ($ColumnasTabla["Field"] as $key => $value) {
                $sql.="`$value`,";
            }
            $sql=substr($sql, 0, -1);
            $sql.=") VALUES ";
            $r=0;
            
            $inicio=12;     
            
            for ($i=10;$i<=15;$i++){
                $FilaC=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                if(substr(str_replace(" ", "", $FilaC),0,6)=='MESLMA' ){
                    $inicio=$i+1;
                    break;
                }
                
            }
            for ($i=$inicio;$i<=$filas;$i++){
                $FilaC=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();                
                              
                if(!is_numeric($FilaC) ){

                    continue; 

                }
                $c=0;  
                $r++;//Contador de filas a insertar
                $sql.="(";
                foreach ($ColumnasTabla["Field"] as $key => $value) {
                    if($value=='ID' or $value=="contrato"){
                        $sql.="'',";
                        continue;
                    }
                    
                    $c=$c+1;
                    $Dato=$objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    
                    $cell = $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i);
                    if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                        $Dato=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getValue());
                        $Dato=get_object_vars($Dato);
                        $Dato = $Dato["date"];

                    }
                        
                    
                    
                    if($value=="Sync"){
                        $Dato='0';
                    }
                    
                    
                    if($value=="FlagUpdate"){
                        $Dato="0";
                    }
                    $Dato= str_replace("'", "", $Dato);
                    $Dato= str_replace(";", ",", $Dato);
                    $sql.="'$Dato',";
                    
                }
                $sql=substr($sql, 0, -1);
                $sql.="),";
                if($r==1000){
                
                    $sql=substr($sql, 0, -1);
                    
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`$table_temp` ( ";
                    foreach ($ColumnasTabla["Field"] as $key => $value) {
                        $sql.="`$value`,";
                    }
                    $sql=substr($sql, 0, -1);
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        }
        
        
        $sql=substr($sql, 0, -1);
        //print($sql);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($Dato);
        
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
    }
    
    //Fin Clases
}